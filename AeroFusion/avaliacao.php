<?php
	session_start();
	require_once('script/lib/compras.php');

	if(
		isset($_POST['titulo']) and !empty($_POST['titulo']) 
		and isset($_POST['descricao']) and !empty($_POST['descricao']) 
		and isset($_POST['quantidadeEstrelas']) and !empty($_POST['quantidadeEstrelas']) 
	){

	}else{
		echo "Preencha todos os campos";
	}
?>