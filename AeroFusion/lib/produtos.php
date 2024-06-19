<?php

    namespace Produto{
        require_once('conexao.php');

        interface ProdutoInterface{
            public function retornaValores();
        }

        Class Filtrar implements ProdutoInterface{
            //Atributos
            public $Conexao = '';
            public $Preco = '';
            public $Categoria = '';
            public $Tamanho = '';
            public $Data = '';
            public $Query = '';
            public $Produto = '';
            protected $stmt = null;

            //Construtor
            public function __construct($Preco, $Data, $Categoria, $Tamanho, $Produto){
                $this->Conexao = new \IniciaServer();
                $this->Conexao = $this->Conexao->conexao();
                //Recebe valores para filtrar produtos
                $this->Preco = $Preco;
                $this->Data = $Data;
                $this->Categoria = $Categoria;
                $this->Tamanho = $Tamanho;
                $this->Produto = $Produto;
            }
            
            //Métodos
            /*
            *Metodo: retornaValores()
            *Descrição: Retorna valores da pesquisa da query
            *Data: 28/05/2024
            *Programador(a): Ighor Drummond
            */
            public function retornaValores(){
                $this->Query = "
                    SELECT 
                        prod.nome as Produto,
                        prod.id_prod as IdProd,
                        cat.nome_cat as Categoria,
                        prod.preco as Preco,
                        prod.tamanho as tamanho,
                        img_prod.img1 as img1,
                        img_prod.img2 as img2,
                        img_prod.img3 as img3,
                        img_prod.img4 as img4,
                        img_prod.img5 as img5
                    FROM
                        produtos AS prod
                    INNER JOIN  
                        categoria as cat ON  cat.id_cat = prod.id_cat
                    INNER JOIN 
                        imagens_prod AS img_prod ON img_prod.id_prod = prod.id_prod 
                    WHERE
                        prod.estoque > 0
                ";
                //Retornará os filtros escolhido pelo usuário
                $this->Query .= $this->montaQuery();
                try{
                    $this->stmt = $this->Conexao->query($this->Query);
                    //Retorna Resultado das busca
                    return $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
                }catch(\PDOException $e){
                    echo $e->getMessage();
                    return 'ERROR';
                }
            }
            /*
			*Metodo: montaQuery()
			*Descrição: Retorna por montar a query
			*Data: 28/05/2024
			*Programador(a): Ighor Drummond
			*/
            private function montaQuery(){
                $query = '';
                //Valida se há um produto especificado
                if(!empty($this->Produto)){
                    $query .= PHP_EOL . "AND prod.nome LIKE '%" . $this->Produto . "%'"; 
                }                
                //Valida se há uma categoria especificada
                if(!empty($this->Categoria)){
                    $query .= PHP_EOL . "AND cat.nome_cat = '" . $this->Categoria . "'"; 
                }
                //Valida se há tamanho especificado
                if(!empty($this->Tamanho)){
                    $query .= PHP_EOL . "AND  FIND_IN_SET('$this->Tamanho', REPLACE(prod.tamanho, ';', ','))";
                }
                //Valida se há um nome do produto inserido
                if(!empty($this->Produto)){
                    $query .= PHP_EOL . "AND prod.nome LIKE '%" . $this->Produto . "%'"; 
                }
                //Valida se há uma ordenação de preço inserido
                if(!empty($this->Preco)){
                    $query .= PHP_EOL . " ORDER BY " . PHP_EOL . 'prod.preco ' . ($this->Preco === 'Alto' ? 'DESC' : 'ASC'); 
                }
                if(!empty($this->Data)){
                    //Validas e já foi colocado ORDER BY dentro da query
                    if(stripos($query, 'ORDER BY') === false){
                        $query .= PHP_EOL . "ORDER BY ";
                    }else{
                        $query .= ",";
                    }
                    //Monta Query filtrando a ordem desejada pelo usuário
                    $query .= PHP_EOL . "prod.id_prod " . ($this->Data === 'Recente' ? 'DESC' : 'ASC'); 
                }

                //Adiciona limite de retorno dos produtos
                $query .= PHP_EOL . "
                    LIMIT
                        12
                ";
                return $query;
            }
        }   
        /*
        *Classes: Produto()
        *Descrição: Classe responsavel por apresentar produtos
        *Data: 28/05/2024
        *Programador(a): Ighor Drummond
        */
        class Produto implements ProdutoInterface{
            //Atributos
            public $ID;
            protected $query = '';
            protected $conexao = null;
            protected $stmt = null;

            //Construtor
            public function __construct($Produto){
                $this->ID = $Produto;
                $this->conexao = new \IniciaServer();
                $this->conexao = $this->conexao->conexao();
            }

            //Métodos
            /*
            *Metodo: retornaValores()
            *Descrição: Retorna valores da pesquisa da query
            *Data: 28/05/2024
            *Programador(a): Ighor Drummond
            */
            public function retornaValores(){
                $Ret = false;
                try{
                    $this->montaQuery();
                    $this->stmt = $this->conexao->query($this->query);
                    $Ret = $this->stmt->fetch(\PDO::FETCH_ASSOC);
                }catch(\PDOException $e){
                    echo $e->getMessage();
                }finally{
                    return $Ret;
                }
            }
            /*
            *Metodo: montaQuery()
            *Descrição: Retorna por montar a query
            *Data: 28/05/2024
            *Programador(a): Ighor Drummond
            */
            private function montaQuery(){
                $this->query = "
                    SELECT 
                        prod.nome,
                        prod.descricao,
                        FORMAT(prod.preco, 2, 'pt_BR') as preco,
                        prod. estoque,
                        prod.promocao,
                        prod.promocao_ativo,
                        prod.tamanho,
                        img_prod.img1,
                        img_prod.img2,
                        img_prod.img3,
                        img_prod.img4,
                        img_prod.img5,
                        cat.nome_cat as categoria,
						CASE 
						    WHEN prod.estoque > 0 THEN 'SIM'
						    ELSE 'FALTA ESTOQUE'
						END AS disponibilidade 
                    FROM 
                        produtos as prod
                    INNER JOIN
                        imagens_prod as img_prod ON img_prod.id_prod = prod.id_prod
                    INNER JOIN
                        categoria as cat ON cat.id_cat = prod.id_cat
                    WHERE
                        prod.id_prod = $this->ID                  
                ";
            }
        }
        /*
        *Classes: AvalicaoProduto()
        *Descrição: Classe responsavel por retornas avaliações dos usuários
        *Data: 28/05/2024
        *Programador(a): Ighor Drummond
        */
        class AvalicaoProduto implements ProdutoInterface{
            //Atributos
            public $ID;
            protected $Conexao = null;
            protected $stmt = null;
            protected $query = '';

            //Construtor
            public function __construct($Produto){
                $this->ID = $Produto;
                $this->Conexao = new \IniciaServer();
                $this->Conexao = $this->Conexao->conexao();
            }

            //Metodo
            /*
            *Metodo: retornaValores()
            *Descrição: Retorna valores da pesquisa da query
            *Data: 28/05/2024
            *Programador(a): Ighor Drummond
            */
            public function retornaValores(){
                $Ret = false;
                try{
                    $this->montaQuery();
                    $this->stmt = $this->Conexao->query($this->query);
                    $Ret = $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
                }catch(\PDOException $e){
                    echo $e->getMessage();
                }finally{
                    return $Ret;
                }
            }
            /*
            *Metodo: montaQuery()
            *Descrição: Retorna por montar a query
            *Data: 28/05/2024
            *Programador(a): Ighor Drummond
            */
            private function montaQuery(){
                $this->query = "
                    SELECT 
                        ava.img,
                        ava.img2,
                        ava.img3,
                        ava.mensagem as comentario,
                        ava.estrelas as estrelas,
                        CONCAT(cli.nome, ' ', cli.sobrenome) as Nome,
                        cli.foto as Foto 
                    FROM 
                        avaliacoes as ava
                    INNER JOIN
                        produtos as prod ON ava.id_prod = prod.id_prod
                    INNER JOIN 
                        cliente as cli ON cli.id = ava.id_cliente
                    WHERE
                        prod.id_prod = $this->ID                  
                ";
            }
        }
        /*
        *Classes: Tamanhos()
        *Descrição: Classe responsavel por retornas os tamanhos do tênis
        *Data: 28/05/2024
        *Programador(a): Ighor Drummond
        */
        class Tamanhos implements ProdutoInterface{
            //Atributos
            public $Tamanhos = '';
            protected $Conexao = null;
            protected $stmt = null;
            protected $query = '';

            //Construtor
            public function __construct($Tamanhos){
                $this->Tamanhos = $Tamanhos;

                try{
                    $this->Conexao = new \IniciaServer();
                    $this->Conexao = $this->Conexao->conexao();
                }catch(\PDOException $e){
                    echo $e->getMessage();
                }
            }

            //Métodos
            /*
            *Metodo: retornaValores()
            *Descrição: Retorna valores da pesquisa da query
            *Data: 28/05/2024
            *Programador(a): Ighor Drummond
            */
            public function retornaValores(){
                $this->montaQuery();
                $Ret = null;
                try{
                    $this->stmt =  $this->Conexao->query($this->query);
                    $Ret = $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
                }catch(\PDOException $e){
                    $Ret = false;
                    echo $e->getMessage();
                }finally{
                    return $Ret;
                }
            }
            /*
            *Metodo: montaQuery()
            *Descrição: Retorna por montar a query
            *Data: 28/05/2024
            *Programador(a): Ighor Drummond
            */
            protected function montaQuery(){
                $Valores = explode(';', $this->Tamanhos);
                $InQuery = "";

                for($nCont = 0; $nCont <= count($Valores)-1; $nCont++){
                    $InQuery .= $Valores[$nCont] .",";
                }

                $InQuery = substr($InQuery, 0, strlen($InQuery) -1);

                $this->query = "
                    SELECT
                        *
                    FROM
                        tamanho
                    WHERE
                        id_tam in($InQuery)
                ";
            }
        }

        /*
        *Classes: Favoritos()
        *Descrição: Valida se o produto já está favoritado
        *Data: 13/06/2024
        *Programador(a): Ighor Drummond
        */
        class Favoritos{
            //Atributos
            private $conexao = null;
            private $stmt = [];
            private $query = null;
            private $IdProd = null;
            private $IdCli = null;

            //Construtor
            function __construct(public $Email = "")
            {
                try{
                    //Liga a conexão com o Banco de Dados
                    $this->conexao = new \IniciaServer();
                    $this->conexao = $this->conexao->conexao();
                }catch(\PDOException $e){
                    echo $e->getMessage();
                }
            }

            //Métodos
            /*
            *Metodo: retornaValores()
            *Descrição: Retorna valores da pesquisa da query
            *Data: 13/06/2024
            *Programador(a): Ighor Drummond
            */
            public function retornaValores($Prod){
                $this->IdProd = $Prod;
                //Monta query pesquisando o produto usando usuário logado
                $this->montaQuery(0);

                try{
                    $this->stmt = $this->conexao->query($this->query);
                    $this->stmt = $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
                    //Valida se existe o produto favoritado no banco de dados
                    if(isset($this->stmt[0]['id_cliente'])){
                        return true;
                    }else{
                        return false;
                    }
                }catch(\PDOException $e){
                    echo $e->getMessage();
                }
            }
            /*
            *Metodo: campoFavorito(Id do Produto) 
            *Descrição: Responsavel por adicionar ou remover produto aos favoritos
            *Data: 13/06/2024
            *Programador(a): Ighor Drummond
            */
            public function campoFavorito($Prod, $Opc){
                $this->IdProd = $Prod;//Passa o Id do Produto
                $Ret = false;
                
                try{             
                    //Caso for para inserir, ele valida se já existe o produto nos favoritos
                    if($Opc === 1){
                        //Monta Query para validar se já existe produto adicionado ao favoritos
                        $this->montaQuery(0);
                        $this->stmt = $this->conexao->query($this->query);
                        $this->stmt = $this->stmt->fetchAll(\PDO::FETCH_ASSOC);  
                          
                        if(isset($this->stmt[0]['id_fav'])){
                            return true;
                        }
                    }
                    //Retorna o Id do cliente
                    $this->montaQuery(3);//Monta query para retornar o id do cliente
                    $this->stmt = $this->conexao->query($this->query);
                    $this->stmt = $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
                    //Guarda o id do usuário no retorno
                    if(isset($this->stmt[0]['id'])){
                        $this->IdCli = $this->stmt[0]['id'];
                        //Monta query para inserir ou remover produto do favoritos
                        $this->montaQuery($Opc);
                        //Adiciona aos favoritos
                        $this->conexao->beginTransaction();
                        $this->conexao->exec($this->query);
                        $this->conexao->commit();
                        $Ret = true;
                    }
                }catch(\PDOException $e){
                    echo $e->getMessage();
                    $Ret = false;
                    $this->conexao->rollback();
                }finally{
                    return true;
                }
            }
            /*
            *Metodo: montaQuery()
            *Descrição: Responsavel por montar as querys
            *Data: 13/06/2024
            *Programador(a): Ighor Drummond
            */
            protected function montaQuery($Opc){
                if($Opc === 0){
                    $this->query = "
                        SELECT 
                            *
                        FROM 
                            favoritos as fav
                        INNER JOIN
                            cliente as cli ON cli.id = fav.id_cliente
                        WHERE
                            cli.email = '$this->Email'
                        AND id_prod = $this->IdProd 
                ";
                }else if($Opc === 1){
                    $this->query = "
                        INSERT INTO favoritos(id_prod, id_cliente)
                        VALUES($this->IdProd, $this->IdCli);
                    ";
                }else if($Opc === 2){
                    $this->query = "
                        DELETE FROM
                            favoritos
                        WHERE
                            id_prod = $this->IdProd
                            AND id_cliente = $this->IdCli;
                    ";
                }else if($Opc === 3){
                    $this->query = "
                        SELECT 
                            id
                        FROM 
                            cliente
                        WHERE 
                            email = '$this->Email';
                    ";
                }
            }
        }
    }
?>