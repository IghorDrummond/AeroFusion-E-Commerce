//Declaração de Variaveis
var ListaItem = document?.getElementsByClassName('itens');
var Artigos = document?.getElementsByTagName('article');
var Cartao = null;
//String
var nAntCard = '';
//Númerico
var antLado = 0;
//Array
var regraNumero = {
    mastercard: /^5[1-5][0-9]{14}$/,
    elo: /^(4011|4312|4514|4573|5041|5066|509|6277|6362|6363|650|6550)/,
    amex: /^3[47][0-9]{13}$/,
    discover: /^6(?:011|5[0-9]{2})[0-9]{12}$/,
    diners: /^3(?:0[0-5]|[68][0-9])[0-9]{11}$/,
    jcb: /^(?:2131|1800|35\d{3})\d{11}$/,
    jcb15: /^(?:2131|1800)\d{11}$/,
    maestro: /^(5[06789]\d\d|6\d{3})\d{8,15}$/,
    unionplay: /^(62|88)/,
    visa: /^4[0-9]{12}(?:[0-9]{3})?$/,
};
var imagem = [
    'https://upload.wikimedia.org/wikipedia/commons/thumb/2/2a/Mastercard-logo.svg/618px-Mastercard-logo.svg.png',
    'https://seeklogo.com/images/E/elo-logo-0B17407ECC-seeklogo.com.png',
    'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ-U8tK4EfgFz0FAX0yYoXfE05yWfq2tqNLQw&s',
    'https://www.discoversignage.com/uploads/09-12-21_04:20_DGN_AcceptanceMark_FC_Hrz_RGB.png',
    'https://seeklogo.com/images/D/diners-club-logo-E375570397-seeklogo.com.png',
    'https://upload.wikimedia.org/wikipedia/commons/thumb/4/40/JCB_logo.svg/1280px-JCB_logo.svg.png',
    'https://upload.wikimedia.org/wikipedia/commons/thumb/4/40/JCB_logo.svg/1280px-JCB_logo.svg.png',
    'https://seeklogo.com/images/M/Maestro-logo-333A576204-seeklogo.com.png',
    'https://upload.wikimedia.org/wikipedia/commons/thumb/1/1b/UnionPay_logo.svg/1280px-UnionPay_logo.svg.png',
    'https://w7.pngwing.com/pngs/49/82/png-transparent-credit-card-visa-logo-mastercard-bank-mastercard-blue-text-rectangle.png'
];
//Booleano
var lGira = false;
//Objeto
var dados = {
    bandeira: 0,
    nome: '',
    validade: '',
    cvv: '',
    numero: '',
    parcelamento: '1'
}
var ajax = null;
//Constantes
const BandeiraClasses = ("mastercard;elo;amex;discover;diners;jcb;jcb15;maestro;unionpay;visa").split(';');

