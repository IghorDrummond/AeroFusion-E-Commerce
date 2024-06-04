<?php
    //Iniciando Sessão
    session_start();
    //Bibliotecas
    require_once 'lib/conta.php';
    use Acesso\Login;

    //Declaração de Variaveis
    //String
    $Email = $_GET['Email'];
    $Senha = $_GET['Senha'];
    //Objeto
    $Login  = null;

    $Login = new Login($Email, $Senha);//Recebe Classe para buscar Valor
    $ValidaDado = $Login->retornaValores();
    
    if($ValidaDado  === 'Logado'){
        $_SESSION['Login'] = true;
        $_SESSION['Email'] = $Email;
        $_SESSION['Nome'] = ucfirst(strtolower($Login->getNome()));
        $_SESSION['Foto'] = $Login->getFoto();
        imprime(1);
    }else if($ValidaDado  === 'Senha'){
        imprime(2);
    }else{
        imprime(3);
    }

    //--------Funções
    function imprime($existe){
        switch($existe){
            case 1:
                echo 'Logado';
                break;
            case 2:
                echo 'Senha';
                break;
            default:
                echo 'NaoExiste';
                break;
        }
    }
?>