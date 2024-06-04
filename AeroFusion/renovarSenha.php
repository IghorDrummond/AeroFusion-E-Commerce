<?php
	session_start();
	require_once('lib/conta.php');
	use Cadastro\RenovarSenha;
	//Declaração de variavel
	//String
	$Email = '';
	$Senha = $_GET['Senha'];
	$ConfirmeSenha = $_GET['ConfirmeSenha'];
	//Objeto
	$Renova = null;

	$Email = explode(';', $_SESSION['TrocaSenha'])[1];

	$Renova = new RenovarSenha(Email: $Email, Senha: $Senha);
	if($Senha === $ConfirmeSenha){
		if($Renova->atualizaSenha()){
			unset($_SESSION['TrocaSenha']);//Remove o acesso unico
			echo 'OK';
		}else{
			echo 'ERROR';
		}
	}else{
		echo 'SENHAS';
	}
?>