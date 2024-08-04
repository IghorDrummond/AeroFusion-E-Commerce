/*
*BANCO CRIADO COM CARINHO POR IGHOR DRUMMOND.
*DATA: 17/05/2024
*OBJETIVO: CRIAR BANCO, TABELA E ALGUNS DADOS PADRÕES.
*/  
#CRIANDO BANCO DE DADOS
CREATE DATABASE db_aerofusion CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
#USANDO O BANCO
USE db_aerofusion;
#CRIANDO TABELA

#CRIANDO TABELA DE SETORES
CREATE TABLE setores(
    id_set int primary key auto_increment not null,
    nome varchar(20) not null,
    descricao varchar(150) not null
);

#INSIRINDO SETORES NA TABELA SETORES
INSERT INTO setores(nome, descricao) VALUES('Admin', 'Responsavel por controlar todo site');
INSERT INTO setores(nome, descricao) VALUES('Atendente', 'Responsavel por controlar os protocolos e garantia');
INSERT INTO setores(nome, descricao) VALUES('Vendedor', 'Responsavel por controlar produtos fora de estoques');
INSERT INTO setores(nome, descricao) VALUES('Cliente', 'Opção de cliente para efetuar compras');

#CRIANDO A TABELA DE CLIENTES
CREATE TABLE cliente(
    id int primary key auto_increment not null,
    email varchar(50) not null,
    senha varchar(12) not null,
    nome varchar(30) not null,
    sobrenome varchar(30) not null,
    data_nascimento date not null,
    genero varchar(1) default 'M',
    celular varchar(11) not null,
    cpf  varchar(11) not null,
    foto varchar(100) default 'novo_usuario.png' not null,
    opc int not null,
    cliente_ativado boolean not null default true,
    FOREIGN KEY (opc) REFERENCES setores(id_set)
);

#INSIRINDO O ADMINISTRADOR NA TABELA CLIENTE
INSERT INTO cliente(email, senha, nome, sobrenome, data_nascimento, celular, cpf, opc)
VALUES('admin@aerofusion.com', '123456789', 'Administrador', 'do Sistema', '2024-01-01', '11999999999', 'xxxxxxxxxxx' , 1);

#CRIANDO A TABELA DE ENDEREÇO
CREATE TABLE endereco(
    id_end int primary key auto_increment not null,
    rua varchar(60) not null,
    bairro varchar(50) not null,
    cidade varchar(20) not null,
    numero int not null,
    complemento varchar(50),
    referencia  varchar(50),
    cep varchar(8) not null,
    uf varchar(2) not null,
    id_cliente int not null,
    end_ativo boolean not null default true,
    FOREIGN KEY (id_cliente) references cliente(id)
);

#CRIANDO A TABELA DE CATEGORIAS
CREATE TABLE categoria(
    id_cat int primary key auto_increment not null,
    nome_cat varchar(30) not null,
    descricao_cat text
);

#INSIRINDO CATEGORIAS NA TABELA CATEGORIAS
INSERT INTO categoria(nome_cat) VALUES('infantil');
INSERT INTO categoria(nome_cat) VALUES('masculino');
INSERT INTO categoria(nome_cat) VALUES('feminino');
INSERT INTO categoria(nome_cat) VALUES('promoção');

#CRIANDO A TABELA DE TAMANHOS
CREATE TABLE tamanho(
    id_tam int primary key auto_increment not null,
    nome_tam varchar(2) not null
);

#INSIRINDO OS TAMANHOS NA TABELA TAMANHO
INSERT INTO tamanho(nome_tam) VALUES('34');
INSERT INTO tamanho(nome_tam) VALUES('35');
INSERT INTO tamanho(nome_tam) VALUES('36');
INSERT INTO tamanho(nome_tam) VALUES('37');
INSERT INTO tamanho(nome_tam) VALUES('38');
INSERT INTO tamanho(nome_tam) VALUES('39');
INSERT INTO tamanho(nome_tam) VALUES('40');
INSERT INTO tamanho(nome_tam) VALUES('41');
INSERT INTO tamanho(nome_tam) VALUES('42');
INSERT INTO tamanho(nome_tam) VALUES('43');
INSERT INTO tamanho(nome_tam) VALUES('44');