//--------------------------Eventos
document.addEventListener('DOMContentLoaded', function() {
    $('section').load('script/home_config.js?Opc=1', ()=>{
        let botoes = document.querySelectorAll('button[onclick^="ativarItens"]');
        botoes.forEach(function(botao) {
            let icone = botao.querySelector('i');
            if (icone) {
                icone.addEventListener('click', function(event) {
                    event.stopPropagation(); // Evita que o clique no ícone interfira no botão
                    botao.click(); // Dispara o evento de clique do botão
                });
            }
        });
    });
});
/*
* Evento: submit
* Descrição: Envia a nova imagem ao servidor após usuário inserir a imagem desejada
* Programador(a): Ighor Drummond
* Data: 31/07/2024
*/
$('#FormularioImagem').on('submit', function(e){
    e.preventDefault();

    let formData = new FormData();
    let arquivo = $("#arquivo")[0].files[0];

    formData.append('file', arquivo);
    if(arquivo){
        // Enviar a imagem via AJAX
        $.ajax({
            url: 'script/home_config_js.php?Opc=7',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                switch(response.trim()){
                    case 'TAMANHO':
                        alerta('Arquivo ultrapassa o limite de 500Kb.', 0);
                        break;
                    case 'FORMATO':
                        alerta('Arquivo não respeita o formato exigido.', 0);
                        break;
                    case 'SUCESSO':
                        alerta('Imagem atualizada com sucesso!', 1);
                        setTimeout(()=>{
                            location.reload(true);
                        }, 2000);
                        break;
                    case 'ARQUIVO':
                        alerta('Arquivo está corrompido.', 0);
                        break;
                    case 'ERRO':
                        alerta('Houve um erro ao tentar atualizar imagem.', 0);
                        break;
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alerta('Erro: ' + textStatus + ' - ' + errorThrown, 0);
            }
        });
    }
});
/*
* Evento: change
* Descrição: Carrega a imagem no img preview para o usuário analisar como irá ficar
* Programador(a): Ighor Drummond
* Data: 31/07/2024
*/
document.getElementById('arquivo').addEventListener('change', function(event) {
    let arquivo = event.target.files[0];

    if (arquivo && arquivo.type.startsWith('image/')) {
        // Exibir pré-visualização da imagem
        let reader = new FileReader();

        reader.onload = function(e) {
            const imagem_preview = document.getElementsByName('foto_perfil')[0];
            imagem_preview.src = e.target.result;
        };

        reader.readAsDataURL(arquivo);
    } else {
        alerta('Por favor, selecione um arquivo de imagem.', 0);
    }
});

