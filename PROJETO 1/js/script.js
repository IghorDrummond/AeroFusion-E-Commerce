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
var QuadroProduto = document.getElementById('3d');
var Carousel = document.getElementById('carouselExampleIndicators');
var campoApProd = document?.getElementsByTagName('article')[0];
//Numerico
var tamAnt = 0;
var Tam = 0;
var scrollHeight = 0;
var PosicImg = 0;
var ide = 0;
var estadoAnima = 0;
//Array
var produtosSelecionados = [];
var antValoresInput = [];
var Intervalos = new Array(produtos.length).fill(null);
// Objeto
var ajax = null;
var cena = null;
var camera = null;
var renderizacao = null;
var carregador = null;
var Luz = null;
var DirLuz = null;
var modelo = null
var Orbita = null;
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
Pesquisa.onkeyup = buscarProduto;//Para pesquisar o produto
Pesquisa.onchange = ()=>{
    fecharPesq();
}
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
        //fecharPesq();
    }
    if (categoria[0] && categoria[0].classList.contains('d-flex') && window.clientWidth >= 1200) {
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
    });
});
/*
*Evento: resize()
*Descrição: Ajusta o tamanho do quadro 3d caso houver alteração de tamanho da página
*Programador(a): Ighor Drummond
*Data: 20/05/2024
*/
window.addEventListener('resize', () => {
    if(campoApProd && Luz){
        camera.aspect = campoApProd.clientWidth / campoApProd.clientHeight;
        camera.updateProjectionMatrix();
        renderizacao.setSize(campoApProd.clientWidth, campoApProd.clientHeight);   
    }
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
    var menu = document.getElementById('navegacao');

    //Valida se o menu está aberto para desligar ele e abrir a categoria
    if (window.innerWidth < 1200 && menu.classList.contains('show')) {
        menu.classList.remove('show');
    }

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
    var menu = document?.getElementById('navegacao');

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
        carrinho.style.transform = 'translateX(0)'; // Centraliza horizontalmente
    }else{
        carrinho.style.left = '50%';
        carrinho.style.transform = 'translateX(-50%)'; // Centraliza horizontalmente
        carrinho.style.top = '10px'; // Distância do topo da página
    }

    //Valida se o menu e categoria estão abertos para desligar o menu e manter categoria em aberto
    if(menu){
        if(window.innerWidth < 1200){
            if(menu.classList.contains('show') && categoria[0].classList.contains('d-flex')){
                menu.classList.remove('show');//Desliga categoria
            }
        }
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
    // Carrega dados do carrinho
    $('#carrinho').load('script/carrinho.php?Opc=1');

    // Obtenha a posição do ícone do carrinho
    let rect = iconeCarrinho[carShop].getBoundingClientRect();

    if (window.innerWidth < 993) {
        // Para telas menores que 993, ajuste para o centro
        carrinho.style.left = '50%';
        carrinho.style.transform = 'translateX(-50%)'; // Centraliza horizontalmente
        carrinho.style.top = '10px'; // Distância do topo da página
    }else{
        let dialogWidth = carrinho.offsetWidth;
        let dialogHeight = carrinho.offsetHeight;
        // Define a posição padrão para telas maiores ou iguais a 1200 pixels
        carrinho.style.left = (rect.left + rect.width / 2 - dialogWidth / 2) + 'px';
        carrinho.style.top = (rect.bottom + 15) + 'px';
    }

    carrinho.style.visibility = 'visible'; // Altera visibilidade
    carrinho.show();
}/*
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
    if(Categoria != 'promoção'){
        window.location.href = 'pesquisa.php?categoria=' + encodeURIComponent(Categoria);
    }else{
        window.location.href = "pesquisa.php?promocao=true";
    }
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
        parametros = encodeURIComponent(parametros.substr(1, parametros.length-1));
        //Chama o construtor de pedidos onde vai validar se cada produto ainda tem o estoque desejado pelo usuário
        ajax = new XMLHttpRequest();
        ajax.open('POST', 'script/pedidos.php?Prod=' + parametros + "&Opc=1");
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
/*
Função: visualizar3D(elemento)
Descrição: Responsavel por configurar todo o quadro 3d
Data: 14/06/2024
Programador: Ighor Drummond
*/
function visualizar3D(element){
    var srcObj = element.getAttribute('data-toggle');
    let i = document.createElement('i');

    if(estadoAnima === 0){
        telaCarregamento(true);
        estadoAnima = 1;
        carregarCena();//Carrega cenário
        carregarLuz();//Carregar iluminação do cenário
        carregarObj3D(srcObj);//Adicionar objeto ao cenário
        i.classList.add('fa-solid', 'fa-panorama');
        element.textContent = 'Voltar para Imagens ';
        element.appendChild(i);
    }else{
        destruirCenario();
        QuadroProduto.classList.add('d-none');
        Carousel.classList.remove('d-none');
        estadoAnima = 0;
        i.classList.add('fa-solid', 'fa-cubes');
        element.textContent = 'Visualizar em 3D ';
        element.appendChild(i);
    }
}
/*
Função: carregarCena()
Descrição: Responsavel por carregar a cena, camera e adicionar ao Quadro
Data: 14/06/2024
Programador: Ighor Drummond
*/
function carregarCena(){
    //Carrega cena para iniciar um quadro 3D
    cena = new THREE.Scene();
    //Ajusta a pespectiva da camera
    camera = new THREE.PerspectiveCamera(75, campoApProd.clientWidth / campoApProd.clientHeight, 0.1, 1000);
    camera.position.set(0, 6, 35); // Ajusta a posição inicial da câmera
    renderizacao = new THREE.WebGLRenderer({antialias: true, alpha: true})//Liga o Anti-Aliasing
    renderizacao.setClearColor(0x000000, 0);//Remove fundo preto do cebário, tornado transparente
    renderizacao.setSize(campoApProd.clientWidth, campoApProd.clientHeight);//Ajusta tamanho do quadro para renderizar
    //Seta tamanho do quadro igual ao do campoApProd
    QuadroProduto.clientWidth = campoApProd.clientWidth;
    QuadroProduto.clientHeight = campoApProd.clientHeight;
    //Desliga campoApProd
    Carousel.classList.add('d-none');
    //Ativa Quadro 3d
    QuadroProduto.classList.remove('d-none');
    //Adiciona Renderização ao corpo do elemento
    QuadroProduto.appendChild(renderizacao.domElement);
}
/*
Função: carregarLuz()
Descrição: Responsavel por configurar a luz e a direção da mesma além de iniciar controle de orbita
Data: 14/06/2024
Programador: Ighor Drummond
*/
function carregarLuz(){
    Luz = new THREE.AmbientLight(0xffffff, 0.5);//Adiciona cor e força da iluminação
    //Adiciona ao cenário
    cena.add(Luz);

    //Aplicando uma direção a luz
    DirLuz = new THREE.DirectionalLight(0xffffff, 0.9);
    DirLuz.position.set(8, 100, 2);
    cena.add(DirLuz);

    //Adiciona orbita de controle
    Orbita = new THREE.OrbitControls(camera, renderizacao.domElement);
    Orbita.enableDamping = true; // Adiciona amortecimento para suavizar movimentos
    Orbita.minDistance = 10; // Distância mínima da câmera ao objeto 
    Orbita.maxDistance = 50; // Distância máxima da câmera ao objeto 
}
/*
Função: carregarObj3D(diretorio 3d)
Descrição: Responsavel por carregar Objeto 3d e adicionar ao cenário
Data: 14/06/2024
Programador: Ighor Drummond
*/
function carregarObj3D(src){
    //Prepara para adicionar o objeto no formato GLTF
    carregador = new THREE.GLTFLoader();
    //Adiciona Objeto pelo diretório e Configura objeto 3d com Orbita de controle
    carregador.load(src, (gltf)=>{
        modelo = gltf.scene;//Modelo recebe cena para configurar
        modelo.position.set(0, 0.6, 0);//posiciona o elemento 
        cena.add(modelo);//adiciona elemento cofigurado ao cenário

        if (typeof callback === 'function') {
            callback();
        }

        //Adiciona orbita controlavel
        function animacao(){
            requestAnimationFrame(animacao);
            //anima orbita
            Orbita.update();
            //Renderiza frame 
            renderizacao.render(cena, camera);
        }

        animacao(); //inicia animação de controle de orbita
    }, undefined, function(error){
        alerta('Algo deu errado ao carregar elemento 3D', 0);
    });
}
/*
Função: destruirCenario()
Descrição: Responsavel por destruir cenário e objetos 3d
Data: 14/06/2024
Programador: Ighor Drummond
*/
function destruirCenario(){
    // Limpar a cena
    cena.remove(); // Remove todos os objetos da cena

    // Remover renderizador
    renderizacao.domElement.remove();

    // Limpar controles, se existirem
    if (Orbita) {
        Orbita.dispose();
        Orbita = null;
    }

    // Limpar variáveis globais
    cena = null;
    camera = null;
    renderizacao = null;
}
/*
Função: callback()
Descrição: Responsavel por dar um estado de concluído ao carregar objeto
Data: 14/06/2024
Programador: Ighor Drummond
*/
function callback(){
    telaCarregamento(false);
}
/*
Função: adicionarEnd()
Descrição: chama tela de adicionar um novo endereço
Data: 24/06/2024
Programador: Ighor Drummond
*/
function adicionarEnd() {
    telaCarregamento(true);
    //Carrega arquivo para adição para html
    $.get('script/HTML/htmlAdicionarEnd.php', function (data) {
        telaCarregamento(false);
        $('body').append(data);
    }).fail(function (xhr) {
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
function fecharEnd() {
    $('.end_body').remove();
}
/*
Função: cadastrarEnd()
Descrição: apaga a tela end da página html
Data: 24/06/2024
Programador: Ighor Drummond
*/
function cadastrarEnd(event) {
    var param = '';
    var form = document.getElementsByClassName('end_dados')[0].getElementsByTagName('form')[0];
    var formData = null;
    //Desliga recarregamento da página após da submit
    event.preventDefault();
    // Cria um objeto FormData a partir do formulário
    formData = new FormData(form);
    // Percorre os dados do formulário
    for (let [name, value] of formData) {
        param += "&" + encodeURIComponent(name) + "=" + encodeURIComponent(value);
    }
    //Inicia uma requisição ajax
    ajax = new XMLHttpRequest();

    ajax.open('POST', 'script/pedidos.php?Opc=6' + param);

    ajax.onreadystatechange = () => {
        if (ajax.readyState === 4) {
            if (ajax.status < 400) {
                fecharEnd();//Fecha tela de endereço
                switch (ajax.responseText.trim()) {
                    case 'OK':
                        alerta('Endereço Cadastrado com Sucesso!', 1);
                        if(typeof attPagina === 'function'){
                            attPagina(4, 6);
                        }else{
                            novoPedido();
                        }
                        break;
                    case 'EXISTE':
                        alerta('Endereço já cadastrado! Por favor, informe um endereço diferente.', 0);
                        break;
                    case 'FALTADADOS':
                        alerta('Cep inválido! Por favor, preencha com um código postal válido.', 0);
                        break;
                    case 'OK':
                        alerta('Endereço Inserido com sucesso!', 1);
                        setTimeout(()=>{
                            location.reload(true);
                        }, 2000);
                        break;
                    default:
                        alerta('Ocorreu um erro interno em nosso servidor. tente novamente ou mais tarde.', 0);
                        break;
                }
            } else {
                window.location.href = "error.php?Error=" + ajax.status.toString();
            }
        }
    }

    ajax.send();
}
/*
Função: mascaraCep()
Descrição: Mascara automatica de CEP
Data: 20/05/2024
Programador: Ighor Drummond   
*/
function mascaraCep() {
    var cep = document.getElementsByName('cep')[0];
    var aux = '';

    for (nCont = 0; nCont <= cep.value.length - 1; nCont++) {
        if (nCont === 5 && cep.value.substr(nCont, 1) != '-') {
            aux += '-';
        }

        aux += cep.value.substr(nCont, 1);
    }

    cep.value = aux;
}
/*
Função: buscaEndereco()
Descrição: Responsavel por buscar cep da cidade
Data: 26/05/2024
Programador: Ighor Drummond   
*/
function buscaEndereco() {
    var cep = document.getElementsByName('cep')[0].value;
    var End = document.getElementsByClassName('end_dados')[0].getElementsByTagName('input');
    var jsonHttp = null;

    if (cep.length === 9) {
        jsonHttp = new XMLHttpRequest();
        jsonHttp.open('GET', 'https://viacep.com.br/ws/' + (cep.replace('-', '')) + '/json/');

        jsonHttp.onreadystatechange = () => {
            if (jsonHttp.readyState === 4 && jsonHttp.status < 400) {
                let jsonVal = JSON.parse(jsonHttp.responseText);
                //Ejeta dados direto nos inputs
                if (!jsonVal.hasOwnProperty('erro')) {
                    End[1].value = jsonVal.logradouro;
                    End[2].value = jsonVal.bairro;
                    End[3].value = jsonVal.localidade;
                    End[4].value = jsonVal.uf;
                }else{
                    for(nCont = 1; nCont <= 4; nCont++){
                        End[nCont].value = "";
                    }
                }
            }
        }
        jsonHttp.send();
    }
}