#CRIANDO A TABELA DE PRODUTOS
CREATE TABLE produtos(
    id_prod int primary key auto_increment not null,
    nome varchar(250) not null,
    descricao text not null,
    preco float(8,2) default 0 not null,
    estoque int default 0 not null,
    promocao float(8, 2) default 0,
    promocao_ativo boolean default false,
    tamanho text not null,
    vizu_3d boolean default false,
    obj_3d varchar(250) default '',
    id_cat int not null,
    FOREIGN KEY (id_cat) REFERENCES categoria(id_cat)
);

#CRIANDO TABELA DE IMAGENS DOS PRODUTOS
CREATE TABLE imagens_prod(
    id_img int primary key auto_increment not null,
    img1 varchar(150) not null,
    img2 varchar(150) default '',
    img3 varchar(150) default '',
    img4 varchar(150) default '',
    img5 varchar(150) default '',
    id_prod int not null,
    FOREIGN KEY (id_prod) REFERENCES produtos(id_prod)
);

#CRIANDO TABELA DE FAVORITOS
CREATE TABLE favoritos(
    id_fav int primary key auto_increment not null,
    id_prod int not null,
    id_cliente int not null,
    FOREIGN KEY (id_prod) REFERENCES produtos(id_prod),
    FOREIGN KEY (id_cliente) REFERENCES cliente(id)
);

#CRIANDO TABELA DE BANDEIRAS PARA CARTÃO
CREATE TABLE bandeiras(
    id_ban int primary key auto_increment not null,
    nome_ban varchar(30) not null,
    img_ban text not null
);

