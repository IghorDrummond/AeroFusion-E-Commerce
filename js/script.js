// Declaração de Variáveis
//Elementos
var categoria = document.getElementsByClassName('navegacao');
var carregamento = document.getElementById('tela_carregamento');
var img = document.getElementById('informacoes');
var carrinho = document.getElementById('carrinho');
var configuracao = document.getElementById('configuracao');
var iconeCarrinho = document.getElementsByClassName('fa-cart-shopping');
var Pesquisa = document.getElementById('Pesquisa');
var caixaPesq = document.getElementById('caixaPesq');
var aviso = document.getElementsByClassName('alertas');
var aviso_texto = document.getElementsByClassName('alerta_texto');
//Numerico
var tamAnt = 0;
var Tam = 0;
// Objeto
var ajax = null;
// Função Anônima para abrir a caixa de diálogo
var abreConfig = function() {
    // Obtenha a posição da imagem
    let rect = img.getBoundingClientRect();
    let dialogWidth = configuracao.offsetWidth;

    // Posiciona o configuracao abaixo da imagem
    configuracao.style.left = (rect.left + rect.width / 2 - dialogWidth / 2) + 'px';
    configuracao.style.top = rect.bottom + 'px';
    configuracao.style.visibility = 'visible'; // Altera visibilidade
    // Abre o configuracao
    configuracao.show();
};
var abrePesq = () => {
    // Obtenha a posição da imagem
    let Dir = Pesquisa.getBoundingClientRect();
    let dialogWidth = caixaPesq.offsetWidth;
    tamAnt = dialogWidth;
    caixaPesq.style.left = (Dir.left + Dir.width / 2 - dialogWidth / 2) + 'px';
    caixaPesq.style.top = (Dir.bottom + 4) + 'px';
    caixaPesq.style.visibility = 'visible'; // Altera visibilidade
    caixaPesq.show();
};
var buscarProduto = ()=>{
    if(Pesquisa.value != ''){
        $("#caixaPesq").load('script/caixaPesquisa.php?Produto=' + encodeURIComponent(Pesquisa.value), ()=>{
            if(tamAnt != caixaPesq.offsetWidth){
                abrePesq();
            }
        });
    }
}

// ------------------------- Escopo
if (img) {
    img.onclick = abreConfig;
}
//Configura a barra de pesquisa
Pesquisa.onfocus = abrePesq;//Para abrir a pesquisa
Pesquisa.onchange = ()=>{
    fecharPesq();
}
Pesquisa.onkeyup = buscarProduto;
// ------------------------- Eventos
/*
* Evento: click
* Descrição: Desliga janelas ao apertar em algum canto do site
* Programador(a): Ighor Drummond
* Data: 23/05/2024
*/
window.addEventListener('click', function(event) {
    if (configuracao && !configuracao.contains(event.target) && event.target !== img) {
        fecharConfig();
    }
    if (caixaPesq && !caixaPesq.contains(event.target) && event.target !== Pesquisa) {
        fecharPesq();
    }
    if (categoria[0] && categoria[0].classList.contains('d-flex')) {
        desligaCab();
    }
});

/*
* Evento: resize
* Descrição: Reajusta posições das janelas abertas
* Programador(a): Ighor Drummond
* Data: 24/05/2024
*/
window.addEventListener('resize', reajusta);

if (configuracao) {
    configuracao.addEventListener('blur', function() {
        configuracao.style.visibility = 'hidden';
        configuracao.close();
    });
}

