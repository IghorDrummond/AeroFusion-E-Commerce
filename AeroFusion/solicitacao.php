<?php
	//Iniciar Sessão
	session_start();
	//declaração da variavel

	switch ($_GET['opc']) {
		case '0':
			
			break;
		case '1':
			break;
		case '2':
			break;
		case '3':
			break;
		case '4':
			session_destroy();//Destrói sessão
			break;
	}

?>