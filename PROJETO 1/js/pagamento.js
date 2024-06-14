//Declaração de variaveis
//Elementos
var Tela = document.getElementsByTagName('main')[0];
var carregamento = document.getElementById('tela_carregamento');
var aviso = document.getElementsByClassName('alertas');
var aviso_texto = document.getElementsByClassName('alerta_texto');

//-------------------------------Eventos
/*
* Evento: DOMContentLoaded
* Descrição: Carrega solicitação de pedidos
* Programador(a): Ighor Drummond
* Data: 14/06/2024
*/
window.addEventListener('DOMContentLoaded', ()=>{
	telaCarregamento(true);//Carrega dados do pedido
    $.ajax({
        url: 'script/pedidos.php?Opc=2',
        method: 'GET',
        dataType: 'html',
        success: function (data) {
            $(Tela).html(data); // Insere os dados no elemento main
            telaCarregamento(false); // Desliga a tela de carregamento
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alerta('Houve um erro ao carregar o seu pedido. Tente novamente ou mais tarde.', 0);
            telaCarregamento(false); // Desliga a tela de carregamento em caso de erro
        }
    });
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