//Declaração de Variaveis
var ListaItem = document?.getElementsByClassName('itens');
var Artigos = document?.getElementsByTagName('article');
//Numero

//--------------------------Eventos
document.addEventListener('DOMContentLoaded', function() {
    $('section').load('script/home_config.js?Opc=1', ()=>{
        let botoes = document.querySelectorAll('button[onclick^="ativarItens"]');
        botoes.forEach(function(botao) {
            let icone = botao.querySelector('i');
            if (icone) {
                icone.addEventListener('click', function(event) {
                    event.stopPropagation(); // Evita que o clique no ícone interfira no botão
                    botao.click(); // Dispara o evento de clique do botão
                });
            }
        });
    });
});
/*
* Evento: submit
* Descrição: Envia a nova imagem ao servidor após usuário inserir a imagem desejada
* Programador(a): Ighor Drummond
* Data: 31/07/2024
*/
$('#FormularioImagem').on('submit', function(e){
    e.preventDefault();

    let formData = new FormData();
    let arquivo = $("#arquivo")[0].files[0];

    formData.append('file', arquivo);
    if(arquivo){
        // Enviar a imagem via AJAX
        $.ajax({
            url: 'script/home_config_js.php?Opc=7',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                switch(response.trim()){
                    case 'TAMANHO':
                        alerta('Arquivo ultrapassa o limite de 500Kb.', 0);
                        break;
                    case 'FORMATO':
                        alerta('Arquivo não respeita o formato exigido.', 0);
                        break;
                    case 'SUCESSO':
                        alerta('Imagem atualizada com sucesso!', 1);
                        setTimeout(()=>{
                            location.reload(true);
                        }, 2000);
                        break;
                    case 'ARQUIVO':
                        alerta('Arquivo está corrompido.', 0);
                        break;
                    case 'ERRO':
                        alerta('Houve um erro ao tentar atualizar imagem.', 0);
                        break;
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alerta('Erro: ' + textStatus + ' - ' + errorThrown, 0);
            }
        });
    }
});
/*
* Evento: change
* Descrição: Carrega a imagem no img preview para o usuário analisar como irá ficar
* Programador(a): Ighor Drummond
* Data: 31/07/2024
*/
document.getElementById('arquivo').addEventListener('change', function(event) {
    let arquivo = event.target.files[0];

    if (arquivo && arquivo.type.startsWith('image/')) {
        // Exibir pré-visualização da imagem
        let reader = new FileReader();

        reader.onload = function(e) {
            const imagem_preview = document.getElementById('foto_perfil');
            imagem_preview.src = e.target.result;
        };

        reader.readAsDataURL(arquivo);
    } else {
        alerta('Por favor, selecione um arquivo de imagem.', 0);
    }
});

//--------------------------Funções
/*
Função: ativarItens(posic, event)
Descrição: Responsavel 
Data: 19/07/2024
Programador: Ighor Drummond
*/
function ativarItens(posic, event){
    event.stopPropagation();

    if(ListaItem){
        let icone = event.target.getElementsByTagName('i')[0];

        if (icone.classList[1] === 'fa-chevron-down') {
            // Ativa a Lista do Item
            ListaItem[posic].classList.remove('d-none');
            // Troca ícone
            icone.classList.remove('fa-chevron-down');
            icone.classList.add('fa-chevron-up');
        } else {
            // Desativa a Lista do Item
            ListaItem[posic].classList.add('d-none');
            // Troca ícone
            icone.classList.remove('fa-chevron-up');
            icone.classList.add('fa-chevron-down');
        }
    }
}
/*
Função: prosseguirPedido(Ped)
Descrição: Responsavel por continuar o pedido para pagamento
Data: 19/07/2024
Programador: Ighor Drummond
*/
function prosseguirPedido(Ped){
    $('table').load('script/pedidos.php?Opc=15&Ped=' + Ped.toString(), ()=>{
        window.location.href = "pagamento.php";
    });
}
/*
Função: cancelaPedido(Ped)
Descrição: Responsavel por cancelar o Pedido desejado
Data: 19/07/2024
Programador: Ighor Drummond
*/
function cancelaPedido(Ped){
    if(confirm('Deseja realmente cancelar o pedido ' + Ped.toString() + '?')){
        $(Artigos[0]).load('script/pedidos.php?Opc=15&Ped=' + Ped.toString());
        $(Artigos[0]).load('script/pedidos.php?Opc=14', ()=>{
            attPagina(0, 2); 
        });   
    }
}
/*
Função: deletarFav(Id do Produto)
Descrição: Responsavel por deletar o produto do favorito
Data: 20/07/2024
Programador: Ighor Drummond
*/
function deletarFav(Prod){
    $(Artigos[1]).load('script/favoritar.php?Opc=0&Produto=' + encodeURIComponent(Prod), ()=>{
        attPagina(1, 3);   
    });
}

/*
Função: atualizaQuant(Operacao, Id do carrinho, Elemento do Html)
Descrição: Responsavel por aumentar ou diminuir a quantidade do item desejado
Data: 20/07/2024
Programador: Ighor Drummond
*/
function atualizaQuant(Opc, IdCar, element) {
    telaCarregamento(true);
    var btnGroup = element.parentElement;
    var Quantidade = btnGroup.querySelector('.quantidade_acao').textContent;

    //Faz a adição do valor novo valor
    if (Opc === '+') {
        Quantidade = (parseInt(Quantidade) + 1).toString();
    } else {
        if (parseInt(Quantidade) === 1) {
            deletaItem(IdCar);//Apaga do carrinho o produto
        } else {
            Quantidade = (parseInt(Quantidade) - 1).toString();
        }
    }
    $(Artigos[2]).load('script/pedidos.php?Opc=4&IdCar=' + encodeURIComponent(IdCar) + '&Quant=' + encodeURIComponent(Quantidade), ()=>{
        telaCarregamento(false);
        attPagina(3, 5);   
    });
}
/*
Função: deletaProdCar(Id do Carrinho)
Descrição: Responsavel por deletar o produto no carrinho
Data: 20/07/2024
Programador: Ighor Drummond
*/
function deletaProdCar(Car){
    deletaItem(Car);
    attPagina(3, 5);   
}
/*
Função: attPagina(Posição, Opção)
Descrição: Responsavel por atualizar o article responsável  
Data: 20/07/2024
Programador: Ighor Drummond
*/
function attPagina(Posic, Opc){
    $(Artigos[Posic]).load('script/home_config_js.php?Opc=' + Opc.toString());      
}
/*
Função: atualizaNome()
Descrição: Responsavel por atualizar o nome do usuário
Data: 31/07/2024
Programador: Ighor Drummond
*/
function atualizarNome(){
    event.preventDefault();
    var Nome = document.getElementsByName('nome')[0].value;
    $('body').load('script/home_config_js.php?Opc=8&Nome=' + encodeURIComponent(Nome),()=>{
        location.reload(true);
    });   
}