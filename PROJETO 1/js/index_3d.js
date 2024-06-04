//Definindo Variaveis Globais
//Objetos
var cena = null;
var camera = null;
var renderizacao = null;
var carregador = null;
var animacaoClip = null;
var modelo = null;
var acaoFrame = null;
var mixagem = null;
var Luz = null;
var DirLuz = null;
var rua = null;
var ruaGeometria = null;
var ruaTextura = null;
var ruaMaterial = null;
var controles = null;
var chaoGeometria = null;
var chaoMaterial = null;
var chao = null;
var ceuMaterial = null;
var ceuGeometria = null;
var ceu = null;
//Array
var predios = [];
//Elementos
var carousel = document.getElementById('carouselExampleIndicators');
var quadro = document.getElementById('quadro');
var indicador = document.getElementsByClassName('indicador');
var descricao = document.getElementsByClassName('carousel-caption')[0].getElementsByTagName('p');
//Numerico
var AntAnima = 0;
var nAnima = 0;
var velocidade = 0.01;
var nCont = 0;
var posicAni = 0;
var nAntAni = 0;
var intervalo = 0;
//Constantes
const ROTATIONX = -1.076570724955071;
const ROTATIONY = -1.4848722955694649;
const ROTATIONZ = -1.0750257452452618;
const POSITIONX = -0.5488504791185305;
const POSITIONY = 0.28141763142527265;
const POSITIONZ = 0.062148339544222636;
//----------------Eventos
/*
*Evento: resize()
*Descrição: Ajusta o tamanho do quadro 3d caso houver alteração de tamanho da página
*Programador(a): Ighor Drummond
*Data: 16/05/2024
*/
window.addEventListener('resize', () => {
    camera.aspect = carousel.clientWidth / carousel.clientHeight;
    camera.updateProjectionMatrix();
    renderizacao.setSize(carousel.clientWidth, carousel.clientHeight);
});
/*
*Evento: DOMContentLoaded
*Descrição: Carrega o 3D após a página ser carregada
*Programador(a): Ighor Drummond
*Data: 16/05/2024
*/
document.addEventListener('DOMContentLoaded', () => {
    configuracaoCena();//Responsavel por configurar o cenário
    configuracaoLuz();//Responsavel por colocar a Luz no cenário
    configuracaoObj3D();//Responsavel por adicionar o objeto 3d
    configuracaoCenario();//Responsavel por criar o cenário animado
});
//----------------Funções
/*
*Função: configuaracaoCena()
*Descrição: Configura a cena, camera e renderização além de definir o quadro 3d
*Programador(a): Ighor Drummond
*Data: 15/05/2024
*/
function configuracaoCena(){
    //Configurando a Cena, camera e renderizando o tamanho do quadro
    cena = new THREE.Scene();
    //Ajusta a pesperctiva da camera
    camera = new THREE.PerspectiveCamera(75, carousel.clientWidth / carousel.clientHeight, 0.1, 1000);
    camera.position.set(POSITIONX, POSITIONY, POSITIONZ); // Posiciona a câmera para a esquerda do personagem
    camera.rotation.set(ROTATIONX, ROTATIONY, ROTATIONZ);//Posiciona a câmera na rotação do personagem
    renderizacao = new THREE.WebGLRenderer({ antialias: true });//Ativa o Anti-Aliasing
    //Seta configurações do quadro no renderizador
    renderizacao.setSize(carousel.clientWidth, carousel.clientHeight);
    //Adiciona renderização ao elemento HTML
    quadro.appendChild(renderizacao.domElement);
}
/*
*Função: configuaracaoLuz()
*Descrição: Configura a luz do ambiente
*Programador(a): Ighor Drummond
*Data: 15/05/2024
*/
function configuracaoLuz(){
    Luz = new THREE.AmbientLight(0xffffff, 0.5);
    cena.add(Luz); //adicionando Luz na scene

    //Aplicando uma direção para luz
    DirLuz = new THREE.DirectionalLight(0xffffff, 0.9);
    DirLuz.position.set(8, 100, 2);
    cena.add(DirLuz);
}
/*
*Função: configuaracaoObj3D()
*Descrição: Adiciona objetos 3d ao cenário
*Programador(a): Ighor Drummond
*Data: 15/05/2024
*/
function configuracaoObj3D(){
    //Inicia o objeto para receber o glb 3d
    carregador = new THREE.GLTFLoader();
    //Irá carregar o objeto 3d setando algumas propriedades
    carregador.load('3Ds/Personagem skatista.glb', (gltf)=>{
        modelo = gltf.scene;//Modelo recebe a cena
        cena.add(modelo);
        //Defini a Escala vezes o tamanho do modelo no sentido x, y e z
        modelo.scale.set(8, 8, 8);
        // Adicionando mixagem para controlar a animação
        mixagem = new THREE.AnimationMixer(modelo);
        //controles = new THREE.OrbitControls(camera, renderizacao.domElement);
        animacaoClip = gltf.animations[0];//Seleciona a Animação a ser animada
        acaoFrame = mixagem.clipAction(animacaoClip);//Seta animação ao objeto
        acaoFrame.play();//Renderiza em frames a animação
 
        function animacao(){
            requestAnimationFrame(animacao);//Recebe função para animar

            //Troca de animação de acordo com a transição do slide
            if (nAntAni !== posicAni) {
                // Armazena a ação atual para a transição
                let acaoAnterior = acaoFrame;

                // Seleciona a próxima animação
                animacaoClip = gltf.animations[posicAni];

                // Define a próxima ação
                acaoFrame = mixagem.clipAction(animacaoClip);

                // Realiza a transição suave entre as animações
                acaoAnterior.crossFadeTo(acaoFrame, 1, true);

                // Define a velocidade de reprodução para garantir consistência
                acaoFrame.setEffectiveTimeScale(1); // Defina a velocidade de reprodução como 1 (velocidade normal)

                // Reinicia a próxima ação
                acaoFrame.reset();
                acaoFrame.play();

                // Atualiza o índice da animação anterior
                nAntAni = posicAni;
            }
            mixagem.update(0.01); // Atualiza a animação
            TWEEN.update();//Atualiza rotação da camera caso houver
            renderizacao.render(cena, camera); //Começa a renderizar o objeto no quadro 
            //controles.update();//atualiza a movimentação da camera
            //console.log('Posição da câmera:', camera.position);
            //console.log('Rotação da câmera:', camera.rotation);
        }
        animacao();

    }); 
}
/*
*Função: configuaracaoCenario()
*Descrição: Adiciona prédios, rua e o fundo estático ao quadro 3d
*Programador(a): Ighor Drummond
*Data: 16/05/2024
*/
function configuracaoCenario() {
    // Geometria do chão (plano)
    chaoGeometria = new THREE.PlaneGeometry(30, 50); // Largura e comprimento do chão

    // Textura do chão
    chaoTextura = new THREE.TextureLoader().load('img/rua.jpg');
    chaoMaterial = new THREE.MeshBasicMaterial({ map: chaoTextura });

    // Criando o chão
    chao = new THREE.Mesh(chaoGeometria, chaoMaterial);
    chao.rotation.x = -Math.PI / 2; // Rotaciona o chão para que fique horizontal
    cena.add(chao); // Adiciona o chão à cena

    //Construindo um céu
    ceuGeometria = new THREE.SphereGeometry(500, 32, 32); // Tamanho grande o suficiente para envolver toda a cena
    ceuMaterial = new THREE.MeshBasicMaterial({
        color: 0x87ceeb, // Cor azul clara para o céu
        side: THREE.BackSide // Para que o material seja visível no lado de dentro da esfera
    });
    ceu = new THREE.Mesh(ceuGeometria, ceuMaterial);
    cena.add(ceu);

    // Carregar e adicionar prédios
    carregador = new THREE.GLTFLoader();
    carregador.load('3Ds/Predio.glb', function (gltf) {
        var predio1 = gltf.scene;
        predio1.scale.set(1, 1, 1); // Define a escala do prédio conforme necessário
        predio1.position.set(5, 0, 20); // Posicione o primeiro prédio ao lado do personagem
        predio1.rotation.y = -300;
        cena.add(predio1);
        predios.push(predio1); // Adiciona o prédio ao array de prédios

        var predio2 = predio1.clone(); // Clona o primeiro prédio para criar o segundo
        predio2.position.set(5, 0, 40); // Posicione o segundo prédio ao lado do primeiro
        predio2.rotation.y = -300;
        cena.add(predio2);
        predios.push(predio2); // Adiciona o prédio ao array de prédios

        var predio3 = predio1.clone(); // Clona o primeiro prédio para criar o segundo
        predio3.position.set(5, 0, 60); // Posicione o segundo prédio ao lado do primeiro
        predio3.rotation.y = -300;
        cena.add(predio3);
        predios.push(predio3); // Adiciona o prédio ao array de prédios

        movimentaCenario(); // Inicia a animação do cenário
    });

    // Adicionar o plano de fundo da cidade
    var fundoGeometria = new THREE.PlaneGeometry(700, 100); // Ajuste as dimensões do fundo 
    var fundoTextura = new THREE.TextureLoader().load('img/cidade.png');
    var fundoMaterial = new THREE.MeshBasicMaterial({ map: fundoTextura });
    fundo = new THREE.Mesh(fundoGeometria, fundoMaterial);
    fundo.position.set(100, 47, -5); // Ajuste a posição para manter o cenário no fundo
    fundo.rotation.y = -7.8;//Rotaciona para direção da camera
    cena.add(fundo);
}
/*
*Função: movimentaCenario()
*Descrição: Anima o cenário dando uma imersão de movimentação
*Programador(a): Ighor Drummond
*Data: 16/05/2024
*/
function movimentaCenario() {
    requestAnimationFrame(movimentaCenario); // Recebe função para animar

    // Responsável por movimentar os prédios ao fundo
    predios.forEach((predio) => {
        predio.position.z <= -35 ? predio.position.z = 25 : predio.position.z -= 0.1;
    });

    // Responsável por movimentar o chão
    chao.position.z < -2 ? chao.position.z = 0 : chao.position.z -= 0.1;

    renderizacao.render(cena, camera); // Começa a renderizar o objeto no quadro 
}
/*
*Função: trocaCena()
*Descrição: Troca animação quando é pedido para rolar o carousel
*Programador(a): Ighor Drummond
*Data: 16/05/2024
*/
function trocaCena(opc){
    switch(opc){
        case '-':
            nCont--;
            break;
        case '+':
            nCont++;
            break;
    }
    //Valida se os Valores Inseridos ultrapassaram o limite
    nCont > 2 ? nCont = 0 : '';
    nCont < 0 ? nCont = 2 : '';

    if(nCont === 0){
        indicador[posicAni].classList.remove('active');
        posicAni = 0;
        indicador[posicAni].classList.add('active');
        descricao[0].innerText = 'Aproveite as últimas novidades com os mais recentes lançamentos dos modelos Jordan!';
                            // Posiciona a câmera para a esquerda do personagem             //Posiciona a câmera na rotação do personagem
        transicaoCamera(POSITIONX, POSITIONY, POSITIONZ, ROTATIONX, ROTATIONY, ROTATIONZ);
    }else if(nCont === 1){
        indicador[posicAni].classList.remove('active');
        posicAni = 1;
        indicador[posicAni].classList.add('active');
        descricao[0].innerText = 'Promoções Imperdíveis Esperam por Você!';
                            // Posiciona a câmera para a esquerda do personagem             //Posiciona a câmera na rotação do personagem
        transicaoCamera(-0.4445285750409234, 0.22921641473885265, 0.07875620583475725, 0.5056940587088021, -1.4956832208956772, 0.5044983380968892);
    }else if(nCont === 2){
        indicador[posicAni].classList.remove('active');
        posicAni = 2;
        indicador[posicAni].classList.add('active');
        descricao[0].innerText = 'Encontre Sneakers Únicos Somente Aqui!';
                            // Posiciona a câmera para a esquerda do personagem             //Posiciona a câmera na rotação do personagem
        transicaoCamera(-0.7445285750409234, 0.22921641473885265, 0.09875620583475725, 0.5056940587088021, -1.4956832208956772, 0.7044983380968892); // Posiciona a câmera para a esquerda do personagem
    }

    // Iniciar a animação
    //movCamera();
}
/*
*Função: transicaoCamera()
*Descrição: Rotaciona e Posiciona a Camera em um novo lugar com animação de deslocamento
*Programador(a): Ighor Drummond
*Data: 16/05/2024
*/
function transicaoCamera(x, y, z, rx, ry, rz) {
    const inicio = {
        x: camera.position.x,
        y: camera.position.y,
        z: camera.position.z,
        rx: camera.rotation.x,
        ry: camera.rotation.y,
        rz: camera.rotation.z
    };

    const destino = { x, y, z, rx, ry, rz };

    new TWEEN.Tween(inicio)
        .to(destino, 500)
        .easing(TWEEN.Easing.Quadratic.InOut)
        .onUpdate(() => {
            camera.position.set(inicio.x, inicio.y, inicio.z);
            camera.rotation.set(inicio.rx, inicio.ry, inicio.rz);
        })
        .onComplete(() => {
            camera.position.set(x, y, z);
            camera.rotation.set(rx, ry, rz);
        })
        .start();
}
