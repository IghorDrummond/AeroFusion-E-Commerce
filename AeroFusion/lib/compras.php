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
		public $Email = null;
		public $Produtos = null;
		public $Endereco = null;
		public $Pagamento = null;
		public $Cupom = null;
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
		public function setPedido(
			$Email = '',
			$Produtos = '',
			$Endereco = '',
			$Pagamento = '',
			$Cupom = '',
		)
		{
			$this->Email = $Email;
			$this->Produtos = $Produtos;
			$this->Endereco = $Endereco;
			$this->Pagamento = $Pagamento;
			$this->Cupom = $Cupom;

			$Ret = [
				"Inclusão" => false,
				"sem_estoque" => "",
				"Pedido" => "",
				"Total" => 0.0,
				"Cupom" => ''
			];

			try{
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
						//Guarda dados anteriores
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
						$Ret['Pedido'] = $this->IdPed; // Guarda o id do novo pedido
					} else {
						throw new \PDOException("Falha ao inserir o pedido.");
					}
					// Vai dar a baixa na quantidade dos produtos e adicionar o item do pedido
					foreach ($this->stmt as $nCont => $Dados) {
						if ($Dados['disponibilidade'] === 'SIM') {
							// Da baixa no produto
							$this->Quant = strval(abs((int) $Dados['quant'] - (int) $Dados['estoque']));
							$this->IdProd = $Dados['id_prod'];
							$this->montaQuery(1); // Monta query para dar a baixa no estoque dos produtos
							$this->pushDados(); // Atualiza estoque do produto

							// Adiciona produto ao item do pedido
							$this->montaQuery(4);
							$this->query .= PHP_EOL . "VALUES($this->IdProd, $this->IdPed," . $Dados['quant'] . ", " . str_replace(',', '.', $Dados['total_item']) . ", {$Dados['id_tam']}  )";
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
		 *Metodo: getPedido()
		 *Descrição: Responsavel por retornar pedido existente
		 *Data: 02/07/2024
		 *Programador(a): Ighor Drummond
		 */
		public function getPedido(
			$IdPed,
			$Email
		){	
			$this->IdPed = $IdPed;
			$this->Email = $Email;
			$this->montaQuery(6);
			$this->getDados();
			return $this->stmt;
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
							car.id_tam,
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
				$this->Cupom = strtoupper($this->Cupom);
				$this->query = "
						INSERT INTO pedidos(valor_total, data_pedido, id_cliente, id_end, status, id_form, nome_cupom)
						VALUES($this->Total, '$this->Data', $this->IdCli, $this->Endereco, " . strval(self::STATUS) . ", $this->Pagamento, '$this->Cupom')
					";
			} else if ($Opc === 4) {
				$this->query = "
						INSERT INTO item_pedidos(id_prod, id_ped, quant, preco_item, id_tam)
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
			}else if($Opc === 6){
				$this->query = "
					SELECT
						Cp.nome_cupom,
						Pd.id_ped,
						Pd.id_cliente,
						Pd.id_form,
						FORMAT(Pd.valor_total, 2, 'pt_BR') as valor_total,
						Pd.id_end,
						Pd.data_pedido,
						Ip.id_prod, 
						Ip.quant,
						Ip.preco_item,
						Prod.nome,
						Prod.promocao_ativo,
						FORMAT(Prod.promocao, 2, 'pt_BR') as promocao,
						FORMAT(Prod.preco, 2, 'pt_BR') as preco,
						Img.img1,
						cat.nome_cat,
						tam.nome_tam,
						fm.forma_pag,
						cli.email,
						st.nome as status_,
                        en.rua,
                        en.bairro,
                        en.cidade,
                        en.numero,
                        en.complemento,
                        en.referencia,
                        en.cep,
                        en.uf
					FROM
						Pedidos as Pd
					LEFT JOIN
						cupons as Cp ON Cp.nome_cupom = Pd.nome_cupom
					LEFT JOIN
						cliente as cli ON cli.id = Pd.id_cliente
					LEFT JOIN
						endereco as en ON en.id_end = Pd.id_end
					INNER JOIN 
						item_pedidos as Ip ON Ip.id_ped = Pd.id_ped
					INNER JOIN 
						produtos as Prod ON Prod.id_prod = Ip.id_prod
					INNER JOIN 
						imagens_prod as Img ON Img.id_prod = Prod.id_prod
					INNER JOIN 
						categoria as cat ON cat.id_cat = Prod.id_cat
					INNER JOIN
						tamanho as tam ON tam.id_tam = Ip.id_tam
					INNER JOIN 
						forma_pagamento as fm ON fm.id_form = Pd.id_form
					INNER JOIN
						status as st ON st.id_sta = Pd.status
					WHERE
						Pd.id_ped = $this->IdPed
						AND cli.email = '$this->Email'
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
			$Ret = '';
			$this->montaQuery(1);
			$this->getDados();

			if (isset($this->stmt[0]['total_carrinho'])) {
				$aux = $this->stmt;//Guarda todos os produtos do pedido
				$this->Total = $aux[0]['total_carrinho'];//Guarda total do carrinho
				//Prepara para validar cupom
				$this->montaQuery(0);	
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
							$Ret = "Total: R$ " . $this->Total;
						}else{
							$Ret =  'VENCIDO';
						}
					} else {
						$Ret = "INVALIDO";
					}
				}
			}

			return $Ret;
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


	/*
	 *Classes: MetodoPagamento
	 *Descrição: Guarda o método de pagamento para o pedido efetuado
	 *Data: 29/06/2024
	 *Programador(a): Ighor Drummond
	 */
	class MetodoPagamento
	{
		//Atributos
		private $con = null;
		private $Query = null;
		private $stmt = null;
		private $IdCli = null;

		//Construtor
		public function __construct(
			public $IdPed = null,
			public $Email = null,
			public $Metodo = null
		)
		{	
			//Inicia a Conexão com o Banco de Dados
			$this->con = new \IniciaServer();
			$this->con = $this->con->conexao(); 
		}

		//Métodos
		/*
		 *Metodo: setPagamento()
		 *Descrição: guarda o novo método de pagamento junto com o seu valor parcelado (caso for parcelamento)
		 *Data: 29/06/2024
		 *Programador(a): Ighor Drummond
		 */
		public function setPagamento(){
			//Recupera ID
			$this->montaQuery(2);
			$this->IdCli = $this->getDados()[0]['id'];
			//Guarda Método de pagamento
			$this->montaQuery(1);
			$this->setDados();
		}
		/*
		 *Metodo: montaQuery(Opção)
		 *Descrição: Responsavel por montar as querys
		 *Data: 29/06/2024
		 *Programador(a): Ighor Drummond
		 */
		private function montaQuery($Opc){
			if($Opc === 1) {
				$this->Query = "
				UPDATE 
					pedidos
				SET
					id_form = $this->Metodo
				WHERE
					id_ped = $this->IdPed
					AND id_cliente = $this->IdCli
				";
			}else if($Opc === 2){
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
		/*
		 *Metodo: getDados()
		 *Descrição: Responsavel por receber dados da consulta
		 *Data: 03/07/2024
		 *Programador(a): Ighor Drummond
		 */
		private function getDados()
		{
			$Ret = [];
			try {
				$this->stmt = $this->con->query($this->Query);
				$Ret = $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
			} catch (\PDOException $e) {
				echo $e->getMessage();
			}finally{
				return $Ret;
			}
		}
		/*
		 *Metodo: setDados()
		 *Descrição: Responsavel por enviar dados ao banco
		 *Data: 03/07/2024
		 *Programador(a): Ighor Drummond
		 */
		private function setDados()
		{
			try {
				$this->con->beginTransaction();
				$this->con->exec($this->Query);
				$this->con->commit();
			} catch (\PDOException $e) {
				echo $e->getMessage();
				$this->con->rollBack();
			}
		}
	}

	/*
	 *Classes: Boleto
	 *Descrição: Gera boleto bradesco para o usuário
	 *Data: 09/07/2024
	 *Programador(a): Ighor Drummond
	 */
	class Boleto
	{
		//Constantes
		const segundos = 86400;
		const dias_de_prazo_para_pagamento = 5;
		const taxa_boleto = 2.95;
		const data_venc = date("d/m/Y", time() + (self::dias_de_prazo_para_pagamento * self::segundos));
		const nosso_numero = "75896452";
		const numero_documento = self::nosso_numero;
		const agencia = "1100";
		const agendica_dg = "1";
		const conta = "0102003";
		const conta_dg = "4";
		const carteira = "4";
		const identificador = "AeroFusion - Desenvolvido por Ighor Drummond - 2024";
		const cnpj = "61.416.543/0001-89";
		const nome_emp = "AeroFusion Company LTDA.";
		const contato = "suporte@aerofusion.com";
		const codigoBanco = "237";
		const nummoeda = "9";


		//Atributos
		private $valor_boleto = null;
		private $dadosboleto = [];
		private $con = null;
		private $Query = null;
		private $stmt = null;
		private $fator_vencimento = null;
		private $codigo_banco_com_dv = null;

		//Construtor
		function __construct(
			public $IdPed = null,
			public $Email = null,
			public $valor_cobrado = null,
		)
		{
			//Inicia conexão com o banco de dados
			$this->con = new \IniciaServer();
			$this->con = $this->con->conexao();

			//Formata virgula para ponto
			$this->valor_cobrado = number_format(',', '.', $this->valor_cobrado);
			//Cacula valor cobrado pela taxa do boleto
			$this->valor_boleto = number_format($this->valor_cobrado + self::taxa_boleto, 2, ',', '');

			//Inserir cabeçalho do boleto
			$this->dadosboleto['nosso_numero'] = self::nosso_numero;
			$this->dadosboleto['numero_documento'] = self::numero_documento;
			$this->dadosboleto['data_vencimento'] = self::data_venc;
			$this->dadosboleto['tada_documento'] = date("d/m/Y");
			$this->dadosboleto['data_processamento'] = date("d/m/Y");
			$this->dadosboleto['valor_boleto'] = $this->valor_cobrado;

			//Dados do cliente
			$this->montaQuery(0);
			$this->getDados();//Retorna dados do cliente logado
			$this->dadosboleto['sacado'] = $this->stmt[0]['nome'];
			$this->dadosboleto['endereco1'] = ($this->stmt[0]['rua'] . " - " . $this->stmt[0]['bairro']);
			$this->dadosboleto['endereco2'] = ($this->stmt[0]['cidade'] . " - " .  $this->stmt[0]['uf'] . " - CEP: " . $this->stmt[0]['cep'] );

			//Informações para cliente
			$this->dadosboleto['demonstrativo1'] = "Pagamento de Compra na Loja " . self::nome_emp;
			$this->dadosboleto['demonstrativo2'] = "Mensalidade referente a " . self::nome_emp . " Taxa Bancária - R$ ". number_format(self::taxa_boleto, 2, ',', '');
			$this->dadosboleto['demonstrativo3'] = self::identificador;
			$this->dadosboleto["instrucoes1"] = "- Sr. Caixa, cobrar multa de 2% após o vencimento";
			$this->dadosboleto["instrucoes2"] = "- Receber até 10 dias após o vencimento";
			$this->dadosboleto["instrucoes3"] = "- Em caso de dúvidas entre em contato conosco: " . self::contato;
			$this->dadosboleto["instrucoes4"] = "&nbsp; " . self::identificador;
			
			//Dados do boleto bancário especifico
			$this->dadosboleto["quantidade"] = "001";
			$this->dadosboleto["valor_unitario"] = $this->valor_boleto;
			$this->dadosboleto["especie"] = "R$";
			$this->dadosboleto["especie_doc"] = "DS";			

			//imprime items e seus valores

			// Dados da conta Bradesco
			$this->dadosboleto["agencia"] = self::agencia;//Num da agencia, sem digito
			$this->dadosboleto["agencia_dv"] = self::agendica_dg;// Digito do Num da agencia
			$this->dadosboleto["conta"] = self::conta;// Num da conta, sem digito
			$this->dadosboleto["conta_dv"] = self::conta_dg;// Digito do Num da conta

			// Dados da conta do cliente
			$this->dadosboleto["conta_cedente"] = self::conta; // ContaCedente do Cliente, sem digito (Somente Números)
			$this->dadosboleto["conta_cedente_dv"] = self::conta_dg; // Digito da ContaCedente do Cliente
			$this->dadosboleto["carteira"] = self::carteira;  // Código da Carteira: pode ser 06 ou 03

			//Dados extras do boleto
			$this->dadosboleto["identificacao"] = self::identificador;
			$this->dadosboleto["cpf_cnpj"] = self::cnpj;
			$this->dadosboleto["endereco"] = "Avenida São paulo";
			$this->dadosboleto["cidade_uf"] = "São Paulo / SP";
			$this->dadosboleto["cedente"] = self::nome_emp;						
		}

		//Métodos
		/*
		 *Metodo:  getDadosBoleto()
		 *Descrição: Responsavel por retornar dados do boleto
		 *Data: 09/07/2024
		 *Programador(a): Ighor Drummond
		 */
		public function getDadosBoleto(){
			return $this->dadosboleto;
		}

		/*
		 *Metodo: getBoleto()
		 *Descrição: Responsavel por preparar e retornar o boleto
		 *Data: 09/07/2024
		 *Programador(a): Ighor Drummond
		 */
		public function getBoleto(){
			//Formata números para um padrão
			$this->dadosboleto['codigo_banco_com_dv'] = $this->geraCodigo();
			$this->fator_vencimento = $this->fator_vencimento();
			//valor tem 10 digitos sem vírgula
			$this->dadosboleto['valor_boleto'] = $this->formata_numero($this->dadosboleto['valor_boleto'], 10, '0', 1);
			//agencia tem 4 digitos
			$this->dadosboleto['agencia'] = $this->formata_numero($this->dadosboleto['agencia'], 4, '0', 0);
			//conta tem 6 digitos
			$this->dadosboleto['conta'] = $this->formata_numero($this->dadosboleto['conta'], 6, '0', 0);
			//digitod a conta tem 1 digito
			$this->dadosboleto['conta_dv'] = $this->formata_numero($this->dadosboleto['conta_dv'], 1, '0', 0);
			//nosso número tem 11 digitos com o digito
			$this->dadosboleto['nosso_numero'] = $this->formata_numero(self::carteira, 2, '0', 0) . $this->formata_numero(self::nosso_numero, 11, '0', 0);
			//dv do nosso numero
			$this->dadosboleto['nosso_numero_dv'] = $this->digito_verificador($this->dadosboleto['nosso_numero'], 0);
			//conta cedente com 7 digitos
			$this->dadosboleto['conta_cedente'] = $this->formata_numero($this->dadosboleto['conta_cedente'], 7, '0', 0);
			//conta cedente com digito
			$this->dadosboleto['conta_cedente_dv'] = $this->formata_numero($this->dadosboleto['conta_cedente'], 1, '0', 0);
			//formatar digito verificador
			$digito_barra = self::codigoBanco . self::nummoeda . $this->fator_vencimento . $this->dadosboleto['valor_boleto'] . $this->dadosboleto['agencia'] . $this->dadosboleto['nosso_numero_dv'] . $this->dadosboleto['conta_cedente'] . "0";
			//43 numeros para o calculo do digito de verificador do código de barras
			$digito_barra = $this->digito_verificador($digito_barra, 2);
			//agora nova linha com o codigo de barra completo com 44 digitos
			$this->dadosboleto['codigo_barras'] =  self::codigoBanco . self::nummoeda . $digito_barra . $this->fator_vencimento . $this->dadosboleto['valor_boleto'] . $this->dadosboleto['agencia'] . $this->dadosboleto['nosso_numero_dv'] . $this->dadosboleto['conta_cedente'] . "0";
			//Envia dados importantes
			$this->dadosboleto['linha_digitavel'] = monta_linha_digitavel($this->dadosboleto['codigo_barras']);
			$this->dadosboleto['agencia_codigo'] = "{$this->dadosboleto['agencia']}-{$this->dadosboleto['agencia_dv']} / {$this->dadosboleto['conta_cedente']}-{$this->dadosboleto['conta_cedente_dv']}"; 
			$this->dadosboleto['nosso_numero'] = substr($this->dadosboleto['nosso_numero'], 0, 2) . '/' . substr($this->dadosboleto['nosso_numero'], 2). '-' . $this->dadosboleto['nosso_numero_dv']; 
			/*
				lista para eu (ighor) aplicar dentro da minha classe conforme o manual de boletos do bradesco.
				adicionar _daytop
				adicionar monta_linha_digitavel
				adicionar modulo_10
			*/
		}

		/*
		 *Metodo: getDados()
		 *Descrição: Responsavel por receber dados de uma pesquisa ao banco de dados
		 *Data: 09/07/2024
		 *Programador(a): Ighor Drummond
		 */
		private function getDados(){
			$this->stmt = null;
			try{
				$this->stmt = $this->con->query($this->Query);
				$this->stmt = $this->stmt->fetch(\PDO::FETCH_ASSOC);
			}catch(\PDOException $e){
				echo $e->getMessage();
			}
		}

		/*
		 *Metodo: montaQuery(Opção)
		 *Descrição: Responsavel por montar as querys
		 *Data: 09/07/2024
		 *Programador(a): Ighor Drummond
		 */
		private function montaQuery($Opc){
			if($Opc === 0){
				$this->Query = "
					SELECT
						cli.nome,
						cli.id,
						en.rua,
						en.bairro,
						en.cidade,
						en.cep,
						en.uf
					FROM
						cliente
					INNER JOIN 
						endereco as en ON cli.id = en.id_cliente
					WHERE
						email = '$this->Email'
				";				
			}
		}

		/*
		 *Metodo: geraCodigo()
		 *Descrição: Responsavel por gerar o código
		 *Data: 09/07/2024
		 *Programador(a): Ighor Drummond
		 */
		private function geraCodigo(){
			$Ret = "";
			$Ret = substr(self::codigoBanco, 0, 3);//Recebe o código do banco
			return $Ret .= " - " . $this->modulo_11($Ret,9,0);
		}

		/*
		 *Metodo: fator_vencimento()
		 *Descrição: Responsavel por calcular o fator do vencimento
		 *Data: 09/07/2024
		 *Programador(a): Ighor Drummond
		 */
		private function fator_vencimento(){
			$data = explode("/", self::data_venc);
			$dia = $data[0];
			$mes = $data[1];
			$ano = $data[2];
			return(abs(($this->_dateToDays("1997","10","07")) - ($this->_dateToDays($ano, $mes, $dia))));
		}

		/*
		 *Metodo: formata_numero(numero a ser formatado, loop, dado inserido, opção a ser escolhida)
		 *Descrição: Responsavel por formata numeros para um valor padrão
		 *Data: 09/07/2024
		 *Programador(a): Ighor Drummond
		 */
		private function formata_numero($numero, $limite, $insert, $Opc){
			switch ($Opc) {
				case 1:
					$numero = str_replace(",", ".", $numero);
					break;
			}
			//Formata numero
			for($nCont = 0; $nCont <= $limite; $nCont++ ){
				$numero = $insert . $numero;
			}
			//Retorna numero formatado
			return $numero;
		}

		/*
		 *Metodo: digito_verificador(valor a ser verificado, opção)
		 *Descrição: 
		 *Data: 10/07/2024
		 *Programador(a): Ighor Drummond
		 */
		private function digito_verificador($valor, $opc){
			$resto = 0;
			$digito = 0;
			$Ret = null;
			//Verificador para nosso numero
			if($opc === 1){	
				$digito = 11 - ($this->modulo_11($valor, 7, 1));

				if($digito === 10){
					$Ret = "P";
				}else if($digito === 11){
					$Ret = 0;
				}else{
					$Ret = $digito;
				}
			}else if($opc === 2){//Verificador para barra
				$resto = $this->modulo_11($valor, 9, 1); 
				if($resto === 0 or $resto === 1 or $resto === 10){
					$Ret = 1;
				}else{
					$Ret = 11 - $resto;
				}
			}

			return $Ret;
		}

		/*
		 *Metodo: modulo_11(valor a ser calculado, base, opção)
		 *Descrição: Responsavel por calcular do modulo 11 para geração do digito verificador
		 *Data: 10/07/2024
		 *Programador(a): Ighor Drummond
		*/
		private function modulo_11($val, $base, $opc){
			$Ret = null;
			$total = 0;
			$fator = 2;
			$nCont = 0;
			$numero = "";

			//Separa os numeros
			for($nCont = strlen($val) - 1; $nCont >= 0; $nCont--){
				$numero = (int)(substr($val, $nCont-1, $nCont));
				$total += ($numero * $fator);
				if($fator === $base){
					$fator = 1;
				}
				$fator++;
			}

			//Calcula o modulo 11 de acordo com o banco bradesco
			if($opc === 0){
				$total *= 10;
				$total = ($total % 11); //Receberá o digito pelo resto da divisão
				$total = $total === 10 ? 0 : $total;
				$Ret = $total;
			}elseif($opc === 1){
				$Ret = $total % 11;
			}

			return $Ret;
		}

		/*
		 *Metodo: modulo_11(valor a ser calculado, base, opção)
		 *Descrição: 
		 *Data: 10/07/2024
		 *Programador(a): Ighor Drummond
		*/
		private function _dateToDays($ano,$mes,$dia){

		}
	}
}
?>