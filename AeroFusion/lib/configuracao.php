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
?>