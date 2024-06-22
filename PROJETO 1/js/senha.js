//Declaração de variavel
//Elemento
var lista = document.getElementById('confereSenha')?.getElementsByTagName('li');
//String
var Senha = document.getElementsByName('Senha')[0];
var Caracteres = [['ABCDEFGHIJKLMNOPQRSTUVWXYZ'], ['0123456789'] , ['@#_-%¨&;?!$()*><:Ç~´^,.=\/{}`´|[]+""£0'] ];

//-----------Funções
function senhaValid(){
	var nCont = 0;
	var nCont2 = 0;
	var Aux = '';

	if(typeof(lista) === 'undefined'){
		lista = document.getElementById('confereSenha')?.getElementsByTagName('li');
		Senha = document.getElementsByName('Senha')[0];
	}

	if(Senha.value.length >= 8){
		lista[0].classList.add('text-success');
	}else{
		lista[0].classList.remove('text-success');
	}

	for(nCont = 0;  nCont <= Caracteres.length -1; nCont++){
		for(nCont2 = 0; nCont2 <= Caracteres[nCont][0].length -1; nCont2++){
			Aux = Caracteres[nCont][0].substr(nCont2, 1);

			if(Senha.value.indexOf(Aux) >= 0){
				lista[nCont +1].classList.add('text-success');
				break;
			}else if(nCont2 === Caracteres[nCont].length -1 && Senha.value.indexOf(Aux) < 0){
				lista[nCont +1].classList.remove('text-success');
			}
		}
	}
}
