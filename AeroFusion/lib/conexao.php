<?php
/*
*Classe: IniciaServer
*Descrição: Responsavel por fazer conexão com o banco de dados
*Data: 17/05/2024
*Programador(a): Ighor Drummond
*/
	class IniciaServer{
		//--Atributos
		//Private 
		//String
		private $dsn = 'mysql: host=localhost; dbname=db_aerofusion';
		private $usuario = 'root';
		private $senha = '';
		//Costantes
		public $conexao = null;

		public function __construct(){
			try{
				//Inicia Conexão com o banco de dados
				$this->conexao = new PDO($this->dsn, $this->usuario, $this->senha);	
				$this->conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			}catch(PDOException $e){	
				//print_r($e);
				echo 'Erro: ' . $e->getCode() . ' Mensagem: ' . $e->getMessage();
			}
		}
		
		public function conexao(){
			return $this->conexao;
		}
	}
?>