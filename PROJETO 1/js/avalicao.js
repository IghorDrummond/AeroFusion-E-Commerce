// Declaracção de variáveis
// Elementos
const descricao = document.getElementsByClassName('descricao')[0];
const titulo = document.getElementsByName('titulo')[0];
const quantidade = document.getElementById('quant-carac');
const inputimagens = document.getElementById('inputGroupFile01');
const imagePreview = document.getElementsByClassName('imagens');
const deletar = document.getElementsByClassName('deletar');
const estrelas = document.getElementsByClassName('stars');
//Array
const imagens = [null, null, null];
// Constantes
const maxCaracteres = 1000;
//numero
var quantStars = 0;

//------------------Eventos
/*
Evento: input
Descrição: Atualiza a quantidade de caracteres restantes na descrição
Data: 17/08/2024
Programador: Ighor Drummond   
*/
descricao.addEventListener('input', () => {
    let diferenca = maxCaracteres - descricao.value.length;
    quantidade.textContent = "Restam " + diferenca.toString() + " caracteres disponíveis";

    if(diferenca <= 100){
        quantidade.classList.remove('text-warning');
        quantidade.classList.add('text-danger');
    }else{
        quantidade.classList.remove('text-danger');
        quantidade.classList.add('text-warning');       
    }
});
/*
Evento: change
Descrição: Mostra uma mensagem de alerta e informações sobre o arquivo selecionado
Data: 17/08/2024
Programador: Ighor Drummond   
*/
inputimagens.addEventListener('change', (event) => {
    // Verifica se há arquivos selecionados
    if (inputimagens.files.length > 0) {
        var file = inputimagens.files[0]; // Pega o primeiro arquivo
        // Cria um novo FileReader
        var reader = new FileReader();

        // Define o que fazer quando o arquivo for carregado
        reader.onload = function(e) {
            let Posic = imagens.indexOf(null);

            if(Posic >= 0){
                // Atualiza o atributo src da tag img com a URL da imagem
                imagePreview[Posic].src = e.target.result;
                imagens[Posic] = inputimagens.files[0];
                deletar[Posic].classList.remove('d-none');
                deletar[Posic].classList.add('d-block');
            }
        }
        // Lê o arquivo como uma URL de dados
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
        element.classList.remove('d-block');
        element.classList.add('d-none');
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
                estrelas[nCont].classList.remove('fa-regular');
                estrelas[nCont].classList.add('fa-solid');
            }else{
                estrelas[nCont].classList.remove('fa-solid');
                estrelas[nCont].classList.add('fa-regular');                
            }
        }
        quantStars = index;
    });
});
