/*
*BANCO CRIADO COM CARINHO POR IGHOR DRUMMOND.
*DATA: 26/06/2024
*OBJETIVO: INSERIR CUPONS PROMOCIONAIS PARA PRODUTOS DEFINIDOS
*/
USE db_aerofusion;

#INSIRINDO CUPONS
#EPOCA DE NATAL
INSERT INTO cupons(nome_cupom, cupom_ativo, data_inicio, data_validade, valor_desconto, id_prod) 
VALUES ('NATALLEGAL', 0, '2024-12-15 08:00:00' , '2024-12-26 00:00:00', 100.00, 17);

INSERT INTO cupons(nome_cupom, cupom_ativo, data_inicio, data_validade, valor_desconto, id_prod) 
VALUES ('NATALLEGAL', 0, '2024-12-15 08:00:00' , '2024-12-26 00:00:00', 100.00, 1);

INSERT INTO cupons(nome_cupom, cupom_ativo, data_inicio, data_validade, valor_desconto, id_prod) 
VALUES ('NATALLEGAL', 0, '2024-12-15 08:00:00' , '2024-12-26 00:00:00', 100.00, 3);

INSERT INTO cupons(nome_cupom, cupom_ativo, data_inicio, data_validade, valor_desconto, id_prod) 
VALUES ('NATALLEGAL', 0, '2024-12-15 08:00:00' , '2024-12-26 00:00:00', 100.00, 7);

#EPOCA HALLOWEEN
INSERT INTO cupons(nome_cupom, cupom_ativo, data_inicio, data_validade, valor_desconto, id_prod) 
VALUES ('HALLOWEEEN', 0, '2024-12-15 08:00:00' , '2024-12-26 00:00:00', 50.00, 17);

INSERT INTO cupons(nome_cupom, cupom_ativo, data_inicio, data_validade, valor_desconto, id_prod) 
VALUES ('HALLOWEEEN', 0, '2024-12-15 08:00:00' , '2024-12-26 00:00:00', 50.00, 1);

INSERT INTO cupons(nome_cupom, cupom_ativo, data_inicio, data_validade, valor_desconto, id_prod) 
VALUES ('HALLOWEEEN', 0, '2024-12-15 08:00:00' , '2024-12-26 00:00:00', 50.00, 3);

INSERT INTO cupons(nome_cupom, cupom_ativo, data_inicio, data_validade, valor_desconto, id_prod) 
VALUES ('HALLOWEEEN', 0, '2024-12-15 08:00:00' , '2024-12-26 00:00:00', 50.00, 7);

#EPOCA INVERNO
INSERT INTO cupons(nome_cupom, cupom_ativo, data_inicio, data_validade, valor_desconto, id_prod) 
VALUES ('INVERNOTOP', 0, '2024-12-15 08:00:00' , '2024-12-26 00:00:00', 150.00, 17);

INSERT INTO cupons(nome_cupom, cupom_ativo, data_inicio, data_validade, valor_desconto, id_prod) 
VALUES ('INVERNOTOP', 0, '2024-12-15 08:00:00' , '2024-12-26 00:00:00', 150.00, 1);

INSERT INTO cupons(nome_cupom, cupom_ativo, data_inicio, data_validade, valor_desconto, id_prod) 
VALUES ('INVERNOTOP', 0, '2024-12-15 08:00:00' , '2024-12-26 00:00:00', 150.00, 3);

INSERT INTO cupons(nome_cupom, cupom_ativo, data_inicio, data_validade, valor_desconto, id_prod) 
VALUES ('INVERNOTOP', 0, '2024-12-15 08:00:00' , '2024-12-26 00:00:00', 150.00, 7);