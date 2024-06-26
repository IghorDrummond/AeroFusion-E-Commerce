<?php
namespace Compra {
	require_once ('conexao.php');

	/*
	 *Classes: Carrinho()
	 *Descrição: Classe responsavel por colocar produtos no carrinho da pessoa logada
	 *Data: 31/05/2024
	 *Programador(a): Ighor Drummond
	 */
	class AdicionarCarrinho
	{
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
		) {
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
		public function guardaProduto()
		{
			$Ret = '';
			$Valores = [];
			try {
				//Monta Query para pesquisar a quantidade dos produtos
				$this->montaQuery(0);
				$Valores = $this->pesquisaQuery();

				//Valida se o produto foi retornado
				if (isset($Valores[0]['id_prod'])) {
					//Monta Query para verificar se já existe produto no carrinho
					$this->montaQuery(1);
					$Valores = $this->pesquisaQuery();

					if (!isset($Valores[0]['IdProd'])) {
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

						if (($this->conexao->exec($this->Query)) > 0) {
							$Ret = 'OK';
							$this->conexao->commit();
						} else {
							$Ret = 'NAOADICIONADO';
							$this->conexao->rollback();
						}
					} else {
						$Ret = 'CARRINHO';
					}
				} else {
					$Ret = 'PRODUTO';
				}
				//
			} catch (\PDOException $e) {
				echo $e->getMessage();
				$Ret = 'ERROR';
			} finally {
				return $Ret;
			}
		}
		/*
		 *Metodo: montaQuery()
		 *Descrição: Responsavel por monta a query
		 *Data: 31/05/2024
		 *Programador(a): Ighor Drummond
		 */
		protected function montaQuery($Val)
		{
			if ($Val === 0) {
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
			} elseif ($Val === 1) {
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
			} elseif ($Val === 2) {
				$this->Query = "
						SELECT 
							id as id_cliente
						FROM
							cliente
						WHERE 
							email = '$this->Cliente'
					";
			} else {
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
		protected function pesquisaQuery()
		{
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
	class VerCarrinho
	{
		//Atributos
		protected $conexao = null;
		protected $stmt = null;
		protected $query = null;

		//Construtor
		public function __construct(
			public $Cliente = ''
		) {
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
		public function retornaValores()
		{
			$Valores = [];
			$this->montaQuery();

			try {
				$Valores = $this->pesquisaQuery();
			} catch (\PDOException $e) {
				echo $e->getMessage();
			} finally {
				return $Valores;
			}
		}
		/*
		 *Metodo: montaQuery()
		 *Descrição: Responsavel por monta a query
		 *Data: 31/05/2024
		 *Programador(a): Ighor Drummond
		 */
		protected function montaQuery()
		{
			$this->query = "
				SELECT 
					prod.id_prod,
					prod.nome as produto,
					prod.estoque,
					prod.promocao,
					prod.promocao_ativo,
					prod.preco,
					cli.nome,
					car.quant,
					car.id_car,
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
					cli.email = '$this->Cliente'
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
		protected function pesquisaQuery()
		{
			//Retorna Valores da pesquisa
			$this->stmt = $this->conexao->query($this->query);
			$Ret = $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
			return $Ret;
		}
	}
	/*
	 *Classes: QuantCarrinho()
	 *Descrição: Classe responsavel por retornar total de items no carrinho
	 *Data: 01/06/2024
	 *Programador(a): Ighor Drummond
	 */
	class QuantCarrinho
	{
		//Atributo
		protected $query = null;
		protected $stmt = null;
		protected $conexao = null;

		//Construtor
		public function __construct(
			public $Email = ''
		) {
			$this->conexao = new \IniciaServer();
			$this->conexao = $this->conexao->conexao();
		}

		//Métodos
		/*
		 *Metodo: retornarValores()
		 *Descrição: Retorna valores da Query
		 *Data: 01/06/2024
		 *Programador(a): Ighor Drummond
		 */
		public function retornarValores()
		{
			$Valores = [];
			$this->montaQuery();

			try {
				$this->stmt = $this->conexao->query($this->query);
				$Valores = $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
			} catch (\PDOException $e) {
				echo $e->getMessage();
			} finally {
				return $Valores;
			}
		}
		/*
		 *Metodo: montaQuery()
		 *Descrição: Responsavel por monta a query
		 *Data: 01/06/2024
		 *Programador(a): Ighor Drummond
		 */
		protected function montaQuery()
		{
			$this->query = "
					SELECT 
						count(car.id_car) as quant_item
					FROM
						carrinho as car
					INNER JOIN
						produtos as prod ON prod.id_prod = car.id_prod
					INNER JOIN  
						cliente as cli ON cli.id = car.id_cliente
					WHERE
						cli.email = '$this->Email'
				";
		}
	}
	/*
	 *Classes: ApagarItem()
	 *Descrição: Classe responsavel por apagar item do carrinho
	 *Data: 01/06/2024
	 *Programador(a): Ighor Drummond
	 */
	class ApagarItem
	{
		//Atributos
		protected $conexao = null;
		protected $query = null;
		protected $stmt = null;

		//Construtor
		public function __construct(
			public $Cliente = null,
			public $IdCar = null
		) {
			$this->conexao = new \IniciaServer();
			$this->conexao = $this->conexao->conexao();
		}

		//Métodos
		/*
		 *Metodo: apagaDados()
		 *Descrição: Responsavel por deletar o item do carrinho
		 *Data: 01/06/2024
		 *Programador(a): Ighor Drummond
		 */
		public function apagaDados()
		{
			$Ret = 0;

			try {
				//Recupera ID do cliente Logado
				$this->montaQuery(2);
				$this->stmt = $this->conexao->query($this->query);
				$this->stmt = $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
				$this->Cliente = $this->stmt[0]['id_cliente'];
				//Apaga Produto no carrinho
				$this->montaQuery(1);
				$this->conexao->beginTransaction();
				$Ret = $this->conexao->exec($this->query);
				//Valida se foi apagado
				if ($Ret > 0) {
					$this->conexao->commit();
				}
			} catch (\PDOException $e) {
				echo $e->getMessage();
				$this->conexao->rollback();
			} finally {
				return $Ret;
			}
		}
		/*
		 *Metodo: montaQuery(Opção)
		 *Descrição: Responsavel por monta a query
		 *Data: 01/06/2024
		 *Programador(a): Ighor Drummond
		 */
		protected function montaQuery($val)
		{
			if ($val === 1) {
				$this->query = "
						DELETE FROM
							carrinho
						WHERE
							id_car = $this->IdCar
							AND	id_cliente = $this->Cliente									
					";
			} else {
				$this->query = "
						SELECT 
							id as id_cliente
						FROM
							cliente
						WHERE 
							email = '$this->Cliente'
					";
			}
		}
	}
	/*
	 *Classes: atualizaCarrinho()
	 *Descrição: Classe responsavel por atualizar dados do carrinho
	 *Data: 18/06/2024
	 *Programador(a): Ighor Drummond
	 */
	class atualizaCarrinho
	{
		//Atributo
		protected $Query = null;
		protected $conexao = null;
		protected $stmt = null;
		protected $Email = null;
		protected $IdCli = null;
		//Construtor
		public function __construct(
			public $IdCar = '',
			public $Quant = ''
		) {
			$this->conexao = new \IniciaServer();
			$this->conexao = $this->conexao->conexao();
		}
		//Métodos
		/*
		 *Metodo: atualizaQuantidade
		 *Descrição: Responsavel por atualiza a quantidade desejada no carrinho
		 *Data: 18/06/2024
		 *Programador(a): Ighor Drummond
		 */
		public function atualizaQuantidade($Email)
		{
			$this->Email = $Email;
			$Ret = false;
			try {
				//Recupera o Id do Cliente
				$this->montaQuery(1);
				$this->stmt = $this->conexao->query($this->Query);
				$this->stmt = $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
				$this->IdCli = $this->stmt[0]['id'];
				//Atualiza dados do carrinho
				$this->montaQuery(0);
				$this->conexao->beginTransaction();
				if ($this->conexao->exec($this->Query) > 0) {
					$this->conexao->commit();
					$Ret = true;
				}
			} catch (\PDOException $e) {
				echo $e->getMessage();
				$this->conexao->rollback();
			} finally {
				return $Ret;
			}
		}
		/*
		 *Metodo: montaQuery(Opc)
		 *Descrição: Responsavel por monta a query
		 *Data: 18/06/2024
		 *Programador(a): Ighor Drummond
		 */
		private function montaQuery($Val)
		{
			if ($Val === 0) {
				$this->Query = "
					UPDATE
						carrinho
					SET
						quant = $this->Quant
					WHERE
						id_car = $this->IdCar
						AND id_cliente = $this->IdCli
				";
			} else {
				$this->Query = "
					SELECT
						id
					FROM
						cliente
					WHERE
						email = '$this->Email'
					";
			}
		}
	}
}

namespace Pedido {
	require_once ('conexao.php');

	/*
	 *Classes: solicitaPedido
	 *Descrição: Classe responsavel por criar o pedido para usuário
	 *Data: 14/06/2024
	 *Programador(a): Ighor Drummond
	 */
	class solicitaPedido
	{
		//Atributos
		private $conexao = null;
		private $stmt = null;
		private $query = null;

		//Construtor
		function __construct(
			public $Email = '',
			public $Produtos = ''
		) {
			//Inicia conexão
			try {
				$this->conexao = new \IniciaServer();
				$this->conexao = $this->conexao->conexao();
			} catch (\PDOException $e) {
				echo $e->getMessage();
			}
		}

		//Métodos
		/*
		 *Metodo: imprimePedido()
		 *Descrição: imprime os produtos selecionados no carrinho
		 *Data: 14/06/2024
		 *Programador(a): Ighor Drummond
		 */
		public function imprimePedido()
		{
			$Ret = null;

			try {
				$this->montaQuery();//Monta query para validar se existe estoques
				//Valida se os produtos selecionados tem disponibilidade
				$this->stmt = $this->conexao->query($this->query);
				$Ret = $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
			} catch (\PDOException $e) {
				echo $e->getMessage();
			} finally {
				return $Ret;
			}
		}
		/*
		 *Metodo: montaQuery()
		 *Descrição: Responsavel por monta a query
		 *Data: 14/06/2024
		 *Programador(a): Ighor Drummond
		 */
		private function montaQuery()
		{
			$this->query = "
						SELECT 
						    cli.email,
						    cli.id,
						    car.id_car,
						    car.id_prod,
						    car.quant,
						    car.data_car,
						    car.id_tam,
						    prod.nome,
						    prod.preco,
						    prod.estoque,
						    prod.promocao_ativo,
						    prod.promocao,
						    prod.estoque,
                            img_prod.img1,
                            tam.nome_tam,
						    FORMAT(CASE
						        WHEN prod.promocao_ativo = 1 THEN prod.promocao * car.quant
						        ELSE prod.preco * car.quant
						    END, 2, 'pt_BR') AS total_item,
						    FORMAT(SUM(CASE
						        WHEN prod.promocao_ativo = 1 THEN prod.promocao * car.quant
						        ELSE prod.preco * car.quant
						    END) OVER(), 2, 'pt_BR') AS total_carrinho,
						    CASE 
						        WHEN prod.estoque >= car.quant THEN 'SIM'
						        ELSE 'FALTA ESTOQUE'
						    END AS disponibilidade
						FROM 
						    carrinho AS car
						INNER JOIN 
						    cliente AS cli ON cli.id = car.id_cliente
						INNER JOIN 
						    produtos AS prod ON car.id_prod = prod.id_prod
						INNER JOIN  
							imagens_prod as img_prod ON img_prod.id_prod = prod.id_prod
						INNER JOIN 
							tamanho as tam ON tam.id_tam = car.id_tam
						WHERE 
							car.id_car IN($this->Produtos)
							AND cli.email = '$this->Email'
					";
		}
	}

	/*
	 *Classes: validaEstoque
	 *Descrição: Classe responsavel por validar estoque de um produto
	 *Data: 18/06/2024
	 *Programador(a): Ighor Drummond
	 */
	class validaEstoque
	{
		//Atributos
		protected $Produto = null;
		protected $IdCar = null;
		protected $con = null;
		protected $query = null;
		protected $stmt = null;
		protected $IdCli = null;
		protected $Email = null;

		//Métodos
		/*
		 *Metodo: verificaEstoque()
		 *Descrição: Responsavel por verificar se tem a quantidade desejada no estoque do produto
		 *Data: 18/06/2024
		 *Programador(a): Ighor Drummond
		 */
		public function verificaEstoque($IdCar, $Quant, $Email)
		{
			$Ret = false;

			$this->IdCar = $IdCar;
			$this->Email = $Email;
			try {
				//Inicia conexão com o banco
				$this->con = new \IniciaServer();
				$this->con = $this->con->conexao();
				//Recupera o Id do Cliente
				$this->montaQuery(1);
				$this->IdCli = $this->retornaValores()[0]['id'];
				//Recupera id do Produto
				$this->montaQuery(2);
				//Guarda Id do produto retornado
				$this->Produto = $this->retornaValores()[0]['id_prod'];
				//Monta a query responsavel por retorna a quantidade de estoque
				$this->montaQuery(0);
				//Retorna verdadeiro ou falso caso tiver estoque
				$Ret = $this->retornaValores()[0]['estoque'] >= (int) $Quant ? true : false;
			} catch (\PDOException $e) {
				echo $e->getMessage();
			} finally {
				return $Ret;
			}
		}
		/*
		 *Metodo: montaQuery(Opção)
		 *Descrição: Responsavel por monta a query
		 *Data: 18/06/2024
		 *Programador(a): Ighor Drummond
		 */
		private function montaQuery($Val)
		{
			if ($Val === 0) {
				$this->query = "
						SELECT
							prod.estoque
						FROM
							produtos as prod
						WHERE 	
							prod.id_prod = $this->Produto
					";
			} else if ($Val === 1) {
				$this->query = "
						SELECT
							id
						FROM
							cliente
						WHERE
							email = '$this->Email'
					";
			} else {
				$this->query = "
						SELECT
							id_prod
						FROM
							carrinho
						WHERE 
							id_car = $this->IdCar;
							AND id_cliente = $this->IdCli
					";
			}
		}
		/*
		 *Metodo: retornaValores
		 *Descrição: Responsavel por retornar valores
		 *Data: 18/06/2024
		 *Programador(a): Ighor Drummond
		 */
		private function retornaValores()
		{
			$this->stmt = $this->con->query($this->query);
			return $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
		}
	}

	/*
	 *Classes: novoPedido
	 *Descrição: 
	 *Data: 19/06/2024
	 *Programador(a): Ighor Drummond
	 */
	class novoPedido
	{
		// Constantes
		const STATUS = 1;
		// Atributos
		protected $con = null;
		protected $stmt = null;
		protected $query = null;
		protected $IdCli = null;
		protected $IdPed = null;
		protected $IdProd = null;
		protected $IdCar = null;
		protected $semEstoque = null;
		protected $Data = null;
		private $Total = null;
		private $Quant = null;

		// Construtor
		public function __construct(
			public $Email = '',
			public $Produtos = '',
			public $Endereco = '',
			public $Pagamento = '',
			public $Cupom = ''
		) {
			try {
				$this->con = new \IniciaServer();
				$this->con = $this->con->conexao();
			} catch (\PDOException $e) {
				echo $e->getMessage();
				$this->__destruct();
			}
		}

		// Destruidor
		public function __destruct()
		{
			return false;
		}

		// Métodos
		/*
		 *Metodo: setPedidos
		 *Descrição: Responsavel por adicionar novo pedido
		 *Data: 18/06/2024
		 *Programador(a): Ighor Drummond
		 */
		public function setPedido()
		{
			$Ret = [
				"Inclusão" => false,
				"sem_estoque" => "",
				"Pedido" => "",
				"Total" => 0.0,
				"Cupom" => ''
			];

			try {
				date_default_timezone_set('America/Sao_Paulo'); // Configura data e hora do servidor
				// Responsável por retorna os IDs dos produtos correspondente ao carrinho do usuário
				$this->montaQuery(0); // Monta query para retorna os items do pedido
				$this->getDados(); // Recupera os dados
				// Valida se trouxe os dados correspondentes
				if (isset($this->stmt[0]['id_cliente'])) {
					$this->IdCli = strval($this->stmt[0]['id_cliente']); // Recebe o Id do Cliente
					// Recebe o total do pedido
					$this->Total = str_replace(',', '.', $this->stmt[0]['total_carrinho']);
					$Ret['Total'] = $this->Total;
					//Valida se tem cupom e se o mesmo está ativo para o produto
					if (!empty($this->Cupom)) {
						$aux = $this->stmt;//Guarda produtos numa variavel auxiliar
						$this->montaQuery(5);
						//Faz a busca do cupom
						$this->getDados();
						//Valida se o cupom existe e se o mesmo está ativo
						if (isset($this->stmt[0]['cupom_ativo']) and $this->stmt[0]['cupom_ativo'] === 1) {
							//Valida se o cupom está vencido
							if ($this->validaData($this->stmt[0]['data_validade'])) {
								//Percorre o cupom para validar se existe um produto valido no pedido para ser aplicado o desconto
								for ($nCont = 0; $nCont <= count($this->stmt) - 1; $nCont++) {
									for ($nCont2 = 0; $nCont2 <= count($aux) - 1; $nCont2++) {
										if ($this->stmt[$nCont]['id_prod'] === $aux[$nCont2]['id_prod']) {
											//Subtraí o total pelo o valor do produto e sua quantidade
											$this->Total = strval((float) $this->Total - ((float) $this->stmt[$nCont]['valor_desconto'] * $aux[$nCont2]['quant']));
											$Ret['Cupom'] = $this->Cupom;//Guarda cupom valido
										}
									}
								}
								$Ret['Total'] = $this->Total;//Guarda o novo total
							}
						} else {
							$this->Cupom = "";//Apaga cupom pois o mesmo ou não existe ou não está ativo
						}
					}
					// Guarda data do novo pedido
					$this->Data = date('Y-m-d H:i:s');
					// Cria um novo pedido com os dados informados
					$this->montaQuery(3);
					$this->con->beginTransaction();
					if ($this->con->exec($this->query) > 0) {
						$this->IdPed = $this->con->lastInsertId();
						$this->con->commit();
						$Ret['Pedido'] = $this->IdPed; // Insere o id do novo pedido feito
					} else {
						throw new \PDOException("Falha ao inserir o pedido.");
					}

					// Vai dar a baixa na quantidade dos produtos e adicionar o item do pedido
					foreach ($this->stmt as $Dados) {
						if ($Dados['disponibilidade'] === 'SIM') {
							// Da baixa no produto
							$this->Quant = strval(abs((int) $Dados['quant'] - (int) $Dados['estoque']));
							$this->IdProd = $Dados['id_prod'];
							$this->montaQuery(1); // Monta query para dar a baixa no estoque dos produtos
							$this->pushDados(); // Atualiza estoque do produto

							// Adiciona produto ao item do pedido
							$this->montaQuery(4);
							$this->query .= PHP_EOL . "VALUES($this->IdProd, $this->IdPed," . $Dados['quant'] . ", " . str_replace(',', '.', $Dados['total_item']) . ")";
							$this->pushDados();

							// Apaga produto do carrinho do cliente
							$this->IdCar = $Dados['id_car'];
							$this->montaQuery(2);
							$this->pushDados();
						} else {
							$Ret['sem_estoque'] .= $Dados['id_prod'] . ";"; // Guarda Produto sem estoque
						}
					}
					// Formata produtos sem estoque caso houver
					$Ret['sem_estoque'] = strlen($Ret['sem_estoque']) > 0 ? substr($Ret['sem_estoque'], 0, strlen($Ret['sem_estoque']) - 1) : '';
					// Retorna operação concluída
					$Ret['Inclusão'] = true;
				}
			} catch (\PDOException $e) {
				echo $e->getMessage();
				$this->con->rollBack();
				$this->__destruct();
			} finally {
				return $Ret;
			}
		}
		/*
		 *Metodo: getDados()
		 *Descrição: Responsavel por receber dados da consulta
		 *Data: 18/06/2024
		 *Programador(a): Ighor Drummond
		 */
		private function getDados()
		{
			try {
				$this->stmt = $this->con->query($this->query);
				$this->stmt = $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
			} catch (\PDOException $e) {
				echo $e->getMessage();
				$this->__destruct();
			}
		}
		/*
		 *Metodo: pushDados()
		 *Descrição: Responsavel por cadastrar dados no banco
		 *Data: 18/06/2024
		 *Programador(a): Ighor Drummond
		 */
		private function pushDados()
		{
			try {
				$this->con->beginTransaction();
				if ($this->con->exec($this->query) > 0) {
					$this->con->commit();
					return true;
				} else {
					throw new \PDOException("Falha ao dar push no dado.");
				}
			} catch (\PDOException $e) {
				echo $e->getMessage();
				$this->con->rollback();
				return false;
			}
		}
		/*
		 *Metodo: montaQuery(Opção)
		 *Descrição: Responsavel por montar as querys
		 *Data: 18/06/2024
		 *Programador(a): Ighor Drummond
		 */
		private function montaQuery($Opc)
		{
			if ($Opc === 0) {
				$this->query = "
						SELECT 
							cli.email,
							cli.id as id_cliente,
							car.id_car,
							car.id_prod,
							car.quant,
							prod.estoque,
							FORMAT(
								CASE
									WHEN prod.promocao_ativo = 1 THEN prod.promocao * car.quant
									ELSE prod.preco * car.quant
								END, 2, 'pt_BR'
							) AS total_item,
							FORMAT(
								(
									SELECT SUM(
										CASE
											WHEN prod_inner.promocao_ativo = 1 THEN prod_inner.promocao * car_inner.quant
											ELSE prod_inner.preco * car_inner.quant
										END
									)
									FROM carrinho AS car_inner
									INNER JOIN produtos AS prod_inner ON car_inner.id_prod = prod_inner.id_prod
									WHERE car_inner.id_cliente = car.id_cliente
									  AND car_inner.id_car IN($this->Produtos)
									  AND prod_inner.estoque >= car_inner.quant
								), 2, 'pt_BR'
							) AS total_carrinho,
							CASE 
								WHEN prod.estoque >= car.quant THEN 'SIM'
								ELSE 'FALTA ESTOQUE'
							END AS disponibilidade
						FROM 
							carrinho AS car
						INNER JOIN 
							cliente AS cli ON cli.id = car.id_cliente
						INNER JOIN 
							produtos AS prod ON car.id_prod = prod.id_prod
						WHERE 
							cli.email = '$this->Email'
							AND car.id_car IN($this->Produtos)
					";
			} else if ($Opc === 1) {
				$this->query = "
						UPDATE 
							produtos
						SET
							estoque = $this->Quant
						WHERE
							id_prod = $this->IdProd
					";
			} else if ($Opc === 2) {
				$this->query = "
						DELETE FROM
							carrinho
						WHERE
							id_car = $this->IdCar
							AND id_cliente = $this->IdCli
					";
			} else if ($Opc === 3) {
				$this->query = "
						INSERT INTO pedidos(valor_total, data_pedido, id_cliente, id_end, status, id_form, )
						VALUES($this->Total, '$this->Data', $this->IdCli, $this->Endereco, " . strval(self::STATUS) . ", $this->Pagamento, '$this->Cupom')
					";
			} else if ($Opc === 4) {
				$this->query = "
						INSERT INTO item_pedidos(id_prod, id_ped, quant, preco_item)
					";
			} else if ($Opc === 5) {
				$this->query = "
					SELECT
						REPLACE(nome_cupom, ' ', '') as nome_cupom,
						cupom_ativo,
						data_inicio,
						data_validade,
						valor_desconto,
						id_prod
					FROM
						cupons
					WHERE
						nome_cupom = '$this->Cupom'
					";
			}
		}
		/*
		 *Metodo: validaData(Data de vencimento do cupom)
		 *Descrição: Responsavel por validar se já está vencido o cupom
		 *Data: 26/06/2024
		 *Programador(a): Ighor Drummond
		 */
		private function validaData($Data)
		{
			// Valida se a data de validade é menor que a data atual 
			$DataFinal = strtotime($Data);
			$DataAtual = strtotime(date('Y-m-d H:i:s'));
			$DataAtual > $DataFinal ? true : false;
		}
	}
	/*
	 *Classes: Cupons
	 *Descrição: Valida se o cupom está ativo ou não
	 *Data: 26/06/2024
	 *Programador(a): Ighor Drummond
	 */
	class validaCupom
	{
		//Atributos
		private $con = null;
		private $stmt = null;
		private $query = null;
		private $Total = null;

		//Construtor
		public function __construct(
			public $Email = "",
			public $Cupom = "",
			public $Produtos = ""
		) {
			try {
				$this->con = new \IniciaServer();
				$this->con = $this->con->conexao();
			} catch (\PDOException $e) {
				echo $e->getMessage();
			}
		}

		//Métodos
		/*
		 *Metodo: validaCupom()
		 *Descrição: Responsavel por atualizar total pelo cupom
		 *Data: 26/06/2024
		 *Programador(a): Ighor Drummond
		 */
		public function validaCupom()
		{
			$this->montaQuery(0);
			$this->getDados();

			if (isset($this->stmt[0]['total_carrinho'])) {
				$aux = $this->stmt;//Guarda todos os produtos do pedido
				$this->Total = $aux[0]['total_carrinho'];//Guarda total do carrinho
				//Prepara para validar cupom
				$this->montaQuery(1);	
				$this->getDados();
				//Valida se tem cupom e se o mesmo está ativo para o produto
				if (isset($this->stmt[0]['nome_cupom'])) {
					//Valida se o cupom existe e se o mesmo está ativo
					if (isset($this->stmt[0]['cupom_ativo']) and $this->stmt[0]['cupom_ativo'] === 1) {
						//Valida se o cupom está vencido
						if ($this->validaData($this->stmt[0]['data_validade'])) {
							//Percorre o cupom para validar se existe um produto valido no pedido para ser aplicado o desconto
							for ($nCont = 0; $nCont <= count($this->stmt) - 1; $nCont++) {
								for ($nCont2 = 0; $nCont2 <= count($aux) - 1; $nCont2++) {
									if ($this->stmt[$nCont]['id_prod'] === $aux[$nCont2]['id_prod']) {
										//Subtraí o total pelo o valor do produto e sua quantidade
										$this->Total = strval((float) $this->Total - ((float) $this->stmt[$nCont]['valor_desconto'] * $aux[$nCont2]['quant']));
									}
								}
							}
						}else{
							echo 'VENCIDO';
						}
					} else {
						echo "INVALIDO";
					}
				}

				return $this->Total;
			}
		}
		/*
		 *Metodo: getDados()
		 *Descrição: Responsavel por receber dados da consulta
		 *Data: 18/06/2024
		 *Programador(a): Ighor Drummond
		 */
		private function getDados()
		{
			try {
				$this->stmt = $this->con->query($this->query);
				$this->stmt = $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
			} catch (\PDOException $e) {
				echo $e->getMessage();
			}
		}
		/*
		 *Metodo: montaQuery(Opção)
		 *Descrição: Responsavel por montar as querys
		 *Data: 26/06/2024
		 *Programador(a): Ighor Drummond
		 */
		private function montaQuery($Opc)
		{
			if ($Opc === 1) {
				$this->query = "
						SELECT 
							car.id_prod,
							car.quant,
							FORMAT(
								(
									SELECT SUM(
										CASE
											WHEN prod_inner.promocao_ativo = 1 THEN prod_inner.promocao * car_inner.quant
											ELSE prod_inner.preco * car_inner.quant
										END
									)
									FROM carrinho AS car_inner
									INNER JOIN produtos AS prod_inner ON car_inner.id_prod = prod_inner.id_prod
									WHERE car_inner.id_cliente = car.id_cliente
									  AND car_inner.id_car IN($this->Produtos)
									  AND prod_inner.estoque >= car_inner.quant
								), 2, 'pt_BR'
							) AS total_carrinho
						FROM 
							carrinho AS car
						INNER JOIN 
							cliente AS cli ON cli.id = car.id_cliente
						INNER JOIN 
							produtos AS prod ON car.id_prod = prod.id_prod
						WHERE 
							cli.email = '$this->Email'
							AND car.id_car IN($this->Produtos)					
					";
			} else {
				$this->query = "
						SELECT
							REPLACE(nome_cupom, ' ', '') as nome_cupom,
							cupom_ativo,
							data_inicio,
							data_validade,
							valor_desconto,
							id_prod
						FROM
							cupons
						WHERE
							nome_cupom = '$this->Cupom'
					";
			}
		}
		/*
		 *Metodo: validaData(Data de vencimento do cupom)
		 *Descrição: Responsavel por validar se já está vencido o cupom
		 *Data: 26/06/2024
		 *Programador(a): Ighor Drummond
		 */
		private function validaData($Data)
		{
			// Valida se a data de validade é menor que a data atual 
			$DataFinal = strtotime($Data);
			$DataAtual = strtotime(date('Y-m-d H:i:s'));
			$DataAtual > $DataFinal ? true : false;
		}
	}
}
?>