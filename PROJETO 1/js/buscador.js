//Declaração de variaveis
//Elementos
var checkboxes = document.querySelectorAll('input[type="checkbox"]');
//Array
var Valores = ['', '', '', ''];
var param = ['preco', 'data', 'categoria', 'tamanho'];

//----------------Eventos
/*
*Evento: DOMContentLoaded
*Descrição: Ajusta o tamanho do quadro 3d caso houver alteração de tamanho da página
*Programador(a): Ighor Drummond
*Data: 25/05/2024
*/
document.addEventListener('DOMContentLoaded', function () {
    const checkboxes = document.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach((checkbox, indice) => {
        checkbox.addEventListener('change', function () {
            if (this.checked) {
                const NomeCheck = document.querySelectorAll(`input[name="${this.name}"]`);
                NomeCheck.forEach((outroCheck, indice) => {
                    if (outroCheck !== this) {
                        outroCheck.checked = false;
                    }else{
                        guardaValor(indice, this.name);
                    }
                });
            }else{
                //Remove valor caso o input for o mesmo selecionado
                let posic = param.indexOf(this.name);
                if(Valores[posic] === this.value){
                    Valores[posic] = '';
                }
            }
        });
    });
});
/*
*Função: guardaValor(indice do input, nome do input)
*Descrição: Guardar Valores
*Programador(a): Ighor Drummond
*Data: 25/05/2024
*/
function guardaValor(i, nome){
    let Valor = document.getElementsByName(nome);
    switch(nome){
        case 'preco':
            //Valores[0] = Valor[i].value;
            Valores[0] = Valor[i].value;
            break;
        case 'data':
            Valores[1] = Valor[i].value;
            break;
        case 'categoria':
            Valores[2] = Valor[i].value;
            break;
        case 'tamanho':
            Valores[3] = Valor[i].value;
            break; 
    }
}
/*
*Função: filtrar
*Descrição: responsavel por filtrar os produtos escolhido pelo usuário
*Programador(a): Ighor Drummond
*Data: 25/05/2024
*/
function filtrar(){
    let parametros = '';

    telaCarregamento(true);

    Valores.forEach((Valor, i) =>{
        parametros += param[i] + '=' + Valor + '&';
    });

    parametros = parametros.substr(0, parametros.length -1);
    
    $("#exibeProdutos").load('../AeroFusion/filtrar.php?' + parametros, ()=>{
        telaCarregamento(false);
    });
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