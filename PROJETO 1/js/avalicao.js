// Declaracção de variáveis
// Elementos
var descricao = document.getElementsByClassName('descricao')[0];
var titulo = document.getElementsByName('titulo')[0];
var quantidade = document.getElementById('quant-carac');
var inputimagens = document.getElementById('inputGroupFile01');
var imagePreview = document.getElementsByClassName('imagens');
var deletar = document.getElementsByClassName('deletar');
var estrelas = document.getElementsByClassName('stars');
var avaliacao = document.getElementById('avaliacao');
//Array
var imagens = [null, null, null];
//numero
var quantStars = 1;

//------------------Eventos
/*
Evento: input
Descrição: Atualiza a quantidade de caracteres restantes na descrição
Data: 17/08/2024
Programador: Ighor Drummond   
*/
descricao.addEventListener('input', () => {
    const maxCaracteres = 1000;
    let diferenca = maxCaracteres - descricao.value.length;
    quantidade.textContent = "Restam " + diferenca.toString() + " caracteres disponíveis";

    if(diferenca <= 100){
        quantidade.classList.replace('text-warning', 'text-danger');
    }else{ 
        quantidade.classList.replace('text-danger', 'text-warning');    
    }
});
/*
Evento: change
Descrição: Mostra uma mensagem de alerta e informações sobre o arquivo selecionado
Data: 17/08/2024
Programador: Ighor Drummond   
*/
inputimagens.addEventListener('change', () => {
    if (inputimagens.files.length > 0) {
        const file = inputimagens.files[0];

        if (file.size > tamLimite) { // Verifica se o arquivo é maior que 500KB
            alert('A imagem não pode ser maior que 500KB', 0);
            return;
        }

        const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!validTypes.includes(file.type)) {
            alert('Formato de imagem inválido. Apenas JPG, PNG e GIF são permitidos.');
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const posic = imagens.indexOf(null);
            if (posic >= 0) {
                imagePreview[posic].src = e.target.result;
                imagens[posic] = file;
                deletar[posic].classList.replace('d-none', 'd-block');
            }
        }
        reader.readAsDataURL(file);
    }
});
/*
Evento: click
Descrição: Deleta as imagens da avaliação
Data: 17/08/2024
Programador: Ighor Drummond   
*/
Array.from(deletar).forEach((element, index) => {
    element.addEventListener('click', () => {
        imagePreview[index].src = "img/inserir_img.jpg";
        imagens[index] = null;
        element.classList.replace('d-block', 'd-none');
    });
});
/*
Evento: mouseover
Descrição: seleciona a quantidade de estrelas
Data: 17/08/2024
Programador: Ighor Drummond   
*/
Array.from(estrelas).forEach((element, index) => {
    element.addEventListener('mouseover', () => {
        for(nCont = 0; nCont <= 4; nCont++){
            if(nCont <= index){
                estrelas[nCont].classList.replace('fa-regular', 'fa-solid');
            }else{
                estrelas[nCont].classList.replace('fa-solid', 'fa-regular');            
            }
        }
        quantStars = index + 1;
    });
});
/*
Evento: submit
Descrição: Envia os dados para o banco de dados
Data: 17/08/2024
Programador: Ighor Drummond   
*/
avaliacao.addEventListener('submit', (event)=>{
    const tamLimite = 500 * 1024;
    //Impede de atualizar a página após o submit
    event.preventDefault();

    //Remove a janela
    $('.end_body').remove();

    // Verificar se o usuário selecionou entre 1 e 3 imagens
    const imagensSelecionadas = imagens.filter(img => img !== null).length;
    if (imagensSelecionadas < 1 || imagensSelecionadas > 3) {
        alert('Você deve enviar entre 1 e 3 imagens.');
        return;
    }

    // Cria um FormData para enviar dados e arquivos
    const formData = new FormData();
    formData.append('titulo', titulo.value);//Envia o titulo
    formData.append('descricao', descricao.value);//Envia a descrição
    formData.append('quantidadeEstrelas', quantStars);//Envia a quantidade de estrelas

    // Adiciona as imagens ao FormData
    imagens.forEach((imagem, index) => {
        if (imagem) {
            formData.append(`imagem${index + 1}`, imagem);
        }
    });

    // Enviar os dados usando $.ajax()
    $.ajax({
        url: 'script/avaliacao.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false, 
        success: function(response) {
            alerta('Dados enviados com sucesso!', 1);
            console.log(response);
        },
        error: function(xhr, status, error) {
            alerta('Ocorreu um erro ao enviar os dados: ' + error, 0);
        }
    });
});

function fecharAba(){
    //Remove a janela
    $('.end_body').remove();
    descricao = null; 
    titulo = null; 
    quantidade = null; 
    inputimagens = null; 
    imagePreview = null; 
    deletar = null; 
    estrelas = null; 
    avaliacao = null; 
    imagens = null;
}