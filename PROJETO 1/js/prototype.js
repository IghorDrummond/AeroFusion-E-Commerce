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
var predios = [];
//Elementos
var carousel = document.getElementById('carouselExampleIndicators');
var quadro = document.getElementById('quadro');
//Numerico
var AntAnima = 0;
var nAnima = 0;
var velocidade = 0.01;
//Constantes
const ROTATIONX = -1.076570724955071;
const ROTATIONY = -1.4848722955694649;
const ROTATIONZ = -1.0750257452452618;
const POSITIONX = -0.5488504791185305;
const POSITIONY = 0.28141763142527265;
const POSITIONZ = 0.062148339544222636;
//----------------Evento
window.addEventListener('resize', () => {
    camera.aspect = carousel.clientWidth / carousel.clientHeight;
    camera.updateProjectionMatrix();
    renderizacao.setSize(carousel.clientWidth, carousel.clientHeight);
});
//----------------Escopo
configuracaoCena();//Responsavel por configurar o cenário
configuracaoLuz();//Responsavel por colocar a Luz no cenário
configuracaoObj3D();//Responsavel por adicionar o objeto 3d
configuracaoCenario();//Responsavel por criar o cenário animado
//----------------Funções
function configuracaoCena(){
    //Configurando a Cena, camera e renderizando o tamanho do quadro
    cena = new THREE.Scene();
    //Ajusta a pesperctiva da camera
    camera = new THREE.PerspectiveCamera(75, carousel.clientWidth / carousel.clientHeight, 0.1, 1000);
    camera.position.set( POSITIONX, POSITIONY, POSITIONZ); // Posiciona a câmera para a esquerda do personagem
    camera.rotation.set(ROTATIONX, ROTATIONY, ROTATIONZ);//Posiciona a câmera na rotação do personagem
    renderizacao = new THREE.WebGLRenderer({ antialias: true });//Ativa o Anti-Aliasing
    //Seta configurações do quadro no renderizador
    renderizacao.setSize(carousel.clientWidth, carousel.clientHeight);
    //Adiciona renderização ao elemento HTML
    quadro.appendChild(renderizacao.domElement);
}

function configuracaoLuz(){
    Luz = new THREE.AmbientLight(0xffffff, 0.5);
    cena.add(Luz); //adicionando Luz na scene

    //Aplicando uma direção para luz
    DirLuz = new THREE.DirectionalLight(0xffffff, 0.9);
    DirLuz.position.set(8, 100, 2);
    cena.add(DirLuz);
}

function configuracaoObj3D(){
    //Inicia o objeto para receber o glb 3d
    carregador = new THREE.GLTFLoader();
    //Irá carregar o objeto 3d setando algumas propriedades
    carregador.load('3Ds/Personagem skatista.glb', (gltf)=>{
        modelo = gltf.scene;//Modelo recebe a cena
        cena.add(modelo);
        //Defini a Escala vezes o tamanho do modelo no sentido x, y e z
        modelo.scale.set(8, 8, 8);
        //camera.lookAt(modelo.position); // Faz a câmera olhar para o personagem
        // Adicionando mixagem para controlar a animação
        mixagem = new THREE.AnimationMixer(modelo);
        //Adiciona um controle de Orbita para movimentação da camera
        //controles = new THREE.OrbitControls(camera, renderizacao.domElement);
        animacaoClip = gltf.animations[0];//Seleciona a Animação a ser animada
        acaoFrame = mixagem.clipAction(animacaoClip);//Seta animação ao objeto
        acaoFrame.play();//Renderiza em frames a animação 

        function animacao(){
            requestAnimationFrame(animacao);//Recebe função para animar
            mixagem.update(0.01); // Atualiza a animação
            renderizacao.render(cena, camera); //Começa a renderizar o objeto no quadro 
            //controles.update();//atualiza a movimentação da camera
            //console.log('Posição da câmera:', camera.position);
            //console.log('Rotação da câmera:', camera.rotation);
        }
        animacao();
    }); 
}

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

    // Adicionar o fundo da cidade
    var fundoGeometria = new THREE.BoxGeometry(10, 10, 10);
    var fundoMaterial = new THREE.MeshBasicMaterial({
        map: new THREE.TextureLoader().load('img/cidade.png'),
        side: THREE.BackSide
    });
    fundo = new THREE.Mesh(fundoGeometria, fundoMaterial);
    cena.add(fundo);
}

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
*Função: transicaoCamera(posições, rotações)
*Descrição: faz um deslocamento animado na camera usando posição e rotação
*Programador(a): Ighor Drummond
*Data: 16/05/2024

function transicaoCamera(x, y, z, rx, ry, rz){
    var xAnt = parseFloat((camera.position.x).toFixed(2));
    var yAnt = parseFloat((camera.position.y).toFixed(2));
    var zAnt = parseFloat((camera.position.z).toFixed(2));
    var rxAnt = parseFloat((camera.rotation.x).toFixed(2));
    var ryAnt = parseFloat((camera.rotation.y).toFixed(2));
    var rzAnt = parseFloat((camera.rotation.z).toFixed(2));

    intervalo = setInterval(()=>{  

        xAnt = retornaValor(xAnt, x);
        yAnt = retornaValor(yAnt, y);
        zAnt = retornaValor(zAnt, z);
        rxAnt = retornaValor(rxAnt, rx);
        ryaAnt = retornaValor(ryAnt, ry);
        rzAnt = retornaValor(rzAnt, rz);

        if(xAnt === parseFloat(x.toFixed(2))  && yAnt === parseFloat(y.toFixed(2)) && zAnt === parseFloat(z.toFixed(2)) && rxAnt == parseFloat(rx.toFixed(2)) && ryAnt === parseFloat(ry.toFixed(2)) && rzAnt === parseFloat(rz.toFixed(2))){
            clearInterval(intervalo);
        }else{
            camera.position.set(xAnt, yAnt, zAnt);
            camera.rotation.set(rxAnt, ryAnt, rzAnt);
        }
    }, 0.01);
}

function retornaValor(valor, comparacao){
    if(valor < parseFloat(comparacao.toFixed(2))){
        valor += 0.001;
    }else if(valor > parseFloat(comparacao.toFixed(2))){
        valor -= 0.001;
    } 

    return valor;
}*/