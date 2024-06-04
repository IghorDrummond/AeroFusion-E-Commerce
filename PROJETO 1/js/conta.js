//Declaração de Variaveis
//Elementos
var Input = document.getElementsByTagName('main')[0].getElementsByTagName('input');
var aviso = document.getElementsByClassName('alertas');
var aviso_texto = document.getElementsByClassName('alerta_texto');
var carregamento = document.getElementById('tela_carregamento');
var tela = document.getElementById('area_dados');
var camposTela = tela.getElementsByTagName('div');
var titulo = document.getElementsByTagName('title');
var telaInfo = document.getElementById('tela_info');
var cep = null;
var cpf = null;
var form = null;
//Objeto
var ajax = null
//String
var emaio = '';
//Array 
var mensagem = ['Pronto para dar aquele passo sneakerístico? Desate seus cadarços e venha conosco nessa jornada de estilo e conforto! 👟✨',
    'Está preparado para se cadastrar e dar um salto rumo ao estilo? Junte-se a nós e entre para o mundo sneaker com muito estilo e personalidade! 👟✨',
    'Descalce-se das preocupações! Se esqueceu a senha, não se aperte! Estamos aqui para dar aquele "boost" na sua segurança sneaker. 😉🔑'
];
var MemoEnd = [];
//Numerico
var telaTam = document.body.clientWidth;

