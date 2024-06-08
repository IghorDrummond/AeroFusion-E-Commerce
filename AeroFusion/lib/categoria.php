<?php
	//Importar Biblioteca
	namespace Categoria{
		require_once('conexao.php');		

		/*
		*Classe: TelaCategoria
		*Descrição: Retorna todas as Categorias
		*Data: 17/05/2024
		*Programador(a): Ighor Drummond
		*/
		class TelaCategoria{
			//Atributo
			private $query = '';
			private $conexao = '';
			private $stmt = [];

			//Construtor
			public function __construct(){	
				$aux = new \IniciaServer();
				$this->conexao = $aux->conexao();
				$this->montaQuery();
				$this->buscarValor();
			}
			//Destruidor
			public function __destruct(){
				return 'Destruido com sucesso!';
			}

			//Métodos
			public function retornarValores(){
				return $this->stmt->fetchAll();
			}

			private function buscarValor(){
				try{
					$this->stmt = $this->conexao->query($this->query);					
				}catch(\PDOException $e){
					echo 'Houve um erro ao tentar conectar';
				}

			}

			private function montaQuery(){
				$this->query = '
					SELECT 
						*
					FROM
						categoria
				';
			}
		}
	}
?>