// ------------------------- Funções
/*
Função: alerta()
Descrição: Responsavel por ligar o alerta na página
Data: 19/05/2024
Programador: Ighor Drummond
*/
function alerta(texto, tipoAlerta){
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
function fecharAlerta(alerta){
    aviso[alerta].classList.remove('d-block');
    aviso[alerta].classList.add('d-none');
}
/*
* Função: ligaCab
* Descrição: Liga categorias
* Programador(a): Ighor Drummond
* Data: 20/05/2024
*/
function ligaCab() {
    categoria[0].classList.remove('d-none');
    categoria[0].classList.add('d-flex');
}

/*
* Função: desligaCab()
* Descrição: Desliga categorias
* Programador(a): Ighor Drummond
* Data: 20/05/2024
*/
function desligaCab() {
    categoria[0].style.animation = '1s sumir';
    var Y = setTimeout(() => {
        categoria[0].classList.remove('d-flex');
        categoria[0].classList.add('d-none');
        categoria[0].style.animation = '1s surgir';
    }, 1000);
}

/*
* Função: reajusta()
* Descrição: Reajusta janela do carrinho e configuração
* Programador(a): Ighor Drummond
* Data: 23/05/2024
*/
function reajusta() {
    var dialogWidth = null;
    var rect = null;
    if (img) {
        // Obtenha a posição da imagem
        rect = img.getBoundingClientRect();
        dialogWidth = configuracao.offsetWidth;

        // reajusta posição do configuracao caso estiver aberto
        configuracao.style.left = (rect.left + rect.width / 2 - dialogWidth / 2) + 'px';
        configuracao.style.top = rect.bottom + 'px';
    }
    // Ajusta Posição do carrinho
    if (window.innerWidth >= 993) {
        // Obtenha a posição da imagem
        rect = iconeCarrinho[0].getBoundingClientRect();
        dialogWidth = carrinho.offsetWidth;
        carrinho.style.left = (rect.left + rect.width / 2 - dialogWidth / 2) + 'px';
        carrinho.style.top = (rect.bottom + 15) + 'px';
    }

    // Ajusta Posição da caixa de pesquisa
    rect = Pesquisa.getBoundingClientRect();
    dialogWidth = caixaPesq.offsetWidth;
    caixaPesq.style.left = (rect.left + rect.width / 2 - dialogWidth / 2) + 'px';
    caixaPesq.style.top = (rect.bottom + 4) + 'px';
}
/*
* Função: fecharConfig()
* Descrição: Fecha janela de configuração
* Programador(a): Ighor Drummond
* Data: 23/05/2024
*/
function fecharConfig() {
    configuracao.style.visibility = 'hidden';
    configuracao.close();
}
/*
* Função: fecharCarrinho()
* Descrição: Fecha janela do carrinho
* Programador(a): Ighor Drummond
* Data: 24/05/2024
*/
function fecharCarrinho() {
    carrinho.style.visibility = 'hidden';
    carrinho.close();
}
/*
* Função: solicitacao(opção da operação)
* Descrição: Faz uma solicitação
* Programador(a): Ighor Drummond
* Data: 23/05/2024
*/
function solicitacao(opc) {
    telaCarregamento(true);
    // Inicia a requisição
    ajax = new XMLHttpRequest();
    // prepara a abertura o get.
    ajax.open('GET', 'script/solicitacao.php?opc=' + opc.toString());
    // monitora o status da solicitação da url
    ajax.onreadystatechange = () => {
        telaCarregamento(false);
        if (ajax.readyState === 4) {
            if (ajax.status < 400) {
                location.reload();
            } else {
                window.location.href = 'error.php?Error=' + ajax.status.toString();
            }
        }
    };
    // Inicia Solicitação
    ajax.send();
}
/*
* Função: fecharPesq()
* Descrição: Fecha a caixa de pesquisa
* Programador(a): Ighor Drummond
* Data: 26/05/2024
*/
function fecharPesq() {
    caixaPesq.style.visibility = 'hidden';
    caixaPesq.close();
}
/*
* Função: abreCarrinho(icone ao qual foi selecionado)
* Descrição: Abre o carrinho do usuário
* Programador(a): Ighor Drummond
* Data: 26/05/2024
*/
function abreCarrinho(carShop){
    //Carrega dados do carrinho
    $('#carrinho').load('script/carrinho.php?Opc=1');
    // Obtenha a posição da imagem
    let rect = iconeCarrinho[carShop].getBoundingClientRect();
    let dialogWidth = carrinho.offsetWidth;
    carrinho.style.left = (rect.left + rect.width / 2 - dialogWidth / 2) + 'px';
    carrinho.style.top = (rect.bottom + 15) + 'px';
    carrinho.style.visibility = 'visible'; // Altera visibilidade
    carrinho.show();
};
/*
Função: telaCarregamento(controle de liga ou desliga)   
Descrição: Responsavel por ligar e desligar tela de carregamento
Data: 19/05/2024
Programador: Ighor Drummond
*/
function telaCarregamento(lFecha){
    if(lFecha){
        carregamento.classList.remove('d-none');
        carregamento.classList.add('d-flex');
    }
    else{
        carregamento.style.animation = '1s sumir';
        var Z = setTimeout(()=>{
            carregamento.classList.remove('d-flex');
            carregamento.classList.add('d-none');
            carregamento.style.animation = '1s surgir';
            clearTimeout(Z);
        },1000);
    }
}
/*
Função: maisDetalhes(Numero do Produto)
Descrição: Encaminha o usuário para página de produtos
Data: 28/05/2024
Programador: Ighor Drummond
*/
function maisDetalhes(NumProd){  
    window.location.href = 'produto.php?Prod=' + encodeURIComponent(NumProd.toString());
}
/*
Função: selecionaCategoria(Nome da categoria)
Descrição: Redireciona para página de pesquisa com a categoria selecionada
Data: 29/05/2024
Programador: Ighor Drummond
*/
function selecionaCategoria(Categoria){  
    window.location.href = 'pesquisa.php?categoria='+ encodeURIComponent(Categoria);
}
/*
Função: pesquisaProduto()
Descrição: Redireciona para página de pesquisa com o produto a ser pesquisado
Data: 29/05/2024
Programador: Ighor Drummond
*/
function pesquisaProduto(){  
    window.location.href = 'pesquisa.php?Pesq='+ encodeURIComponent((Pesquisa.value).toUpperCase());
}
