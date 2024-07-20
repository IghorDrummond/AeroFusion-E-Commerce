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

//--------------------------Funções
/*
Função: alerta()
Descrição: Responsavel por ligar o alerta na página
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
Função: alerta()
Descrição: Responsavel por ligar o alerta na página
Data: 19/07/2024
Programador: Ighor Drummond
*/
function prosseguirPedido(Ped){
    $('table').load('script/pedidos.php?Opc=15&Ped=' + Ped.toString(), ()=>{
        window.location.href = "pagamento.php";
    });
}
/*
Função: alerta()
Descrição: Responsavel por ligar o alerta na página
Data: 19/07/2024
Programador: Ighor Drummond
*/
function cancelaPedido(Ped){
    if(confirm('Deseja realmente cancelar o pedido ' + Ped.toString() + '?')){
        $(Artigos[0]).load('script/pedidos.php?Opc=15 &Ped=' + Ped.toString());
        $(Artigos[0]).load('script/pedidos.php?Opc=14');
        attPagina(0, 2);    
    }
}
/*
Função: atualizaQuantidade(Operacao, Id do carrinho, Elemento do Html)
Descrição: Responsavel por aumentar ou diminuir a quantidade do item desejado
Data: 20/07/2024
Programador: Ighor Drummond
*/
function deletarFav(Prod){
    $(Artigos[1]).load('script/favoritar.php?Opc=1&Produto="' + encodeURIComponent(Prod));
    attPagina(1, 3);   
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
    });
    attPagina(3, 5);   
}
/*
Função: atualizaQuant(Operacao, Id do carrinho, Elemento do Html)
Descrição: Responsavel por aumentar ou diminuir a quantidade do item desejado
Data: 20/07/2024
Programador: Ighor Drummond
*/
function deletaProdCar(Car){
    deletaItem(Car);
    attPagina(3, 5);   
}
/*
Função: atualizaQuant(Operacao, Id do carrinho, Elemento do Html)
Descrição: Responsavel por aumentar ou diminuir a quantidade do item desejado
Data: 20/07/2024
Programador: Ighor Drummond
*/
function attPagina(Posic, Opc){
    $(Artigos[Posic]).load('script/home_config_js.php?Opc=' + Opc.toString());      
}