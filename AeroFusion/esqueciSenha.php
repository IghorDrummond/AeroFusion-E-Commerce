<?php
	require_once('lib/conta.php');
	use Cadastro\CodigoEmail;
	//Declaração de Variaveis
	//String
	$Email = $_GET['Email'];
	//Objeto
	$Envio = null;
	//Valida email
	$Envio = new CodigoEmail(Email: $Email);
	if($Envio->enviaEmail()){
		echo 'OK';
	}else{
		echo "EMAIL";
	}
?>