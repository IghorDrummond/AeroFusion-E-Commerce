//Declaração de variaveis
//Elementos
var Tela = document.getElementsByTagName('main')[0];
var carregamento = document.getElementById('tela_carregamento');
var aviso = document.getElementsByClassName('alertas');
var aviso_texto = document.getElementsByClassName('alerta_texto');
var botao_end = null;
var pagamento = null;
//String
var End = '';
//Objeto
var ajax = null;

//-------------------------------Eventos
/*
* Evento: DOMContentLoaded
* Descrição: Carrega solicitação de pedidos
* Programador(a): Ighor Drummond
* Data: 14/06/2024
*/
window.addEventListener('DOMContentLoaded', ()=>{
    telaCarregamento(true);
    novoPedido();
})
//-------------------------------Funções
/*
Função: telaCarregamento(controle de liga ou desliga)   
Descrição: Responsavel por ligar e desligar tela de carregamento
Data: 19/05/2024
Programador: Ighor Drummond
*/
function telaCarregamento(lFecha) {
    if (lFecha) {
        carregamento.classList.remove('d-none');
        carregamento.classList.add('d-flex');
    }
    else {
        carregamento.style.animation = '1s sumir';
        var Z = setTimeout(() => {
            carregamento.classList.remove('d-flex');
            carregamento.classList.add('d-none');
            carregamento.style.animation = '1s surgir';
            clearTimeout(Z);
        }, 1000);
    }
}
/*
Função: alerta()
Descrição: Responsavel por ligar o alerta na página
Data: 19/05/2024
Programador: Ighor Drummond
*/
function alerta(texto, tipoAlerta) {
    aviso[tipoAlerta].classList.remove('d-none');
    aviso[tipoAlerta].classList.add('d-block');
    aviso_texto[tipoAlerta].innerText = texto;
}
/*
Função: fecharAlerta()
Descrição: Responsavel por desligar o alerta da página
Data: 19/05/2024
Programador: Ighor Drummond
*/
function fecharAlerta(alerta) {
    aviso[alerta].classList.remove('d-block');
    aviso[alerta].classList.add('d-none');
}
/*
Função: deletarItem(Id do Carrinho)
Descrição: Responsavel por deletar items do carrinho e do pedido
Data: 15/06/2024
Programador: Ighor Drummond
*/
function deletaItem(Item){
    telaCarregamento(true);
    //Apaga da session o produto
    //Apaga do carrinho o produto
    //Recarrega a página usando a função dom
    ajax = new XMLHttpRequest();
    ajax.open('POST', 'script/pedidos.php?Opc=3&Prod=' + encodeURIComponent(Item));
    ajax.onreadystatechange = ()=>{
        if(ajax.readyState === 4){
            if(ajax.status < 400){
                novoPedido();//Chama para carregar os dados novamente sem o item
            }else{
                window.location.href = "error.php?Error=" + encodeURIComponent(ajax.status.toString());
            }
        }
    }
    ajax.send();
}
/*
Função: novoPedido()
Descrição: Responsavel por carregar dados do pagamentos
Data: 15/06/2024
Programador: Ighor Drummond
*/
function novoPedido(){
    $.ajax({
        url: 'script/pedidos.php?Opc=2',
        method: 'GET',
        dataType: 'html',
        success: function (data) {
            $(Tela).html(data); // Insere os dados no elemento main
            telaCarregamento(false); // Desliga a tela de carregamento
            botao_end = document?.getElementsByClassName('dropdown-toggle')[0];
            pagamento = document?.getElementById('compra').getElementsByTagName('select')[0];
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alerta('Houve um erro ao carregar o seu pedido. Tente novamente ou mais tarde.', 0);
            telaCarregamento(false); // Desliga a tela de carregamento em caso de erro
        }
    });
}
/*
Função: selecionaEndereco(Elemento)
Descrição: Responsavel por selecionar o endereço
Data: 15/06/2024
Programador: Ighor Drummond
*/
function selecionaEndereco(element){
    //Guarda o endereço selecionado
    End = element.id;
    //Ajusta o botão para o titulo correto
    botao_end.textContent = element.getAttribute('data-title');
}
/*
Função: atualizaQuantidade(Operacao, Id do carrinho, Elemento do Html)
Descrição: Responsavel por aumentar ou diminuir a quantidade do item desejado
Data: 18/06/2024
Programador: Ighor Drummond
*/
function atualizaQuantidade(Opc, IdCar, element){
    telaCarregamento(true);
    var btnGroup = element.parentElement;
    var Quantidade = btnGroup.querySelector('.quantidade_acao').textContent;

    //Faz a adição do valor novo valor
    if(Opc === '+'){
        Quantidade = (parseInt(Quantidade) + 1).toString();
    }else{
        if(parseInt(Quantidade) === 1){
            deletaItem(IdCar);//Apaga do carrinho o produto
            return false;
        }else{
            Quantidade = (parseInt(Quantidade) - 1).toString();
        }
    }

    ajax = new XMLHttpRequest();

    ajax.open('POST', 'script/pedidos.php?Opc=4&IdCar=' + encodeURIComponent(IdCar) + '&Quant=' + encodeURIComponent(Quantidade));

    ajax.onreadystatechange = ()=>{
        telaCarregamento(false);
        if(ajax.readyState === 4){
            if(ajax.status < 400){
                novoPedido();//Atualiza o pedido novamente após atualização da quantidade
                if(ajax.responseText  === 'ESTOQUE'){
                    alerta('Limite máximo do estoque atingido!', 0);
                }
            }else{
                alerta('Ocorreu um erro ao adicionar uma nova quantidade. Error: ' + ajax.status.toString(), 0);
            }
        }
    }

    ajax.send();
}
/*
Função: finalizarCompra()
Descrição: Responsavel por finalizar a compra e abrir um novo pedido no banco de dados
Data: 18/06/2024
Programador: Ighor Drummond
*/
function finalizarCompra(){
    //Impede de recarregar a página
    event.preventDefault();

    //Valida se um endereço foi escolhido
    if(botao_end.textContent.substr(0, 8).toUpperCase() === 'ENDEREÇO' ){
        //Valida se foi escolhido um método de pagamento
        if(pagamento.selectedIndex > 0){
            telaCarregamento(true);
            ajax = new XMLHttpRequest();
            ajax.open('POST', 'script/pedidos.php?Opc=5&Pagamento=' + encodeURIComponent(pagamento.selectedIndex) + '&Endereco=' + encodeURIComponent(End));
            ajax.onreadystatechange = ()=>{
                telaCarregamento(false);
                if(ajax.readyState === 4){
                    if(ajax.status < 400){
                        switch(ajax.responseText.trim()){
                            case 'S':
                                //Atualiza para página para pagar o pedido
                                window.location.reload();
                                break;
                            case 'N':
                                alerta('Houve um erro interno em nosso servidor. Tente novamente ou mais tarde.', 0);
                                break;
                        }
                    }else{
                        window.location.href =  "error.php?Error=" + ajax.status.toString();
                    }
                }
            }
            ajax.send();
        }else{
            alerta('Escolha um método de pagamento.', 0);
        }
    }else{
        alerta('Escolha um endereço.', 0);
    }
}
/*
Função: adicionarEnd()
Descrição: chama tela de adicionar um novo endereço
Data: 24/06/2024
Programador: Ighor Drummond
*/
function adicionarEnd(){
    telaCarregamento(true);
    //Carrega arquivo para adição para html
    $.get('script/HTML/htmlAdicionarEnd.php', function(data) {
            telaCarregamento(false);
            $('body').append(data);
        }).fail(function(xhr) {
        alerta("Ocorreu um erro: " + xhr.status + " " + xhr.statusText, 0);
        telaCarregamento(false);
    });
}
/*
Função: fecharEnd()
Descrição: apaga a tela end da página html
Data: 24/06/2024
Programador: Ighor Drummond
*/
function fecharEnd(){
    $('.end_body').remove();
}