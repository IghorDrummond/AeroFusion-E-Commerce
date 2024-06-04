//Declaração de variaveis
//Elementos
var aviso = document.getElementsByClassName('alertas');
var aviso_texto = document.getElementsByClassName('alerta_texto');
//String
var Senha = document.getElementsByName('Senha')[0];
var ConfirmeSenha = document.getElementsByName('ConfirmeSenha')[0];
//Objeto
var ajax = null;

/*
Função: trocarSenha()
Descrição: Responsavel por enviar a nova senha ao banco de dados
Data: 22/05/2024
Programador: Ighor Drummond
*/
function trocarSenha(){
	event.preventDefault();//Retira carregamento da página

	ajax = new XMLHttpRequest();

	ajax.open('GET', 'script/renovarSenha.php?Senha=' + encodeURIComponent(Senha.value)  + '&ConfirmeSenha=' + encodeURIComponent(ConfirmeSenha.value));
	telaCarregamento(true);

	ajax.onreadystatechange = () =>{
		if(ajax.readyState === 4){
			telaCarregamento(false);
			if(ajax.status < 400){
				switch(ajax.responseText.trim()){
					case 'OK':
						alerta('Senha Trocada com sucesso! Redirecionando você para página inicial...', 1);
						encaminhaPagina();
						break;
					case 'SENHAS':
						alerta('Senha Incorretas!', 0);
						break;
					case 'ERROR':
						alerta('Houve um erro interno em nosso servidor!', 0);
						break;
				}
			}else{
				window.location.href = 'error.php?Error=' + ajax.status;
			}
		}
	}

	ajax.send();
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
Função: encaminhaPagina()
Descrição: Responsavel por devolver o usuário para página principal
Data: 22/05/2024
Programador: Ighor Drummond
*/
function encaminhaPagina(){
	var Y = setTimeout(()=>{
		window.location.href= 'index.php';
		clearTimeout(Y);
	}, 5000);
}