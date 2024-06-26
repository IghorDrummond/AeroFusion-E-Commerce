/*
*BANCO CRIADO COM CARINHO POR IGHOR DRUMMOND.
*DATA: 26/06/2024
*OBJETIVO: INSERIR CUPONS PROMOCIONAIS PARA PRODUTOS DEFINIDOS
*/
USE db_aerofusion;

#INSIRINDO CUPONS
#EPOCA DE NATAL
INSERT INTO cupons(nome_cupom, cupom_ativo, data_inicio, data_validade, valor, valor_desconto, id_prod) 
VALUES ('NATALLEGAL', false , '2024-12-26 00:00:00', 100, 17);

INSERT INTO cupons(nome_cupom, cupom_ativo, data_inicio, data_validade, valor, valor_desconto, id_prod) 
VALUES ('NATALLEGAL', false , '2024-12-15 08:00:00' , '2024-12-26 00:00:00', 100, 1);

INSERT INTO cupons(nome_cupom, cupom_ativo, data_inicio, data_validade, valor, valor_desconto, id_prod) 
VALUES ('NATALLEGAL', false , '2024-12-15 08:00:00' , '2024-12-26 00:00:00', 100, 3);

INSERT INTO cupons(nome_cupom, cupom_ativo, data_inicio, data_validade, valor, valor_desconto, id_prod) 
VALUES ('NATALLEGAL', false , '2024-12-15 08:00:00' , '2024-12-26 00:00:00', 100, 7);

#EPOCA HALLOWEEN
INSERT INTO cupons(nome_cupom, cupom_ativo, data_inicio, data_validade, valor, valor_desconto, id_prod) 
VALUES ('HALLOWEEEN', false , '2024-12-26 00:00:00', 50, 17);

INSERT INTO cupons(nome_cupom, cupom_ativo, data_inicio, data_validade, valor, valor_desconto, id_prod) 
VALUES ('HALLOWEEEN', false , '2024-12-15 08:00:00' , '2024-12-26 00:00:00', 50, 1);

INSERT INTO cupons(nome_cupom, cupom_ativo, data_inicio, data_validade, valor, valor_desconto, id_prod) 
VALUES ('HALLOWEEEN', false , '2024-12-15 08:00:00' , '2024-12-26 00:00:00', 50, 3);

INSERT INTO cupons(nome_cupom, cupom_ativo, data_inicio, data_validade, valor, valor_desconto, id_prod) 
VALUES ('HALLOWEEEN', false , '2024-12-15 08:00:00' , '2024-12-26 00:00:00', 50, 7);

#EPOCA INVERNO
INSERT INTO cupons(nome_cupom, cupom_ativo, data_inicio, data_validade, valor, valor_desconto, id_prod) 
VALUES ('INVERNOTOP', false , '2024-12-26 00:00:00', 150, 17);

INSERT INTO cupons(nome_cupom, cupom_ativo, data_inicio, data_validade, valor, valor_desconto, id_prod) 
VALUES ('INVERNOTOP', false , '2024-12-15 08:00:00' , '2024-12-26 00:00:00', 150, 1);

INSERT INTO cupons(nome_cupom, cupom_ativo, data_inicio, data_validade, valor, valor_desconto, id_prod) 
VALUES ('INVERNOTOP', false , '2024-12-15 08:00:00' , '2024-12-26 00:00:00', 150, 3);

INSERT INTO cupons(nome_cupom, cupom_ativo, data_inicio, data_validade, valor, valor_desconto, id_prod) 
VALUES ('INVERNOTOP', false , '2024-12-15 08:00:00' , '2024-12-26 00:00:00', 150, 7);