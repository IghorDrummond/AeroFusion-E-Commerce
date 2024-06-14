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
var quantCarrinho = document.getElementsByClassName('badge-primary');
var produtos = document?.getElementsByClassName('produto');
//Numerico
var tamAnt = 0;
var Tam = 0;
var scrollHeight = 0;
var PosicImg = 0;
var ide = 0;
//Array
var produtosSelecionados = [];
var antValoresInput = [];
var Intervalos = new Array(produtos.length).fill(null);
// Objeto
var ajax = null;
// Função Anônima para abrir a caixa de diálogo
var abreConfig = function () {
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
var buscarProduto = () => {
    if (Pesquisa.value != '') {
        $("#caixaPesq").load('script/caixaPesquisa.php?Produto=' + encodeURIComponent(Pesquisa.value), () => {
            if (tamAnt != caixaPesq.offsetWidth) {
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
Pesquisa.onchange = () => {
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
window.addEventListener('click', function (event) {
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
* Evento: DOMContentLoaded
* Descrição: Carrega a quantidade de items no carrinho
* Programador(a): Ighor Drummond
* Data: 01/06/2024
*/
window.addEventListener('DOMContentLoaded', () => {
    //Inicia os valores
    $('.badge-primary').load('script/carrinho.php?Opc=3');
    //Inicia o intervalo para atualização a cada 1.5 segundos
    var Z = setInterval(() => {
        $('.badge-primary').load('script/carrinho.php?Opc=3');
        /*
        $('#carrinho').load('script/carrinho.php?Opc=1', ()=>{
            carrinho.scrollHeight = scrollHeight;
        });*/
    }, 1000);
});

/*
* Evento: resize
* Descrição: Reajusta posições das janelas abertas
* Programador(a): Ighor Drummond
* Data: 24/05/2024
*/
window.addEventListener('resize', reajusta);

if (configuracao) {
    configuracao.addEventListener('blur', function () {
        configuracao.style.visibility = 'hidden';
        configuracao.close();
    });
}
if (carrinho) {
    carrinho.addEventListener('scroll', () => {
        alert(carrinho.scrollHeight);
    });
}
/*
* Evento: change - input name Produto
* Descrição: Salva produtos para acionar um novo pedido
* Programador(a): Ighor Drummond
* Data: 04/06/2024
*/
document.querySelectorAll('input[name="CarProd"]').forEach(checkbox => {
    checkbox.addEventListener('change', (event) => {
        let valor = event.target.value;

        //verifica se o input que foi selecionado o produto ainda existe no carrinho
        if (document.querySelectorAll('input[name="CarProd"][value="$value"]')) {
            //Valida se há inputs com o checked, ai salva valores
            if (event.target.checked) {
                produtosSelecionados.push(event.target.value);
            } else {
                //valida se há algum valor que foi retirado o check para deletar do array
                produtosSelecionados = produtosSelecionados.filter(item => item !== event.target.value);
            }
        } else {
            //remove valor do input caso o mesmo não existir mais no carrinho
            produtosSelecionados = produtosSelecionados.filter(item => item !== event.target.value);
        }

        console.log(produtosSelecionados);
    });
});

// ------------------------- Funções
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
function abreCarrinho(carShop) {
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
Função: maisDetalhes(Numero do Produto)
Descrição: Encaminha o usuário para página de produtos
Data: 28/05/2024
Programador: Ighor Drummond
*/
function maisDetalhes(NumProd) {
    window.location.href = 'produto.php?Prod=' + encodeURIComponent(NumProd.toString());
}
/*
Função: selecionaCategoria(Nome da categoria)
Descrição: Redireciona para página de pesquisa com a categoria selecionada
Data: 29/05/2024
Programador: Ighor Drummond
*/
function selecionaCategoria(Categoria) {
    window.location.href = 'pesquisa.php?categoria=' + encodeURIComponent(Categoria);
}
/*
Função: pesquisaProduto()
Descrição: Redireciona para página de pesquisa com o produto a ser pesquisado
Data: 29/05/2024
Programador: Ighor Drummond
*/
function pesquisaProduto() {
    event.preventDefault();
    window.location.href = 'pesquisa.php?Pesq=' + encodeURIComponent((Pesquisa.value).toUpperCase());
}
/*
Função: guardaScroll()
Descrição: Guarda Posição do Scroll para fixar logo depois
Data: 01/06/2024
Programador: Ighor Drummond
*/
function guardaScroll() {
    scrollHeight = carrinho.scrollHeight;
}
/*
Função: deletaItem()
Descrição: deleta o produto no carrinho
Data: 03/06/2024
Programador: Ighor Drummond
*/
function deletaItem(Prod) {
    telaCarregamento(true);
    // Inicia a requisição
    ajax = new XMLHttpRequest();
    // prepara a abertura o get.
    ajax.open('GET', 'script/carrinho.php?Opc=4&Prod=' + encodeURIComponent(Prod));
    // monitora o status da solicitação da url
    ajax.onreadystatechange = () => {
        telaCarregamento(false);
        if (ajax.readyState === 4) {
            if (ajax.status < 400) {
                let ret = parseInt(ajax.responseText);
                if (ret > 0) {
                    $('#carrinho').load('script/carrinho.php?Opc=1', () => {
                        selecionarProduto();//Atualiza dados para remoção do item ao carrinho
                    });
                }
            } else {
                window.location.href = 'error.php?Error=' + ajax.status.toString();
            }
        }
    };
    // Inicia Solicitação
    ajax.send();
}
/*
Função: selecionarProduto()
Descrição: cria um novo pedido com os produtos selecionados dentro do carrinho
Data: 04/06/2024
Programador: Ighor Drummond
*/
function selecionarProduto() {
    let checkbox = document.querySelectorAll('input[name="CarProd"]');
    let auxAnt = [];

    checkbox.forEach((check, indice) => {
        //verifique os inputs com check
        if (check.checked) {
            if (!produtosSelecionados.includes(check.value)) {
                produtosSelecionados.push(check.value);
            }
        } else {
            //valida se há algum valor que foi retirado o check para deletar do array
            produtosSelecionados = produtosSelecionados.filter(item => item !== check.value);
        }
        auxAnt.push(check.value);
    });

    //Remove valores ao qual os inputs não existem mais
    antValoresInput.forEach(valor => {
        //valida se o input com o valor anterior não existe mais
        if (document.querySelectorAll('input[name="CarProd"][value="' + valor + '"]').length === 0) {
            //se caso não existir mais, valida se esse valor já estava dentro do input
            if (produtosSelecionados.includes(valor)) {
                //se caso estiver, ele deleta do nosso produtos selecionados
                produtosSelecionados = produtosSelecionados.filter(item => item !== valor)
            }
        }
    });
    //Coloca os novos valores do checkboxs existentes
    antValoresInput = auxAnt;
}

/*
Função: adicionarPedido()
Descrição: cria um novo pedido com os produtos selecionados dentro do carrinho
Data: 04/06/2024
Programador: Ighor Drummond
*/
function adicionarPedido() {
    if (produtosSelecionados.length > 0) {
        let parametros = "";
        //Ativa tela de carregamento
        telaCarregamento(true);
        //Prepara os Produtos para iniciar um pedido
        for(nCont = 0; nCont <= produtosSelecionados.length -1; nCont++){
            parametros += "," + produtosSelecionados[nCont];
        }
        parametros += "&Opc=1";//Carregar dados do pedidos
        parametros = encodeURIComponent(parametros.substr(1, parametros.length-1));
        //Chama o construtor de pedidos onde vai validar se cada produto ainda tem o estoque desejado pelo usuário
        ajax = new XMLHttpRequest();
        ajax.open('POST', 'script/pedidos.php?Prod=' + parametros);
        ajax.onreadystatechange = ()=>{
            telaCarregamento(false);
            if(ajax.readyState === 4){
                if(ajax.status < 400){
                    window.location.href = "pagamento.php";
                }else{
                    window.location.href = "error.php?Error=" + ajax.status.toString(); 
                }
            }
        }
        ajax.send();
        //Chama um fonte que vai enviar os id dos carrinhos e lá ele abre um pedido com status de aguardando, com prazo de 7 dias
        //após isso, vai retornar o id do pedido aqui para js e levaremos para a página de pagamento, validar o numero do id do pedido se é do cliente para não houver fraudes ou invasão de dados indevidos
    } else {
        alerta('Selecione um dos produtos do carrinho para continuar', 0);
    }
}

/*
Função: passaImagens(produto selecionado)
Descrição: passar imagens disponíveis do produto com o mouse em cima
Data: 07/06/2024
Programador: Ighor Drummond
*/
function passaImagens(event) {
    //Desativa imagem principal do produto
    event.getElementsByTagName('img')[0].classList.add('d-none');
    //ativa o carousel
    event.getElementsByClassName('carousel')[0].classList.remove('d-none');
}

/*
Função: paraImagens(produto selecionado)
Descrição: parar a passagem de imagens do produto ao retirar o mouse do produto
Data: 07/06/2024
Programador: Ighor Drummond
*/
function paraImagens(event) {
    //Ativa imagem principal do produto
    event.getElementsByTagName('img')[0].classList.remove('d-none');
    //Desativa o carousel
    event.getElementsByClassName('carousel')[0].classList.add('d-none');
}

/*
Função: favorito(Estrela favorito, Id do Produto)
Descrição: Favorita ou retira o favorito do produto selecionado
Data: 13/06/2024
Programador: Ighor Drummond
*/
function favorito(event, Prod){
    //Declaração de variavel
    let Produto = {
        IdProd : Prod,
        Estado : 0     
    }
    let estrela = event.getElementsByClassName('fa-star')[0];
    
    //Valida o estado do favorito
    if(estrela.classList.contains('fa-regular')){
        Produto.Estado = 1;
    }

    //Atualiza dados após operação 
    ajax = new XMLHttpRequest();
    //Inicia a requisição
    ajax.open('GET', 'script/favoritar.php?Opc=' + encodeURIComponent(Produto.Estado) + "&Produto=" + encodeURIComponent(Produto.IdProd));
    //Atualiza os estados da requisição
    ajax.onreadystatechange = ()=>{
        if(ajax.readyState === 4){
            if(ajax.status < 400){

                if(ajax.responseText.toString() === 'LOGIN'){
                    window.location.href="login.php";
                }else{
                    //Ajusta a estrela
                    switch(Produto.Estado){
                        case 0:
                            estrela.classList.remove('fa-solid');
                            estrela.classList.add('fa-regular');
                            break;
                        case 1:
                            estrela.classList.remove('fa-regular');
                            estrela.classList.add('fa-solid');                            
                            break;
                    }
                }
            }else{
                window.location.href = 'error.php?Error=' + ajax.status.toString();
            }
        }
    }   
    //Ativa requisição
    ajax.send()
}