//---------Funções
/*
Função: Logar()	
Descrição: Responsavel por validar se os Campos estão preenchidos
Data: 19/05/2024
Programador: Ighor Drummond
*/
function Logar(){
    if(Input[0].value != '' && Input[1].value != ''){
        requisitar();
    }else{
        alerta('Por favor, Insira os dados corretamente!', 0);
    }
}
/*
Função: requisitar()	
Descrição: Responsavel por validar login do usuário
Data: 19/05/2024
Programador: Ighor Drummond
*/
function requisitar(){
    ajax = new XMLHttpRequest();//Iniciando ajax
    //Tela de Carregamento
    telaCarregamento(true);

    ajax.open('GET', 'script/logar.php?Email='+ encodeURIComponent(Input[0].value) + '&Senha='+ encodeURIComponent(Input[1].value));//Requisita fonte
    
    ajax.onreadystatechange = () =>{
        if(ajax.readyState === 4){
            //Remove Tela de carregamento
            telaCarregamento(false);

            if(ajax.status >= 400){
                window.location.href = 'error.php?Error=' + ajax.status.toString();
            }else{
                if(ajax.responseText === 'Senha'){
                    Input[0].style.border = '1px solid red';
                    Input[1].style.border = '1px solid red';    
                    alerta('Email ou Senha Incorretos.', 0);//Ativa alerta              
                }else if(ajax.responseText === 'NaoExiste'){
                    Input[0].style.border = '1px solid red';
                    Input[1].style.border = '1px solid red';     
                    alerta('Email não Cadastrado!', 0);//Ativa alerta    
                }else{
                    window.location.href = 'index.php';
                }
            }
        }
    }

    ajax.send();//Faz Requisição
}
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
Função: cadastrar()
Descrição: Responsavel por ligar a tela de cadastrar
Data: 19/05/2024
Programador: Ighor Drummond
*/
function cadastrar(){
    titulo[0].innerText = 'AeroFusion - Cadastrar';//Atualiza o titulo da página

    //Valida se o usuário atual está operando em um dispositivo Desktop
    if(telaTam > 768){
        tela.style.animation = ' 2s transicionar';
        telaInfo.style.animation = '2s transicionarinverso';

        var Y = setTimeout(()=>{
            tela.style.transform = ' translateX(-100%)';
            telaInfo.style.transform = ' translateX(100%)';
            telaInfo.getElementsByTagName('h1')[0].innerText = mensagem[1];//Insere a nova mensagem para tela de cadastro
            clearTimeout(Y);
        }, 2000);
    }
    //Percorre o Objeto usando forEach
    Object.keys(camposTela).forEach(function(key) {
      camposTela[key].style.animation = '1s sumir';
    });
    //Some com os campos setando display none
    var Z = setTimeout(()=>{
        Object.keys(camposTela).forEach(function(key) {
          camposTela[key].style.display = 'none';
        });
        requisitarPagina('htmlCadastrar.php');
        clearTimeout(Z);
    }, 1000);
}
/*
Função: login()
Descrição: Responsavel por carregar tela de Login
Data: 19/05/2024
Programador: Ighor Drummond
*/
function login(){
    titulo[0].innerText = 'AeroFusion - Login';//Atualiza o titulo da página

    //Valida se o usuário atual está operando em um dispositivo Desktop
    if(telaTam > 768){
        tela.style.animation = ' 2s transicionarVoltar';
        telaInfo.style.animation = '2s transicionarinversoVoltar';

        var Y = setTimeout(()=>{
            tela.style.transform = 'translateX(0%)';
            telaInfo.style.transform = ' translateX(0%)';
            telaInfo.getElementsByTagName('h1')[0].innerText = mensagem[0];//Insere a nova mensagem para tela de cadastro
            clearTimeout(Y);
        }, 2000);
    }

    document.getElementsByTagName('pre')[0].style.animation ='2s sumir';
    //Some com os campos setando display none
    var P = setTimeout(()=>{
        document.getElementsByTagName('pre')[0].style.display ='none';//Desliga a tag pre
        requisitarPagina('htmlLogin.php');
        clearTimeout(P);
    }, 2000);

}
/*
Função: trocaSenha()
Descrição: Responsavel por carregar tela de Login
Data: 19/05/2024
Programador: Ighor Drummond
*/
function trocaSenha(){
    titulo[0].innerText = 'AeroFusion - Esqueci Senha';//Atualiza o titulo da página

    //Valida se o usuário atual está operando em um dispositivo Desktop
    if(telaTam > 768){
        tela.style.animation = ' 2s transicionar';
        telaInfo.style.animation = '2s transicionarinverso';

        var Y = setTimeout(()=>{
            tela.style.transform = ' translateX(-100%)';
            telaInfo.style.transform = ' translateX(100%)';
            telaInfo.getElementsByTagName('h1')[0].innerText = mensagem[2];//Insere a nova mensagem para tela de cadastro
            clearTimeout(Y);
        }, 2000);
    }
    //Percorre o Objeto usando forEach
    Object.keys(camposTela).forEach(function(key) {
      camposTela[key].style.animation = '1s sumir';
    });
    //Some com os campos setando display none
    var Z = setTimeout(()=>{
        Object.keys(camposTela).forEach(function(key) {
          camposTela[key].style.display = 'none';
        });
        requisitarPagina('htmlEsquecisenha.php');
        clearTimeout(Z);
    }, 1000);

}
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
Função: ajustaTam()
Descrição: Responsavel por capturar tamanho da tela caso a mesma for ajustada
Data: 19/05/2024
Programador: Ighor Drummond
*/
function ajustaTam(){
    telaTam = document.body.clientWidth;
}
/*
Função: requisitarPagina(nome do arquivo php a ser recuperado)
Descrição: Responsavel por requisitar páginas pelo ajax
Data: 19/05/2024
Programador: Ighor Drummond
*/
function requisitarPagina(pagina){
    ajax = new XMLHttpRequest();//Inicia Requisição
    //Configura ajax para puxar arquivo php
    ajax.open('GET', 'script/HTML/' + pagina);
    //Passa por cada estado retornado
    ajax.onreadystatechange = () =>{
        if(ajax.readyState === 4){
            if(ajax.status >= 400){
                window.location.href = 'error.php?Error=' + ajax.status.toString();
            }else{
                area_dados.innerHTML = ajax.responseText;//Carrega Dados dentro do campo solicitado
            }
        }
    }
    //Solicita Inicio do Pedido Get
    ajax.send(); 
}
/*
Função: cadastrarUsuario()
Descrição: Responsavel por cadastrar o usuário em nosso banco de dados
Data: 20/05/2024
Programador: Ighor Drummond   
*/
function cadastrarUsuario(){
    var Campos =  area_dados.getElementsByTagName('input');
    var parametroObj = {
        Email: Campos[0].value.trim(),
        Nome: Campos[1].value.trim(),
        Sobrenome: Campos[2].value.trim(),
        Data: Campos[3].value.trim(),
        Celular: (Campos[4].value.trim()).replace(/[()-/\s+/g]/g, ''),
        Senha: Campos[5].value.trim(),
        ConfirmaSenha: Campos[6].value.trim(),
        Sexo: selecao(),
        Cep: (Campos[7].value.trim()).replace('-', ''),
        Cidade: Campos[8].value.trim(),
        Estado: Campos[9].value.trim(),
        Rua: Campos[10].value.trim(),
        Bairro: Campos[11].value.trim(),
        Numero: Campos[12].value.trim(),
        Complemento: Campos[13].value.trim(),
        Referencia: Campos[14].value.trim(),
        Cpf: (Campos[15].value.trim()).replace(/[.-]/g, '')
    };
    var paramString = '';
    var End = validaEnd(parametroObj);

    event.preventDefault();//Cancela o Carregamento do Submit
    //Valida se o sexo foi cadastrado
    if(parametroObj.Sexo === ''){
        alerta('Insira o gênero', 0);
        return null;
    }else if(End != ''){
        alerta('Endereço Invalido com o Cep: ' + End, 0);
        return null;
    }
    //Ativa Tela de Carregamento 
    telaCarregamento(true);
    //Converte Dados de String para formatos de URL Parametros
    for(var Campo in parametroObj){
        paramString += '&' + encodeURIComponent(Campo) + '=' + encodeURIComponent(parametroObj[Campo]);
    } 
    //Retira o Primeiro &
    paramString = paramString.substr(paramString.indexOf('&') + 1, paramString.length);
    //Chama a página de cadastro passando os dados por parametros
    ajax = new XMLHttpRequest();//Inicia Requisição
    //Configura ajax para puxar arquivo php
    ajax.open('GET', 'script/cadastrar.php?' + paramString, true);
    //Passa por cada estado retornado
    ajax.onreadystatechange = () =>{
        if(ajax.readyState === 4){
            //Desativa tela de carregamento
            telaCarregamento(false);
            if(ajax.status >= 400){
                window.location.href = 'error.php?Error=' + ajax.status.toString();
            }else{
                //Retorna a Operação Executada
                switch(ajax.responseText.trim()){
                    case 'EMAIL':
                        alerta('A conta já existe em nosso banco de dados!', 0);
                        break;
                    case 'DATA':
                        alerta('Você é menor de 18 anos, não pode comprar!', 0);
                        break;
                    case  'SENHA':
                       alerta('As Senhas não se correspondem!', 0);
                        break;
                    case 'CPF':
                        alerta('CPF Invalido!', 0);
                        break;
                    case 'OK':
                        login();
                        alerta('Cadastrado com Sucesso', 1);
                        break;
                    default:
                        alerta('Houve um erro interno em nosso servidor!Error: ' + ajax.responseText, 0);
                        break;
                }
            }
        }
    }
    //Solicita Inicio do Pedido Get
    ajax.send(); 
}
/*
Função: buscaEndereco()
Descrição: Responsavel por buscar cep da cidade
Data: 20/05/2024
Programador: Ighor Drummond   
*/
function buscaEndereco(){
    var cep = document.getElementsByName('Cep')[0].value;
    var End =  document.getElementsByTagName('fieldset')[1].getElementsByTagName('input');
    var jsonHttp = null;

    if(cep.length === 9){
        jsonHttp = new XMLHttpRequest();
        jsonHttp.open('GET', 'https://viacep.com.br/ws/'+ (cep.replace('-', '')) +'/json/');

        jsonHttp.onreadystatechange = ()=>{
            if(jsonHttp.readyState === 4 && jsonHttp.status < 400){
                let jsonVal = JSON.parse(jsonHttp.responseText);

                if(!jsonVal.hasOwnProperty('erro')){
                    End[1].value = jsonVal.localidade;
                    End[2].value = jsonVal.uf;
                    End[3].value = jsonVal.logradouro;
                    End[4].value = jsonVal.bairro;
                }

                for(nCont = 0; nCont <= 3; nCont++){
                    MemoEnd[nCont] = End[(nCont + 1)].value;
                }
            }
        }

        jsonHttp.send();
    }
}
/*
Função: mascaraCep()
Descrição: Mascara automatica de CEP
Data: 20/05/2024
Programador: Ighor Drummond   
*/
function mascaraCep(){
    var cep = document.getElementsByName('Cep')[0];
    var aux = '';

    for(nCont = 0; nCont <= cep.value.length-1; nCont++){
        if(nCont === 5 && cep.value.substr(nCont, 1) != '-'){
            aux += '-';
        }

        aux += cep.value.substr(nCont, 1);
    }

    cep.value = aux;
}
/*
Função: mascaraCpf()
Descrição: Mascara Automatica de CPF 
Data: 20/05/2024
Programador: Ighor Drummond   
*/
function mascaraCpf(){
    var cpf = document.getElementsByName('Cpf')[0];
    var aux = '';

    for(nCont = 0; nCont <= cpf.value.length-1; nCont++){
        if(nCont === 3 || nCont === 7){
            if(cpf.value.substr(nCont, 1) != '.'){
                aux += '.';
            }
        }else if(nCont === 11){
            if(cpf.value.substr(nCont, 1) != '-'){
                aux += '-';
            }            
        }

        aux += cpf.value.substr(nCont, 1);
    }

    cpf.value = aux;
}
/*
Função: mascaraCel()
Descrição: Mascara Automatica de Celular
Data: 21/05/2024
Programador: Ighor Drummond   
*/
function mascaraCel(){
    var cel = document.getElementsByName('Celular')[0];
    var aux = '';
    var caracter = '';
    var nCont = 0;

    for(nCont = 0; nCont <= cel.value.length-1; nCont++){
        caracter = cel.value.substr(nCont, 1);
        if(nCont === 0 && caracter != '('){
            aux += '(';
        }else if(nCont === 3 && caracter != ')'){
            aux += ')';
        }else if(nCont === 4 && caracter != ' '){
            aux += ' ';
        }else if(nCont === 10 && caracter != '-'){
            aux += '-';
        }

        aux += caracter;
    }

    cel.value = aux;
}
/*
Função: selecao()
Descrição: Verifica se o foi inserido o gênero
Data: 21/05/2024
Programador: Ighor Drummond   
*/
function selecao(){
    var select = document.getElementsByTagName('select')[0];

    if(select.value != 'Selecione um gênero'){
        return select.value;
    }

    return '';
}
/*
Função: validaEnd(Endereço em formato Objeto)
Descrição: Verifica se o foi inserido o gênero
Data: 21/05/2024
Programador: Ighor Drummond   
*/
function validaEnd(endObj){
    var ret = '';

    if(MemoEnd[0] != endObj.Cidade){
       ret += 'Cidade - ';
    }
    if(MemoEnd[1] != endObj.Estado){
       ret += 'Estado - ';
    }
    if(MemoEnd[2] != endObj.Rua){
       ret += 'Rua/Avênida - ';
    }
    if(MemoEnd[3] != endObj.Bairro){
       ret += 'Bairro - ';       
    }

    return ret;
}
/*
Função: enviarEmail()
Descrição: Envia um Email para recuperar a senha do usuário
Data: 21/05/2024
Programador: Ighor Drummond   
*/
function enviarEmail(){
    email = document.getElementsByName('Email')[0].value.trim();
    ajax = new XMLHttpRequest();

    ajax.open('GET', 'script/esqueciSenha.php?Email=' + encodeURIComponent(email));
    //Ativa tela de carregamento
    telaCarregamento(true);

    ajax.onreadystatechange = () =>{
        if(ajax.readyState === 4){
            if(ajax.status < 400){
                //desativa tela de carregamento
                telaCarregamento(false);
                switch(ajax.responseText.trim()){
                    case 'OK':
                        alerta('Enviado email com sucesso! Lembre-se de checar sua caixa de email ou spam.', 1);
                        requisitarPagina('htmlTrocarSenha.php');
                        break;
                    default:
                        email = '';
                        alerta('Houve um erro interno em nosso servidor! Error: ' + ajax.responseText);
                        break;
                }
            }else{
                window.location.href = 'error.php?Error=' + ajax.status;
            }
        }else{
            alerta('Houve um problema interno!' , 0);
        }
    }

    ajax.send();
}
/*
Função: validaCodigo
Descrição: Valida o código digitado pelo usuário
Data: 21/05/2024
Programador: Ighor Drummond   
*/
function validaCodigo(){
    codigo = document.getElementsByName('Codigo')[0].value.trim();

    event.preventDefault();//Cancela o Carregamento do Submit
    ajax = new XMLHttpRequest();
    ajax.open('GET', 'script/validaCodigo.php?Codigo=' + encodeURIComponent(codigo)+ '&Email=' + encodeURIComponent(email));
    //Ativa tela de carregamento
    telaCarregamento(true);

    ajax.onreadystatechange = () =>{
        if(ajax.readyState === 4){
            if(ajax.status < 400){
                //desativa tela de carregamento
                telaCarregamento(false);
                //Caso não direcionar para página de troca de senha, ele estora um alerta de erro invalidando o código
                switch(ajax.responseText.trim()){
                    case 'OK':
                        window.location.href = 'trocaSenha.php';
                        break;
                    case 'DATA':
                        alerta('Código  com data  ultrapassada! gere um novo para trocar a senha.', 0);
                        break;
                    case 'CODIGO':
                        alerta('Código Invalido!', 0);
                        break;
                    default:
                        alerta('Houve um erro interno em nosso servidor, Tente novamente ou mais tarde.', 0);
                        break;
                }
            }else{
                window.location.href = 'error.php?Error=' + ajax.status;
            }
        }
    }

    ajax.send();
}