//--------------------------Funções
/*
Função: ativarItens(posic, event)
Descrição: Responsavel 
Data: 19/07/2024
Programador: Ighor Drummond
*/
function ativarItens(posic, event){
    event.stopPropagation();

    if(ListaItem){
        let icone = event.target.getElementsByTagName('i')[0];

        if (icone.classList[1] === 'fa-chevron-down') {
            // Ativa a Lista do Item
            ListaItem[posic].classList.remove('d-none');
            // Troca ícone
            icone.classList.remove('fa-chevron-down');
            icone.classList.add('fa-chevron-up');
        } else {
            // Desativa a Lista do Item
            ListaItem[posic].classList.add('d-none');
            // Troca ícone
            icone.classList.remove('fa-chevron-up');
            icone.classList.add('fa-chevron-down');
        }
    }
}
/*
Função: prosseguirPedido(Ped)
Descrição: Responsavel por continuar o pedido para pagamento
Data: 19/07/2024
Programador: Ighor Drummond
*/
function prosseguirPedido(Ped){
    $('table').load('script/pedidos.php?Opc=15&Ped=' + Ped.toString(), ()=>{
        window.location.href = "pagamento.php";
    });
}
/*
Função: cancelaPedido(Ped)
Descrição: Responsavel por cancelar o Pedido desejado
Data: 19/07/2024
Programador: Ighor Drummond
*/
function cancelaPedido(Ped){
    if(confirm('Deseja realmente cancelar o pedido ' + Ped.toString() + '?')){
        $(Artigos[0]).load('script/pedidos.php?Opc=15&Ped=' + Ped.toString());
        $(Artigos[0]).load('script/pedidos.php?Opc=14', ()=>{
            attPagina(0, 2); 
        });   
    }
}
/*
Função: deletarFav(Id do Produto)
Descrição: Responsavel por deletar o produto do favorito
Data: 20/07/2024
Programador: Ighor Drummond
*/
function deletarFav(Prod){
    $(Artigos[1]).load('script/favoritar.php?Opc=0&Produto=' + encodeURIComponent(Prod), ()=>{
        attPagina(1, 3);   
    });
}
/*
Função: atualizaQuant(Operacao, Id do carrinho, Elemento do Html)
Descrição: Responsavel por aumentar ou diminuir a quantidade do item desejado
Data: 20/07/2024
Programador: Ighor Drummond
*/
function atualizaQuant(Opc, IdCar, element) {
    telaCarregamento(true);
    var btnGroup = element.parentElement;
    var Quantidade = btnGroup.querySelector('.quantidade_acao').textContent;

    //Faz a adição do valor novo valor
    if (Opc === '+') {
        Quantidade = (parseInt(Quantidade) + 1).toString();
    } else {
        if (parseInt(Quantidade) === 1) {
            deletaItem(IdCar);//Apaga do carrinho o produto
        } else {
            Quantidade = (parseInt(Quantidade) - 1).toString();
        }
    }
    $(Artigos[2]).load('script/pedidos.php?Opc=4&IdCar=' + encodeURIComponent(IdCar) + '&Quant=' + encodeURIComponent(Quantidade), ()=>{
        telaCarregamento(false);
        attPagina(3, 5);   
    });
}
/*
Função: deletaProdCar(Id do Carrinho)
Descrição: Responsavel por deletar o produto no carrinho
Data: 20/07/2024
Programador: Ighor Drummond
*/
function deletaProdCar(Car){
    deletaItem(Car);
    attPagina(3, 5);   
}
/*
Função: attPagina(Posição, Opção)
Descrição: Responsavel por atualizar o article responsável  
Data: 20/07/2024
Programador: Ighor Drummond
*/
function attPagina(Posic, Opc){
    $(Artigos[Posic]).load('script/home_config_js.php?Opc=' + Opc.toString());      
}
/*
Função: atualizaNome()
Descrição: Responsavel por atualizar o nome do usuário
Data: 31/07/2024
Programador: Ighor Drummond
*/
function atualizarNome(){
    event.preventDefault();
    var Nome = document.getElementsByName('nome')[0].value;
    $.ajax({
        url: 'script/home_config_js.php?Opc=8&Nome=' + encodeURIComponent(Nome),
        method: 'POST',
        success: function (response) {
            telaCarregamento(false);
            alerta('Nome alterado com sucesso!', 1);
            setTimeout(()=>{
                location.reload();
            }, 2000);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            telaCarregamento(false);
            alerta('Erro ao alterar o nome: ' + textStatus + errorThrown, 0);
        }
    });
}
/*
Função: virarCartao(direção do cartão)
Descrição: vira o cartão para posição correta
Data: 02/07/2024
Programador: Ighor Drummond   
*/
function virarCartao(estadoCard) {
    var graus = estadoCard === 1 ? 0 : 180;
    var opc = estadoCard === 1 ? '+' : '-';
    var limite = estadoCard === 1 ? 180 : 0;
    Cartao = document.getElementById('cartao');

    //Evita o cartão fica em um loop caso o botão for pressionado varias vezes
    if (lGira) {
        return null;
    }

    Z = setInterval(() => {
        lGira = true;
        graus = opc === '+' ? graus + 1 : graus - 1;
        Cartao.style.transform = 'rotateY(' + graus.toString() + 'deg)'
        if (graus === limite) {
            clearInterval(Z);
            lGira = false;
        } else {
            if (graus === 90) {
                Cartao.getElementsByClassName('cartaoLado')[antLado].classList.remove('d-flex');
                Cartao.getElementsByClassName('cartaoLado')[antLado].classList.add('d-none');
                Cartao.getElementsByClassName('cartaoLado')[estadoCard].classList.remove('d-none');
                Cartao.getElementsByClassName('cartaoLado')[estadoCard].classList.add('d-flex');
                antLado = estadoCard;
            }
        }
    }, 1);
}
/*
Função: bandeira(elemento)
Descrição: Aplica Bandeira ao cartão de crédito
Data: 03/07/2024
Programador: Ighor Drummond   
*/
function bandeira(element) {
    var numero = element.value;
    var operadora = document.getElementsByName('operadora')[0];
    var tipo = null;
    var pattern = null;
    var achado = false;

    //Remove máscara
    numero = numero.replace(/\D/g, '');
    //Validar qual bandeira é correspondente ao numero inserido
    for ([tipo, pattern] of Object.entries(regraNumero)) {
        if (pattern.test(numero)) {
            achado = true;
            break;
        }
    }
    //Aplica bandeira na imagem
    if (achado) {
        operadora.src = imagem[Object.keys(regraNumero).indexOf(tipo)];
        dados.bandeira = Object.keys(regraNumero).indexOf(tipo) + 1;
        dados.numero = numero;
        trocarCor(tipo);
    } else {
        trocarCor('');
        operadora.src = 'https://cdn-icons-png.flaticon.com/512/2695/2695969.png';
    }
}
/*
Função: trocarCor(Bandeira aplicada)
Descrição: troca cor do cartão
Data: 03/07/2024
Programador: Ighor Drummond   
*/
function trocarCor(tipo) {
    var Cartao = document.getElementById('cartao');
    //Troca fundo do cartão
    //Remove fundos anteriores caso tiver adicionado
    for (nCont = 0; nCont <= 9; nCont++) {
        Cartao.classList.remove(Object.keys(regraNumero)[nCont]);
    }
    //Adiciona fundo ao cartão
    if (tipo != '') {
        Cartao.classList.add(tipo);
    }
}
/*
Função: maskValidade(elemento)
Descrição: aplica máscara na validade do cartão
Data: 03/07/2024
Programador: Ighor Drummond   
*/
function maskValidade(element) {
    var validade = element;
    var validadeMask = '';

    for (nCont = 0; nCont <= (validade.value.length) - 1; nCont++) {
        if (nCont === 2 && validade.value[2] != '/') {
            validadeMask += '/';
        }
        validadeMask += validade.value[nCont];
    }

    validade.value = validadeMask;
    dados.validade = validadeMask;
}
/*
Função: maskNumero(elemento)
Descrição: aplica máscara no número do cartão
Data: 03/07/2024
Programador: Ighor Drummond   
*/
function maskNumero(element) {
    var numero = element;
    var numeroMask = '';

    numero.value = numero.value.replace(/\s+/g, '');

    if(numero.value.length > 16){
        numero.style.border = '2px solid yellow';
        return
    }

    numero.style.border = 'none';
     
    for (let nCont = 0; nCont < (numero.value.length); nCont++) {
        if (nCont > 0 && nCont % 4 === 0) {
            numeroMask += ' ';
        }
        numeroMask += numero.value[nCont];
    }

    numero.value = numeroMask;
}
/*
Função: cartaoSelecionado(Elemento selecionado)
Descrição: Seleciona um novo cartão
Data: 07/07/2024
Programador: Ighor Drummond   
*/
function cartaoSelecionado(element) {
    var elementoPai = element.parentElement;
    var Card = elementoPai.id;

    if (nAntCard === Card) {
        return null;
    }

    telaCarregamento(true);
    nAntCard = Card;
    $.ajax({
        url: 'script/pedidos.php?Opc=11&Card=' + encodeURIComponent(Card),
        type: 'POST',
        dataType: 'json',
        success: function (CardDate) {
            telaCarregamento(false);
            if (Object.values(CardDate).every(value => value)) {
                Cartao = document.getElementById('cartao');//Recebe o elemento Cartão
                trocarCor(CardDate[0].bandeira);//Troca Cor do Cartão
                Cartao.getElementsByTagName('img')[0].src = CardDate[0].img_ban;//Troca bandeira do cartão
                $('#nome_cartao').val(CardDate[0].nome_cartao);
                $('#vencimento').val(CardDate[0].validade_formatada);
                $('#numero_cartao').val(CardDate[0].numero_cartao);
                $('#cvv').val(CardDate[0].cvv);
                //Guarda Informações do cartão selecionado
                dados.nome = CardDate[0].nome_cartao;
                dados.validade = CardDate[0].validade_formatada;
                dados.numero = CardDate[0].numero_cartao;
                dados.cvv = CardDate[0].cvv;
                dados.bandeira = CardDate[0].bandeira;
            }
        },
        error: function (xhr, status, error) {
            telaCarregamento(false);
            alerta('Não foi possivel buscar o seu cartão no banco de dados. Tente novamente ou mais tarde', 0);
        }
    });
}
/*
Função: addCartao() 
Descrição: Adicionar Cartão
Data: 13/07/2024
Programador: Ighor Drummond   
*/
function addCartao() {
    //Recupera valores importante para validar dados
    if (Object.values(dados).every(value => value)) {
        telaCarregamento(true);
        $.ajax({
            url: 'script/pedidos.php',
            method: 'GET',
            data: {
                Opc: 13,
                bandeira: dados.bandeira,
                nome: dados.nome,
                validade: dados.validade,
                cvv: dados.cvv,
                numero: dados.numero
            },
            success: function (response) {
                telaCarregamento(false);
                alerta('Cartão cadastrado com sucesso!', 1);
                attPagina(4, 6);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                telaCarregamento(false);
                alerta('Erro ao adicionar Cartão: ' + textStatus + errorThrown, 0);
            }
        });
    } else {
        alerta('Falta preencher dados no cartão!', 0);
    }
}
/*
Função: guardaNome(element)
Descrição: Guarda nome do cartão
Data: 13/07/2024
Programador: Ighor Drummond   
*/
function guardaNome(element) {
    dados.nome = element.value;
}
/*
Função: guardaCvv(element)
Descrição: Guarda o Cvv do cartão
Data: 13/07/2024
Programador: Ighor Drummond   
*/
function guardaCvv(element) {
    dados.cvv = element.value;
}
/*
Função: deletarCartao(element)
Descrição: Deleta cartão desejado
Data: 31/07/2024
Programador: Ighor Drummond   
*/
function deletarCartao(element){
    if(confirm('Tem certeza que deseja deletar este cartão? Após este processo, o cartão será deletado permanentemente.')){
        var elementoPai = element.parentElement;
        var Card = elementoPai.id;
        $.ajax({
            url: 'script/pedidos.php?Opc=16&Card=' + encodeURIComponent(Card),
            method: 'POST',
            success: function (CardDate) {
                telaCarregamento(false);
                attPagina(4, 6);//Atualiza Pagina
                if(CardDate === 'DELETADO'){
                    alerta('Cartão deletado com sucesso!', 1);
                }else{
                    alerta('Não foi possivel buscar o seu cartão no banco de dados. Tente novamente ou mais tarde', 0);
                }
            },
            error: function (xhr, status, error) {
                telaCarregamento(false);
                alerta('Não foi possivel buscar o seu cartão no banco de dados. Tente novamente ou mais tarde', 0);
            }
        });
    }
}
/*
Função: deletarEnd(element)
Descrição: Deletar endereço desejado
Data: 04/07/2024
Programador: Ighor Drummond   
*/
function deletarEnd(element){
    var titulos = element.parentElement.getElementsByTagName('h6');
    var nCont = 0;
    var dadosEnd = {
        cep: '',
        rua: '',
        numero: '',
        cidade: '',
        bairro: '',
        estado: '',
        complemento: '',
        referencia: ''
    };

    //Prepara a mascará para remover palavras e caracteres indesejados
    let palavrasMortas = /(Cep:|Rua:|Estado:|Referência:|Complemento:|Numero:|Cidade:|Bairro:|:\s*|\s+)/gi;

    //Adiciona os dados do endereço para ser deletado
    for(const chave in dadosEnd){
        if(dadosEnd.hasOwnProperty(chave)){
            dadosEnd[chave] = titulos[nCont].textContent.replace(palavrasMortas, ' ').trimStart();
            nCont++;
        }
    }
    
    //Envia dados para o servidor
    $.ajax({
        url: 'script/home_config_js?Opc=20',
        method: 'POST',
        data: dadosEnd,
        success: function(response){
            console.log(response);
        },
        error: function(xhr, status, error){
            console.log(xhr + status + error);
        }
    });
}