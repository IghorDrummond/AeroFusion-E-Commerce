//Declaração de Variaveis
var ListaItem = document?.getElementsByClassName('itens');
//Numero

//--------------------------Eventos
document.addEventListener('DOMContentLoaded', function() {
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

//--------------------------Funções
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
