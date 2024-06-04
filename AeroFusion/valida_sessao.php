<?php
	//Inicia a Sessão
	session_start();

	if(isset($_SESSION['Login'])){
		if($_SESSION['Login']){
			header('location: index.php');
		}
	}else{
		$_SESSION['Login'] = false;
	}
?>