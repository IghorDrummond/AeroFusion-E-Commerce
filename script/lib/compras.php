<?php
	namespace Compra{
		require_once('conexao.php');

		/*
		*Classes: Carrinho()
		*Descrição: Classe responsavel por colocar produtos no carrinho da pessoa logada
		*Data: 31/05/2024
		*Programador(a): Ighor Drummond
		*/
		class AdicionarCarrinho {
			//Atributo
			protected $Query = '';
			protected $conexao = null;
			protected $stmt = null;
			protected $IdCli = null;
			protected $Data = null;

			//Construtor
			public function __construct(
				public $Produto = '',
				public $Quantidade = '',
				public $Tam = '',
				public $Cliente = ''
			){
				$this->conexao = new \IniciaServer();
				$this->conexao = $this->conexao->conexao();
			}

			//Métodos
			/*
			*Metodo: guardaProduto
			*Descrição: Responsavel por guardar produto no carrinho
			*Data: 31/05/2024
			*Programador(a): Ighor Drummond
			*/
			public function guardaProduto(){
				$Ret = '';
				$Valores = [];
				try{
					//Monta Query para pesquisar a quantidade dos produtos
					$this->montaQuery(0);
					$Valores = $this->pesquisaQuery();

					//Valida se o produto foi retornado
					if(isset($Valores[0]['id_prod'])){
						//Monta Query para verificar se já existe produto no carrinho
						$this->montaQuery(1);
						$Valores = $this->pesquisaQuery();

						if(!isset($Valores[0]['IdProd'])){
							//Retorna id do cliente 
							$this->montaQuery(2);
							$this->IdCli = $this->pesquisaQuery()[0]['id_cliente'];
							//Adiciona o Produto e sua quantidade no carrinho
							//Configura data do servidor para brasil
							date_default_timezone_set('America/Sao_Paulo');
							//Guarda a data atual do produto adicionado no carrinho
							$this->Data = date('Y-m-d H:i:s');
							//Monta Query para inserir valor
							$this->montaQuery(3);
							//Guarda valor
							$this->conexao->beginTransaction();

							if(($this->conexao->exec($this->Query)) > 0){
								$Ret = 'OK';
								$this->conexao->commit();
							}else{
								$Ret = 'NAOADICIONADO';
								$this->conexao->rollback();
							}
						}else{
							$Ret = 'CARRINHO';
						}
					}else{	
						$Ret = 'PRODUTO';
					}
					//
				}catch(\PDOException $e){
					echo $e->getMessage();
					$Ret = 'ERROR';
				}finally{
					return $Ret;
				}
			}
			/*
			*Metodo: montaQuery()
			*Descrição: Responsavel por monta a query
			*Data: 31/05/2024
			*Programador(a): Ighor Drummond
			*/
			protected function montaQuery($Val){
				if($Val === 0){
					//Pesquisa o produto para validar se está em estoque e sua quantidade além de ter o tamanho definido
					$this->Query = "
						SELECT 
							id_prod,
							promocao_ativo,
							promocao,
							preco,
							estoque,
							tamanho 
						FROM 
							produtos
						WHERE
							id_prod = $this->Produto
							AND estoque >= $this->Quantidade
							AND FIND_IN_SET('$this->Tam', REPLACE(tamanho, ';', ','))
					";
				}elseif($Val === 1){
					//Pesquisa se o produto já existe dentro do carrinho e caso existir, ele avisa se quer adicionar esse novo produto a quantidade já existente dentro do carrinho
					$this->Query = "
						SELECT 
							prod.id_prod as IdProd,
							prod.estoque,
							car.quant
						FROM
							carrinho as car
						INNER JOIN
							cliente as cli ON cli.id = car.id_cliente
						INNER JOIN
							produtos as prod ON car.id_prod  = prod.id_prod
						WHERE
							prod.id_prod = $this->Produto
							AND cli.email = '$this->Cliente'
							AND car.id_tam = $this->Tam
					";
				}elseif($Val === 2){
					$this->Query = "
						SELECT 
							id as id_cliente
						FROM
							cliente
						WHERE 
							email = '$this->Cliente'
					";
				}else{
					//Adiciona o produto ao carrinho
					$this->Query = "
						INSERT INTO carrinho(id_prod, id_cliente, quant, id_tam ,data_car)
						VALUES($this->Produto, $this->IdCli, $this->Quantidade, $this->Tam,'$this->Data');
					";
				}
			}
			/*
			*Metodo: pesquisaQuery()
			*Descrição: Retorna valores da Query
			*Data: 31/05/2024
			*Programador(a): Ighor Drummond
			*/
			protected function pesquisaQuery(){
				//Retorna Valores da pesquisa
				$this->stmt = $this->conexao->query($this->Query);
				$Ret = $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
				return $Ret;
			}
		}
		/*
		*Classes: VerCarrinho()
		*Descrição: Classe responsavel por apresentar produtos adicionado ao carrinho para usuário
		*Data: 31/05/2024
		*Programador(a): Ighor Drummond
		*/
		class VerCarrinho{
			//Atributos
			protected $conexao = null;
			protected $stmt = null;
			protected $query = null;

			//Construtor
			public function __construct(
				public $Cliente = ''
			){
				$this->conexao = new \IniciaServer();
				$this->conexao = $this->conexao->conexao();
			}

			//Métodos
			/*
			*Metodo: retornarValores()
			*Descrição: Retorna valores da Query
			*Data: 31/05/2024
			*Programador(a): Ighor Drummond
			*/
			public function retornaValores(){
				$Valores = [];
				$this->montaQuery();

				try{
					$Valores = $this->pesquisaQuery();
				}catch(\PDOException $e){
					echo $e->getMessage();
				}finally{
					return $Valores;
				}
			}
			/*
			*Metodo: montaQuery()
			*Descrição: Responsavel por monta a query
			*Data: 31/05/2024
			*Programador(a): Ighor Drummond
			*/
			protected function montaQuery(){
				$this->query = "
				SELECT 
					prod.id_prod,
					prod.nome as produto,
					prod.estoque,
					prod.promocao,
					prod.promocao_ativo,
					cli.nome,
					car.quant,
					img_prod.img1 as img,
					tam.nome_tam as tamanho,
					FORMAT(CASE
						WHEN 
							prod.promocao_ativo = 1
						THEN 
							prod.promocao * car.quant
						ELSE 
							prod.preco * car.quant
					END, 2, 'pt_BR') as total_item,
					FORMAT(SUM(
						CASE 
							WHEN 
								prod.promocao_ativo = 1
							THEN 
								prod.promocao * car.quant
							ELSE
								prod.preco * car.quant
						END
					) OVER() ,2, 'pt_BR') as total_carrinho
				FROM
					carrinho as car
				INNER JOIN
					produtos as prod ON prod.id_prod = car.id_prod
				INNER JOIN 
					cliente as cli ON cli.id = car.id_cliente
				INNER JOIN
					imagens_prod as img_prod ON img_prod.id_prod = prod.id_prod
				INNER JOIN 
					tamanho as tam ON tam.id_tam = car.id_tam
				WHERE  
					prod.estoque > 0
					AND cli.email = '$this->Cliente'
				GROUP BY
					prod.id_prod, 
					prod.nome, 
					prod.estoque, 
					prod.promocao, 
					prod.promocao_ativo, 
					cli.nome, 
					car.quant, 
					tam.nome_tam,
					img_prod.img1
				";
			}

			/*
			*Metodo: pesquisaQuery()
			*Descrição: Retorna valores da Query
			*Data: 31/05/2024
			*Programador(a): Ighor Drummond
			*/
			protected function pesquisaQuery(){
				//Retorna Valores da pesquisa
				$this->stmt = $this->conexao->query($this->query);
				$Ret = $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
				return $Ret;
			}
		}
	}
?>