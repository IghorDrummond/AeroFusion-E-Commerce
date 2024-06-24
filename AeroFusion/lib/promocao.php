<?php
    namespace Promocao{
        require_once('conexao.php');

        interface montagemQuery{
            public function montaQuery();
        }
		/*
		*Classes: ProdutosTela()
		*Descrição: Classe responsavel por imprimir os 12 produtos na tela principal
		*Data: 23/05/2024
		*Programador(a): Ighor Drummond
		*/
        class ProdutoTela implements montagemQuery {
            //Atributo
            protected $Conexao = '';
            protected $Stmt = '';
            private $Query = '';
            
            //Construtor
            public function __construct(){
                $this->Conexao = new \IniciaServer();
                $this->Conexao = $this->Conexao->conexao();
            }

            //Métodos
			/*
			*Metodo: retornaProdutos()
			*Descrição: retorna produtos para tela principal
			*Data: 23/05/2024
			*Programador(a): Ighor Drummond
			*/
            public function retornaProdutos(){
                try{
                    $this->montaQuery();
                    $this->Stmt = $this->Conexao->query($this->Query);
                    //Retorna os Ultimos Produtos que entraram
                    return $this->Stmt->fetchAll(\PDO::FETCH_ASSOC);
                }catch(\PDOException $e){
                    echo $e->getMessage();
                    return '';
                }
            }

            public function montaQuery(){
                $this->Query = "
                    SELECT 
                        prod.nome as Produto,
                        FORMAT(prod.preco, 2, 'pt_BR') as Preco,
                        prod.id_prod as IdProd,
                        prod.estoque as Estoque,
                        FORMAT(prod.promocao, 2, 'pt_BR') as promocao,
                        prod.promocao_ativo,
                        cat.nome_cat as Categoria,
                        img_prod.img1,
                        img_prod.img2,
                        img_prod.img3,
                        img_prod.img4,
                        img_prod.img5
                    FROM 
                        produtos as prod
                    INNER JOIN
                        imagens_prod AS img_prod ON prod.id_prod = img_prod.id_prod
                    INNER JOIN 
                        categoria AS cat ON cat.id_cat = prod.id_cat
                    WHERE 
                        prod.estoque > 0
                    ORDER BY
                        CASE WHEN prod.promocao_ativo = 1 THEN prod.promocao ELSE prod.preco END ASC
                    LIMIT
                        12;
                ";
            }
        }
    }
?>