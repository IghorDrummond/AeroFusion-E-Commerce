//Elementos
var quantidade = document.getElementById('quantidade');
var quantidade_texto = document.getElementById('quantidade_texto');
var preco = document.getElementById('preco');
var parcela = document.getElementById('parcela');
var aviso = document.getElementsByClassName('alertas');
var aviso_texto = document.getElementsByClassName('alerta_texto');
var carregamento = document.getElementById('tela_carregamento');
var tamTargetAnt = null;
//Numerico
var quantAnt = 1;
var precoFixado = 0.0;
var tamAnt = 0;
var Tam = 0;
// Objeto
var ajax = null;
//-----Escopo
quantidade.value = 1;
precoFixado = preco.innerText.replace(",", ".");
precoFixado = parseFloat(precoFixado.substr(precoFixado.indexOf('$') +1, precoFixado.length));
//-----Enventos
quantidade.addEventListener('input', ()=>{
    quantidade_texto.innerText = 'Quant: ' + quantidade.value;

    //Reajusta preço para quantidade escolhida
    total = (parseInt(quantidade.value) * precoFixado);
   // preco.innerText = "R$" + total.toFixed(2).toString();
    preco.innerText = total.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }).toString();
    //Reajusta parcela para novo valor
    parcela.innerText = (total/12).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }).toString();
});
//-----Funções
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
Descrição: Seleciona Tamanho do Tênis
Data: 29/05/2024
Programador: Ighor Drummond
*/
function escolheTam(NumTam){  
    Tam = NumTam;

    if(tamTargetAnt){
        tamTargetAnt.classList.remove('bg-warning');
        tamTargetAnt.classList.remove('text-white');
    }
    event.target.classList.add('bg-warning');
    event.target.classList.add('text-white');
    tamTargetAnt = event.target;
}
/*
Função: maisDetalhes(Numero do Produto)
Descrição: Seleciona Tamanho do Tênis
Data: 29/05/2024
Programador: Ighor Drummond
*/
function comprar(Prod){  
    if(Tam > 0){
        telaCarregamento(true);
        //Estancia o ajax
        ajax = new XMLHttpRequest();
        //Passa requisição via URL
        ajax.open('GET', 'script/carrinho.php?Prod=' + encodeURIComponent(Prod) + '&Tam=' + encodeURIComponent(Tam) + '&Quant=' + encodeURIComponent(quantidade.value) + '&Opc=2');
        //Valida cada status
        ajax.onreadystatechange = ()=>{
            telaCarregamento(false);
            if(ajax.readyState === 4){
                if(ajax.status < 400){
                    $('#carrinho').load('script/carrinho.php?Opc=1');
                    switch(ajax.responseText.trim()){
                        case 'LOGIN':
                            alerta('Para comprar este item, você precisa está logado primeiro!', 0);
                            break;
                        case 'ERROR':
                            alerta('Houve um erro interno em nosso servidor!', 0);
                            break;
                        case 'PRODUTO':
                            alerta('O produto não foi adicionado pois pode não ter mais a quantidade desejada no estoque ou não está mais disponivel.', 0);
                            break;
                        case 'CARRINHO':
                            alerta('O produto já foi adicionado ao carrinho, para adicionar mais quantidades, acesse seu carrinho.', 0);
                            break;
                        case 'NAOADICIONADO':
                            alerta('Houve um erro ao adicionar seu produto ao carrinho, tente novamente ou mais tarde', 0);
                            break;
                        case 'OK':
                            alerta('Produto adicionado ao carrinho!', 1);
                            break;
                    }
                }else{
                    window.location.href = 'error.php?Error=' + encodeURIComponent(ajax.status);
                }
            }
        }
        //Solicita Requisição
        ajax.send();
    }else{
        alerta('Insira um tamanho.', 0);
    }
}
