<?php
	session_start();

	if(isset($_SESSION['Login']) and $_SESSION['Login'] === false){
		if(isset($_SESSION['TrocaSenha'])){
			$Dados = explode(';', $_SESSION['TrocaSenha']);
	
			if(!validaData($Dados[0])){
				header('Location: index.php');
			}
		}else{
			header('Location: index.php');
		}
	}else{
		if(isset($_SESSION['TrocaSenha'])){
			unset($_SESSION['TrocaSenha']);
		}
		header('Location: index.php');
	}

	//Funções
	function validaData($Data){
		//Declaração de Variaveis
		//Numericos
		$dataInicial = null;
		$dataFinal = null;
		$diferenca = null;
		//Define o horario 
		date_default_timezone_set('America/Sao_Paulo');
		//Pega a Data Inicial
		$dataInicial = new DateTime($Data);
		//Pega a Data final
		$dataFinal = new DateTime(Date('Y-m-d H:i:s'));
		//Faz a Diferença
		$diferenca = $dataInicial->diff($dataFinal);
		//Valida se a data inicial e final é maior ou igual a 18 anos	
		return $diferenca->d < 1 ? true : false;
	}
?>