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
    //Objetos
    $Favoritos = null;

    //Escopo
    //Valida se usuário está logado
    if(isset($_SESSION['Login']) and $_SESSION['Login']){
        $Favoritos = new Favoritos(Email: $_SESSION['Email']);
        switch($Opc){
            case '1': 
                //Adiciona ao favoritos
                $Favoritos->campoFavorito($Prod, 1);
                break;
            case '0':   
                $Favoritos->campoFavorito($Prod, 2);
                //Retira favorito
                break;
        }
    }
?>