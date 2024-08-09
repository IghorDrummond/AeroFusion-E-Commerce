<?php
	namespace Pagamentos{
		require_once('conexao.php');

		/*
		*Classes: Pagamento
		*Descrição: Classe responsavel por recuperar formas de pagamentos
		*Data: 15/06/2024
		*Programador(a): Ighor Drummond
		*/
		class Pagamento{ 
			//Atributos
			private $con = null;
			private $query = null;
			private $stmt = null;

			//Construtor
			public function __construct()
			{
				try{
					$this->con = new \IniciaServer();
					$this->con = $this->con->conexao();
				}catch(\PDOException $e){
					echo $e->getMessage();
				}
			}

			//Métodos
			public function getPagamentos(){
				$this->montaQuery();
				try{
					$this->stmt = $this->con->query($this->query);
					$this->stmt = $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
				}catch(\PDOException $e){
					echo $e->getMessage();
				}finally{
					return $this->stmt;
				}
			}
			private function montaQuery(){
				$this->query = "
					SELECT
						*
					FROM
						forma_pagamento
				";
			}
		}

	}

	namespace Jobs{
		require_once('conexao.php');
		require_once('compras.php');
		/*
		*Classes: AtualizaProduto
		*Descrição: Atualiza Produto sem estoque
		*Data: 20/07/2024
		*Programador(a): Ighor Drummond
		*/
		class AtualizaProduto{
			//Declaração
			private $Query = null;
			private $stmt = null;
			private $con = null;
			private $IdProd = null;
			private $Estoque = null;
			private $Log = null;

			//Construtor
			public function __construct(){
				$this->con = new \IniciaServer();
				$this->con = $this->con->conexao();
			}

			//Métodos
			/*
			 *Metodo: arrumaEstoque()
			 *Descrição: Responsavel por atualizar estoque dos produtos zerados
			 *Data: 20/07/2024
			 *Programador(a): Ighor Drummond
			 */
			public function arrumaEstoque(){
				$this->montaQuery(1);
				$this->getDados();
				date_default_timezone_set('America/Sao_Paulo');
				//Valida se existe produtos sem estoque
				if(isset($this->stmt[0]['id_prod'])){	
					for ($nCont=0; $nCont <= count($this->stmt) -1 ; $nCont++) { 
						//Atualiza estoque dos produtos 
						$this->Estoque = mt_rand(15, 70);
						$this->IdProd = $this->stmt[$nCont]['id_prod'];
						$this->montaQuery(2);
						$this->pushDados();
						$this->Log .= PHP_EOL . date('d/m/Y H:i:s') . " - Produto atualizado: $this->IdProd - Estoque adicionado $this->Estoque";
					}
				}else{
					$this->Log .= PHP_EOL . date('d/m/Y H:i:s') . " Não existem produtos sem estoque para atualizar.";
				}
				//Retorna o Log da execução realizada
				return $this->Log;
			}

			/*
			 *Metodo: getDados()
			 *Descrição: Responsavel por receber dados da consulta
			 *Data: 20/07/2024
			 *Programador(a): Ighor Drummond
			 */
			private function getDados()
			{
				try {
					$this->stmt = $this->con->query($this->Query);
					$this->stmt = $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
				} catch (\PDOException $e) {
					echo $e->getMessage();
				}
			}

			/*
			 *Metodo: pushDados()
			 *Descrição: Responsavel por cadastrar dados no banco
			 *Data: 20/07/2024
			 *Programador(a): Ighor Drummond
			 */
			private function pushDados()
			{
				try {
					$this->con->beginTransaction();
					if ($this->con->exec($this->Query) > 0) {
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
			 *Data: 20/07/2024
			 *Programador(a): Ighor Drummond
			 */
			private function montaQuery($Opc){
				if($Opc === 1){
					$this->Query = "
						SELECT
							*
						FROM 
							produtos  as prod
						WHERE 
							prod.estoque <= 0
					";
				}else if($Opc === 2){
					$this->Query = "
						UPDATE 
							produtos
						SET
							estoque = $this->Estoque
						WHERE
							id_prod = $this->IdProd
					";
				}
			}
		}
		/*
		*Classes: AtualizaPedidos
		*Descrição: Atualiza todos os pedidos e seus rastreios
		*Data: 04/08/2024
		*Programador(a): Ighor Drummond
		*/
		class AtualizaPedidos{
			//Atributos
			private $con = null;
			private $Query = null;
			private $IdPed = null;
			private $IdCli = null;
			private $stmt = null;
			private $status = null;
			private $status_ras = null;
			private $Data_Ras = null;

			
			//Construtor
			public function __construct(){
				$this->con = new \IniciaServer();
				$this->con = $this->con->conexao();
			}

			//Métodos
			public function attPeds(){
				$log = '';

				//Pesquisa Pedidos que estão com status ainda não finalizados
				$this->montaQuery(0);
				$this->getDados();

				if(isset($this->stmt[0]['id_ped'])){
					date_default_timezone_set('America/Sao_Paulo'); // Configura data e hora do servidor
					foreach ($this->stmt as $Ped){
						switch($Ped['status']){
							case 1:
							/*
								//Calcula a diferença da data e verifica se já passou do prazo de 7 dias para processar - Pendente
								$Diferenca = $this->calculaData($Ped['data_ped']);
								if($Diferenca->days >= 7){
									//Implementar herança aqui nesse código para deletar o pedido

									//Atualiza rastreio para devolvido a AeroFusion - Cancelado
									$this->AtualizaStatusRastreio($Ped['id_ped'], 7);
									//Atualiza status do pedido para 6 de cancelado
									$log .= PHP_EOL . date('d/m/Y H:i:s') . " - Pedido {$Ped['id_ped']} foi cancelado por passar do 7 dias para pagar - Data do Pedido Inicial: {$Ped['data_ped']}";
								}*/
								break;
							case 2:
								$Diferenca = $this->calculaData($Ped['data_rastreio']);//Calcula a diferença da data do rastreio
								//Valida pedido que está sendo preparado para envio - Aguardando Envio
								if($Ped['status_ras'] === 1 and $Diferenca->i > 5){
									//Atualiza status do rastreio para 2 de saiu do armazem
									$this->AtualizaStatusRastreio($Ped['id_ped'],2);
									$log .= PHP_EOL . date('d/m/Y H:i:s') . " - Rastreio do Pedido {$Ped['id_ped']} atualizado - Saiu do Armazém";
								}else if($Ped['status_ras'] === 2 and $Diferenca->i > 5){
									//Atualiza status do rastreio para 3 de recebido pela transportadora
									$this->AtualizaStatusRastreio($Ped['id_ped'], 3);
									$log .= PHP_EOL . date('d/m/Y H:i:s') . " - Rastreio do Pedido {$Ped['id_ped']} atualizado - Recebido pela Transportadora";
								}else if($Ped['status_ras'] === 3 and $Diferenca->i > 5){
									//Atualiza status do rastreio para 4 de deslocando para sua cidade e o pedido atualizado para Transportando
									$this->AtualizaStatusRastreio($Ped['id_ped'], 4);
									$this->status = 3;
									$this->IdPed = $Ped['id_ped'];
									$this->montaQuery(2);
									$this->setDados();
									$log .= PHP_EOL . date('d/m/Y H:i:s') . " - Rastreio do Pedido {$Ped['id_ped']} atualizado - Recebido pela Transportadora";
									$log .= PHP_EOL . date('d/m/Y H:i:s') . " - Pedido {$Ped['id_ped']} está sendo transportado para a cidade do destinatário";
								}
								break;
							case 3:
								$Diferenca = $this->calculaData($Ped['data_rastreio']);//Calcula a diferença da data do rastreio
								//Valida pedido que está saiu para entrega - Saiu para entrega
								if($Diferenca->i > 5 and $Ped['status_ras'] === 4){
									$this->AtualizaStatusRastreio($Ped['id_ped'], 5);
									$this->status = 4;
									$this->IdPed = $Ped['id_ped'];
									$this->montaQuery(2);
									$this->setDados();	
									$log .= PHP_EOL . date('d/m/Y H:i:s') . " - Rastreio do Pedido {$Ped['id_ped']} atualizado - Saiu para Entrega";
									$log .= PHP_EOL . date('d/m/Y H:i:s') . " - Pedido {$Ped['id_ped']} está se deslocando para a residência do destinatário";								
								}
								break;
							case 4:
								$Diferenca = $this->calculaData($Ped['data_rastreio']);//Calcula a diferença da data do rastreio
								//Valida pedido que foi entregue ao destinatario - Entregue
								if($Diferenca->i > 5 and $Ped['status_ras'] === 5){
									$this->AtualizaStatusRastreio($Ped['id_ped'], 6);
									$this->status = 5;
									$this->IdPed = $Ped['id_ped'];
									$this->montaQuery(2);
									$this->setDados();
									$log .= PHP_EOL . date('d/m/Y H:i:s') . " - Rastreio do Pedido {$Ped['id_ped']} atualizado - Entregue";
									$log .= PHP_EOL . date('d/m/Y H:i:s') . " - Pedido {$Ped['id_ped']} Foi entregue para o destinatário";				
								}
								break;
						}
					}
				}else{
					$log .= date('d/m/Y H:i:s') . " - Nenhum pedido encontrado para atualização.";
				}	

				return $log;
			}
			/*
			 *Metodo: montaQuery(Opção)
			 *Descrição: Responsavel por montar as querys
			 *Data: 04/08/2024
			 *Programador(a): Ighor Drummond
			 */
			private function montaQuery($Opc){
				if($Opc === 1){
					$this->Query = "
						SELECT
							*
						FROM
							pedidos ped
						INNER JOIN	
							rastreio as ras ON ras.id_ped = ped.id_ped
						WHERE
							status BETWEEN 1 AND 4
					";
				}else if($Opc === 2){
					$this->Query = "
						UPDATE
							pedidos
						SET
							status = $this->status
						WHERE
							id_ped = $this->IdPed
					";						
				}else if($Opc === 3){
					$this->Query = "
						INSERT INTO rastreio(id_ped, data_rastreio, status_ras)
						VALUES($this->IdPed, '$this->Data_Ras', $this->status_ras);
					";
				}
			}
			/*
			 *Metodo: getDados()
			 *Descrição: Responsavel por retornar dados
			 *Data: 04/08/2024
			 *Programador(a): Ighor Drummond
			 */
			private function getDados(){
				try{
					$this->stmt = $this->con->query($this->Query);
					$this->stmt = $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
				}catch(\PDOException $e){
					echo $e->getMessage();
				}
			}
			/*
			 *Metodo: setDados()
			 *Descrição: Responsavel por guardar dados
			 *Data: 04/08/2024
			 *Programador(a): Ighor Drummond
			 */
			private function setDados(){
				try{
					$this->con->exec($this->Query);
				}catch(\PDOException $e){
					echo $e->getMessage();
				}
			}
			/*
			 *Metodo: AtualizaStatusRastreio(id do pedido, id do status do rastreio)
			 *Descrição: Atualiza rastreio do pedido para um novo status definido 
			 *Data: 04/08/2024
			 *Programador(a): Ighor Drummond
			 */
			private function AtualizaStatusRastreio($IdPed, $Opc){
				$this->IdPed = $IdPed;
				$this->status_ras = $Opc;
				$this->Data_Ras = date('Y-m-d H:i:s');
				$this->montaQuery(3);
				$this->setDados();
			}
			/*
			 *Metodo: calculaData(Data anterior)
			 *Descrição: Calcula a diferença da data anterior para a atual e retorna 
			 *Data: 04/08/2024
			 *Programador(a): Ighor Drummond
			 */
			private function calculaData($Data_Ant){
				$Data_Ant  = new \DateTime($Data_Ant);
				$Data_Atual = new \DateTime(date('Y-m-d'));	
				return $Data_Atual->diff($Data_Ant);	
			}
		}
	}

	namespace Configuracao{
		require_once('conexao.php');
		/**
		 * 
		 */
		class Configuracao
		{	
			//Atributos
			private $con = null;
			private $stmt = null;
			private $query = null;
			private $IdCli = null;
			private $Src = null;

			//Construtor
			function __construct(
				public string $Email = ""
			)
			{
				//Conexão com o banco de dadoss
				$this->con = new \IniciaServer();
				$this->con = $this->con->conexao();	
				//Retornar o Id do Cliente
				$this->montaQuery(1);
				$this->getDados();
				$this->IdCli = $this->stmt[0]['id_cliente'];
			}

			//Métodos
			/*
			 *Metodo: setImagem(Diretorio Imagem)
			 *Descrição: Responsavel por guardar o diretorio da imagem no cliente responsavel
			 *Data: 21/07/2024
			 *Programador(a): Ighor Drummond
			 */
			public function setImagem($Imagem){
				$this->Src = $Imagem;
				$this->montaQuery(2);
				$this->setDados();
			}
			/*
			 *Metodo: montaQuery(Opção)
			 *Descrição: Responsavel por montar as querys
			 *Data: 21/07/2024
			 *Programador(a): Ighor Drummond
			 */
			private function montaQuery($Opc){
				if($Opc === 1){
					$this->query = "
						SELECT
							id as id_cliente
						FROM
							cliente
						WHERE
							email = '$this->Email'
					";
				}else if($Opc === 2){
					$this->query = "
						UPDATE
							cliente
						SET
							foto = '$this->Src'
						WHERE
							id = $this->IdCli
					";						
				}
			}
			/*
			 *Metodo: montaQuery(Opção)
			 *Descrição: Responsavel por retornar dados
			 *Data: 20/07/2024
			 *Programador(a): Ighor Drummond
			 */
			private function getDados(){
				try{
					$this->stmt = $this->con->query($this->query);
					$this->stmt = $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
				}catch(\PDOException $e){
					echo $e->getMessage();
				}
			}
			/*
			 *Metodo: setDados()
			 *Descrição: Responsavel por guardar dados
			 *Data: 21/07/2024
			 *Programador(a): Ighor Drummond
			 */
			private function setDados(){
				try{
					$this->con->exec($this->query);
				}catch(\PDOException $e){
					echo $e->getMessage();
				}
			}
		}

		/*
		*Classes: AtualizaUsuario
		*Descrição: Atualiza Dados do usuário Logado
		*Data: 31/07/2024
		*Programador(a): Ighor Drummond
		*/
		class AtualizaUsuario
		{
			//Atributos
			private $con = null;
			private $stmt = null;
			private $query = null;
			private $IdCli = null;
			private $Nome = null;

			//Construtor
			function __construct(
				public $Email = ''
			)
			{
				//Conexão com o banco de dadoss
				$this->con = new \IniciaServer();
				$this->con = $this->con->conexao();	
				//Retornar o Id do Cliente
				$this->montaQuery(1);
				$this->getDados();
				$this->IdCli = $this->stmt[0]['id_cliente'];				
			}

			//Metodos
			/*
			 *Metodo: setNome(Novo Nome do usuário)
			 *Descrição: Responsavel por alterar nome do usuário
			 *Data: 31/07/2024
			 *Programador(a): Ighor Drummond
			*/
			public function setNome($Nome){
				$this->Nome = $Nome;
				$this->montaQuery(2);
				$this->setDados();
			}
			/*
			 *Metodo: montaQuery(Opção)
			 *Descrição: Responsavel por montar as querys
			 *Data: 21/07/2024
			 *Programador(a): Ighor Drummond
			*/
			private function montaQuery($Opc){
				if($Opc === 1){
					$this->query = "
						SELECT
							id as id_cliente
						FROM
							cliente
						WHERE
							email = '$this->Email'
					";
				}else if($Opc === 2){
					$this->query = "
						UPDATE
							cliente
						SET
							nome = '$this->Nome'
						WHERE
							id = $this->IdCli
					";						
				}
			}
			/*
			 *Metodo: getDados
			 *Descrição: Responsavel por recuperar dados de uma pesquisa
			 *Data: 31/07/2024
			 *Programador(a): Ighor Drummond
			*/
			private function getDados(){
				try{
					$this->stmt = $this->con->query($this->query);
					$this->stmt = $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
				}catch(\PDOException $e){
					echo $e->getMessage();
				}
			}
			/*
			 *Metodo: setDados()
			 *Descrição: Responsavel por guardar dados
			 *Data: 31/07/2024
			 *Programador(a): Ighor Drummond
			*/
			private function setDados(){
				try{
					$this->con->exec($this->query);
				}catch(\PDOException $e){
					echo $e->getMessage();
				}
			}						
		}

		class ConfigEndereco {
			//Atributos
			private $con = null;
			private $stmt = null;
			private $query = null;
			private $cep = null;
			private $rua = null;
			private $bairro = null;
			private $referencia = null;
			private $complemento = null;
			private $cidade = null;
			private $estado = null;
			private $numero = null;
			private $IdCli = null;
			private $IdEnd = null;

			//Construtor
			public function __construct(public $Email = null){
				//Conexão com o banco de dadoss
				$this->con = new \IniciaServer();
				$this->con = $this->con->conexao();	
				//Retornar o Id do Cliente
				$this->montaQuery(1);
				$this->getDados();
				$this->IdCli = $this->stmt['id_cliente']	;						
			}

			//Métodos
			/*
			 *Metodo: delEnd(dados do endereço para apagar)
			 *Descrição: Responsavel por deletar o endereço desejado do usuário
			 *Data: 04/07/2024
			 *Programador(a): Ighor Drummond
			*/
			public function delEnd($cep, $rua, $numero, $bairro, $cidade, $estado, $complemento, $referencia){
				$this->cep = $cep;
				$this->rua = $rua;
				$this->bairro = $bairro;
				$this->referencia = $referencia;
				$this->complemento = $complemento;
				$this->cidade = $cidade;
				$this->estado = $estado;
				$this->numero = $numero;				

				try{
					$this->montaQuery(2);
					if($this->con->exec($this->query) > 0){
						return true;
					}else{
						return false;
					}
				}catch(\PDOException $e){
					echo $e->getMessage();
					return false;
				}
			}
			/*
			 *Metodo: getDados
			 *Descrição: Responsavel por recuperar dados de uma pesquisa
			 *Data: 04/07/2024
			 *Programador(a): Ighor Drummond
			*/
			private function getDados(){
				try{
					$this->stmt = $this->con->query($this->query);
					$this->stmt = $this->stmt->fetch(\PDO::FETCH_ASSOC);
				}catch(\PDOException $e){
					echo $e->getMessage();
				}
			}
			/*
			 *Metodo: montaQuery(Opção)
			 *Descrição: Responsavel por montar as querys
			 *Data: 04/07/2024
			 *Programador(a): Ighor Drummond
			*/
			private function montaQuery($Opc){
				if($Opc === 1){
					$this->query = "
						SELECT
							id as id_cliente
						FROM
							cliente
						WHERE
							email = '$this->Email'
					";
				}else if($Opc === 2){
					$this->query = "
						UPDATE 
							endereco
						SET	
							end_ativo = false
						WHERE
							cep = '$this->cep'
							AND rua = '$this->rua'
							AND bairro = '$this->bairro'
							AND cidade = '$this->cidade'
							AND uf = '$this->estado'
							AND numero = $this->numero
							AND id_cliente = $this->IdCli
					";
					//Dados extras mas não obrigatórios
					if(!empty($this->referencia)){
						$this->query .= "AND referencia = '$this->referencia'" . PHP_EOL;
					}
					if(!empty($this->complemento)){
						$this->query .= "AND complemento = '$this->complemento'" . PHP_EOL;
					}						
				}
			}
		}
	}
	
?>