#INSIRINDO AS BANDEIRAS DOS CARTÕES
INSERT INTO bandeiras(nome_ban, img_ban) VALUES('MASTERCARD', 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/2a/Mastercard-logo.svg/618px-Mastercard-logo.svg.png');
INSERT INTO bandeiras(nome_ban, img_ban) VALUES('ELO', 'https://seeklogo.com/images/E/elo-logo-0B17407ECC-seeklogo.com.png');
INSERT INTO bandeiras(nome_ban, img_ban) VALUES('AMERICAN EXPRESS', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ-U8tK4EfgFz0FAX0yYoXfE05yWfq2tqNLQw&s');
INSERT INTO bandeiras(nome_ban, img_ban) VALUES('DISCOVER', 'https://www.discoversignage.com/uploads/09-12-21_04:20_DGN_AcceptanceMark_FC_Hrz_RGB.png');
INSERT INTO bandeiras(nome_ban, img_ban) VALUES('DINERS', 'https://seeklogo.com/images/D/diners-club-logo-E375570397-seeklogo.com.png');
INSERT INTO bandeiras(nome_ban, img_ban) VALUES('JCB' , 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/40/JCB_logo.svg/1280px-JCB_logo.svg.png');
INSERT INTO bandeiras(nome_ban, img_ban) VALUES('JCB15', 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/40/JCB_logo.svg/1280px-JCB_logo.svg.png');
INSERT INTO bandeiras(nome_ban, img_ban) VALUES('MAESTRO', 'https://seeklogo.com/images/M/Maestro-logo-333A576204-seeklogo.com.png');
INSERT INTO bandeiras(nome_ban, img_ban) VALUES('UNIONPLAY', 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/1b/UnionPay_logo.svg/1280px-UnionPay_logo.svg.png');
INSERT INTO bandeiras(nome_ban, img_ban) VALUES('VISA', 'https://w7.pngwing.com/pngs/49/82/png-transparent-credit-card-visa-logo-mastercard-bank-mastercard-blue-text-rectangle.png');

#CRIANDO TABELA DE CARTÕES
CREATE TABLE cartoes(
    id_card int primary key auto_increment not null,
    nome_cartao varchar(250) not null,
    numero_cartao varchar(16) not null,
    cvv int(3) not null,
    validade date not null,
    id_ban int not null,
    id_cliente int not null,
    FOREIGN KEY (id_cliente) REFERENCES cliente(id),
    FOREIGN KEY (id_ban) REFERENCES bandeiras(id_ban) 
);

#CRIANDO TABELA DE AVALIAÇÕES
CREATE TABLE avaliacoes(
    id_ava int primary key auto_increment not null,
    titulo_men varchar(100) not null,
    mensagem text not null,
    estrelas int default 0 not null,
    img varchar(150),
    img2 varchar(150),
    img3 varchar(150),
    id_prod int not null,
    id_cliente int not null,
    data_ava datetime not null,
    FOREIGN KEY (id_cliente) REFERENCES cliente(id),
    FOREIGN KEY (id_prod) REFERENCES produtos(id_prod)
);

#CRIANDO TABELA DE CUPONS
CREATE TABLE cupons(
    id_cup int primary key auto_increment not null,
    nome_cupom  varchar(20) not null,
    cupom_ativo boolean not null default false,
    data_inicio datetime not null,
    data_validade datetime not null,
    valor_desconto float(8,2) default 0 not null,
    id_prod int not null,
    foreign key (id_prod) references produtos(id_prod)
);

#CRIANDO TABELA DE CÓDIGOS DE RECUPERAÇÃO PARA SENHAS
CREATE TABLE codigos(
    id_cod int primary key auto_increment not null,
    codigo varchar(8) not null,
    data_codigo datetime not null,
    id_cliente int not null,
    FOREIGN KEY (id_cliente) REFERENCES cliente(id)
);

#CRIANDO TABELA DE FUNCIONARIOS
CREATE TABLE funcionarios(
    id_fun int primary key auto_increment not null,
    id_setor int not null,
    descricao varchar(250) default 'Resposanvel por Diversos(a) areas',
    data_fun datetime not null,
    id_cliente int not null, 
    FOREIGN KEY (id_cliente) REFERENCES cliente(id),
    FOREIGN KEY (id_setor) REFERENCES setores(id_set)
);

#CRIANDO TABELA DE STATUS
CREATE TABLE status(
    id_sta int primary key auto_increment not null,
    nome varchar(30) not null
);

#INSIRINDO SETORES NA TABELA SETORES
INSERT INTO status(nome) VALUES('Pendente');
INSERT INTO status(nome) VALUES('Aguardando Envio');
INSERT INTO status(nome) VALUES('Transportando');
INSERT INTO status(nome) VALUES('Saiu para entrega');
INSERT INTO status(nome) VALUES('Entregue');
INSERT INTO status(nome) VALUES('Cancelado');

#CRIANDO TABELA DE FORMA DE PAGAMENTO
CREATE TABLE forma_pagamento(
    id_form int primary key auto_increment not null,
    forma_pag varchar(100) default '' not null
);

#INSIRINDO FORMA DE PAGAMENTOS
INSERT INTO forma_pagamento(forma_pag) VALUES('PIX');
INSERT INTO forma_pagamento(forma_pag) VALUES('CARTÃO');
INSERT INTO forma_pagamento(forma_pag) VALUES('BOLETO');

#CRIANDO TABELA DE PEDIDOS DE COMPRA
CREATE TABLE pedidos(
    id_ped int auto_increment primary key not null,
    valor_total float(8,2) default 0 not null,
    data_pedido datetime not null,
    id_cliente int not null,
    id_end int not null,
    status int not null,
    id_form int not null,
    nome_cupom varchar(20) default '',
    parcelamento int default 1,
    FOREIGN KEY (id_cliente) REFERENCES cliente(id),
    FOREIGN KEY (status) REFERENCES status(id_sta),
    FOREIGN KEY (id_end) REFERENCES endereco(id_end),
    FOREIGN KEY (id_form) REFERENCES forma_pagamento(id_form)
);

#CRIANDO TABELA DE ITENS DOS PEDIDOS
CREATE TABLE item_pedidos(
    id_iped int primary key auto_increment not null,
    id_prod int not null,
    id_ped int not null,
    id_tam int not null,
    quant int not null,
    preco_item float(8,2) not null,
    FOREIGN KEY (id_prod) REFERENCES produtos(id_prod),
    FOREIGN KEY (id_ped) REFERENCES pedidos(id_ped),
    FOREIGN KEY (id_tam) REFERENCES tamanho(id_tam)
);

#CRIANDO TABELA DE PROTOCOLOS E GARANTIA
CREATE TABLE protocolo(
    id_pro int primary key auto_increment not null,
    id_cliente int not null,
    id_fun int not null,
    id_ped int not null,
    data_abertura datetime not null,
    status_ int not null,
    data_fechamento datetime,
    FOREIGN KEY (id_cliente) REFERENCES cliente(id),
    FOREIGN KEY (id_fun) REFERENCES funcionarios(id_fun),
    FOREIGN KEY (id_ped) REFERENCES pedidos(id_ped) 
);

#CRIANDO TABELA DE MENSAGENS DO PROTOCOLOS
CREATE TABLE mensagens_protocolos(
    id_men int primary key auto_increment not null,
    mensagem text not null,
    data_prot datetime not null,
    img varchar(250),
    id_pro int not null,
    FOREIGN KEY (id_pro) REFERENCES protocolo(id_pro) 
);

#CRIANDO TABELA DE CARRINHO
CREATE TABLE carrinho(
    id_car int primary key auto_increment not null,
    id_prod int not null,
    id_cliente int not null,
    quant int default 1 not null,
    data_car datetime not null,
    id_tam int not null,
    FOREIGN KEY (id_prod) REFERENCES produtos(id_prod),
    FOREIGN KEY (id_cliente) REFERENCES cliente(id),
    FOREIGN KEY (id_tam) REFERENCES tamanho(id_tam)
);

#CRIANDO TABELA DE REGISTRO DE RASTREIO
CREATE TABLE rastreio(
    id_ras int primary key auto_increment not null,
    id_ped int not null,
    data_rastreio datetime not null,
    status_ras int not null,
    FOREIGN KEY (id_ped) REFERENCES pedidos(id_ped),
    FOREIGN KEY (status_ras) REFERENCES status_rastreio(id_sta_ras)
);

#CRIANDO TABELA DE STATUS DE RASTREIO
CREATE TABLE status_rastreio(
    id_sta_ras int primary key auto_increment not null,
    status_ras varchar(50) not null,
    descricao_ras varchar(250) default ''
);

#INSIRINDO FORMA DE PAGAMENTOS
INSERT INTO status_rastreio(status_ras, descricao_ras) VALUES('PREPARANDO PRODUTOS(OS)', 'A AEROFUSION ESTÁ PREPARANDO SEU PRODUTO(OS)');
INSERT INTO status_rastreio(status_ras, descricao_ras) VALUES('SAIU DO ARMAZÉM', 'SAIU DO ARMAZÉM PARA A DISTRIBUIDORA');
INSERT INTO status_rastreio(status_ras, descricao_ras) VALUES('RECEBIDO PELA TRANSPORTADORA', 'TRANSPORTADORA COLETOU O PRODUTO(OS) DO PEDIDO');
INSERT INTO status_rastreio(status_ras, descricao_ras) VALUES('DESLOCANDO PARA SUA CIDADE', 'TRANSPORTADORA ESTÁ SE DESLOCANDO PARA SUA CIDADE');
INSERT INTO status_rastreio(status_ras, descricao_ras) VALUES('SAIU PARA ENTREGA', 'A TRANSPORTADORA ESTÁ LEVANDO SEU PRODUTO(OS) PARA SUA RESIDÊNCIA');
INSERT INTO status_rastreio(status_ras, descricao_ras) VALUES('ENTREGUE', 'PRODUTO(OS) ENTREGUE PARA O DESTINATÁRIO');
INSERT INTO status_rastreio(status_ras, descricao_ras) VALUES('DEVOLVIDO', 'PRODUTO(OS) FOI DEVOLVIDO AO ARMAZÉM PARA A AEROFUSION');
