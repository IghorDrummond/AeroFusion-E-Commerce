//Definindo Variaveis Globais
//Objetos
var cena = null;
var camera = null;
var renderizacao = null;
var carregador = null;
var animacaoClip = null;
var acaoFrame = null;
var modelo = null;
var acaoFrame = null;
var mixagem = null;
var Luz = null;
var DirLuz = null;
var cenario = null;
var controles = null;
var ceuMaterial = null;
var ceuGeometria = null;
var ceu = null;
//Elementos
var Quadro = document.getElementById('Quadro');
//Constantes
const ROTATIONX = -2.9299467403696067;
const ROTATIONY = 0.1444832404270051;
const ROTATIONZ = 3.1106662057012726;
const POSITIONX = 0.10804110146665466;
const POSITIONY = 1.4711142698265924;
const POSITIONZ = -1.5729460064618468;

//-------------Eventos
/*
*Evento: resize()
*Descrição: Ajusta o tamanho do quadro 3d caso houver alteração de tamanho da página
*Programador(a): Ighor Drummond
*Data: 20/05/2024
*/
window.addEventListener('resize', () => {
    camera.aspect = Quadro.clientWidth / Quadro.clientHeight;
    camera.updateProjectionMatrix();
    renderizacao.setSize(Quadro.clientWidth, Quadro.clientHeight);
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
*Data: 20/05/2024
*/
function configuracaoCena(){
    //Configurando a Cena, camera e renderizando o tamanho do quadro
    cena = new THREE.Scene();
    //Ajusta a pesperctiva da camera
    camera = new THREE.PerspectiveCamera(75, Quadro.clientWidth / Quadro.clientHeight, 0.1, 1000);
    camera.position.set(POSITIONX, POSITIONY, POSITIONZ); // Posiciona a câmera para a esquerda do personagem
    camera.rotation.set(ROTATIONX, ROTATIONY, ROTATIONZ);//Posiciona a câmera na rotação do personagem
    renderizacao = new THREE.WebGLRenderer({ antialias: true });//Ativa o Anti-Aliasing
    //Seta configurações do quadro no renderizador
    renderizacao.setSize(Quadro.clientWidth, Quadro.clientHeight);
    //Adiciona renderização ao elemento HTML
    Quadro.appendChild(renderizacao.domElement);
}
/*
*Função: configuaracaoLuz()
*Descrição: Configura a luz do ambiente
*Programador(a): Ighor Drummond
*Data: 20/05/2024
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
*Data: 20/05/2024
*/
function configuracaoObj3D(){
    //Inicia o objeto para receber o glb 3d
    carregador = new THREE.GLTFLoader();
    //Irá carregar o objeto 3d setando algumas propriedades
    carregador.load('3Ds/Disco voador.glb', (gltf)=>{
        modelo = gltf.scene;//Modelo recebe a cena
        modelo.position.set(0, 0.6, 0);
        // Adicionando mixagem para controlar a animação
        mixagem = new THREE.AnimationMixer(modelo);
        // Seleciona a primeira animação do array de animações
        animacaoClip = gltf.animations[2];
        acaoFrame = mixagem.clipAction(animacaoClip);//Seta animação ao objeto
        acaoFrame.play();//Renderiza em frames a animação
        cena.add(modelo);
        //controles = new THREE.OrbitControls(camera, renderizacao.domElement);
        function animacao(){
            requestAnimationFrame(animacao);//Recebe função para animar
            const deltaTime = 0.01;
            mixagem.update(deltaTime); // Atualiza a animação
            //controles.update();//atualiza a movimentação da camera
            //console.log('Posição da câmera:', camera.position);
            //console.log('Rotação da câmera:', camera.rotation);
            renderizacao.render(cena, camera); //Começa a renderizar o objeto no quadro 
            
        }
        animacao();

    }); 
}
/*
*Função: configuaracaoCenario()
*Descrição: Adiciona um cenário e um céu ao fundo
*Programador(a): Ighor Drummond
*Data: 20/05/2024
*/
function configuracaoCenario() {
    //Construindo um céu
    ceuGeometria = new THREE.SphereGeometry(500, 32, 32); // Tamanho grande o suficiente para envolver toda a cena
    ceuMaterial = new THREE.MeshBasicMaterial({
        map: new THREE.TextureLoader().load('img/estrelas.jpg'), // Cor azul clara para o céu
        side: THREE.BackSide // Para que o material seja visível no lado de dentro da esfera
    });
    ceu = new THREE.Mesh(ceuGeometria, ceuMaterial);
    cena.add(ceu);

    // Carregar e adicionar prédios
    carregador = new THREE.GLTFLoader();
    carregador.load('3Ds/Diorama na floresta perene.glb', function (gltf) {
        var Vegetacao = gltf.scene;
        Vegetacao.rotation.y = 360;
        Vegetacao.scale.set(30, 30, 30); // Define a escala do prédio conforme necessário
        Vegetacao.position.set(-0.2, -1 , 0.2); // Posicione o primeiro prédio ao lado do personagem
        cena.add(Vegetacao);
    });

    // Carregar e adicionar prédios
    carregador = new THREE.GLTFLoader();
    carregador.load('3Ds/Vaca.glb', function (gltf) {
        var Vaca = gltf.scene;
        Vaca.rotation.y = 300;
        Vaca.scale.set(0.1, 0.1, 0.1); // Ajuste conforme necessário
        Vaca.position.set(0, 0.8 ,0); // Posicione o primeiro prédio ao lado do personagem
        cena.add(Vaca);

        function balancaVaca(){
            requestAnimationFrame(balancaVaca);
            Vaca.rotation.y += 0.01;
            renderizacao.render(cena, camera);
        }

        balancaVaca();
    });
}