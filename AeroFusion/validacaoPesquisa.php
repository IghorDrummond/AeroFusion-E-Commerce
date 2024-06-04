<?php
    require_once('script/lib/produtos.php');
    use Produto\Filtrar;

    $Pesquisa = isset($_POST['Pesquisa']) ? $_POST['Pesquisa'] : '';

    if(!empty($Pesquisa)){
        $Filtrar = new Filtrar('', '', '', '', $_POST['Pesquisa']);
    }
?>