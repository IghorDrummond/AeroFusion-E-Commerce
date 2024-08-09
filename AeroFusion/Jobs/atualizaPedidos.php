<?php
    // Biblioteca
    require_once('C:/Users/Drummond/Documents/FullStack/Gits/AeroFusion-E-Commerce/AeroFusion/lib/configuracao.php');
    use Jobs\AtualizaPedidos;

    // Declaração de variáveis
    $Pedidos = null;
    $LogMsg = null;

    $Pedidos = new AtualizaPedidos();
    $LogMsg = $Pedidos->attPeds();

	// Imprimir log no console
	echo $LogMsg;
?>
