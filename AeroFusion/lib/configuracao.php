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

		/*
		*Classes: AtualizaProduto
		*Descrição: Atualiza Produto sem estoque
		*Data: 20/07/2024
		*Programador(a): Ighor Drummond
		*/
		class AtualizaProduto(){
			//Declaração
			private $Query = null;
			private $stmt = null;
			private $con = null;
			private $IdProd = null;
			private $Estoque = null;

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
				//Valida se existe produtos sem estoque
				if(isset($this->stmt[0]['id_prod'])){	
					for ($nCont=0; $nCont <= count($this->stmt) -1 ; $nCont++) { 
						//Atualiza estoque dos produtos 
						$this->Estoque = mt_rand(15, 70);
						$this->IdProd = $this->stmt[$nCont]['id_prod'];
						$this->montaQuery(2);
						$this->pushDados();
					}
				}
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
					$this->stmt = $this->con->query($this->query);
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
	}
?>