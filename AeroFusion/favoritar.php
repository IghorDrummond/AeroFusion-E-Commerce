<?php
    //Inicia servidor
    session_start();
    //Bibliotecas
    require_once('lib/produtos.php');
    use Produto\Favoritos;
    //Declaração de variaveis
    //String
    $Prod = $_GET['Produto'];
    $Opc = $_GET['Opc'];
    //Numerico
    $Param = 0;
    //Objetos
    $Favoritos = null;

    //Escopo
    //Valida se usuário está logado
    if(isset($_SESSION['Login']) and $_SESSION['Login']){
        $Favoritos = new Favoritos(Email: $_SESSION['Email']);
        $Param = $Opc === '1' ? 1 : 2;//Valida se é para deletar ou adicionar
        $Favoritos->campoFavorito($Prod, $Param); 
    }else{
        echo 'LOGIN';
    }
?>