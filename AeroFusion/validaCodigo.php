<?php
    //Inicia a Sessão
    session_start();
    //Importa Bibliotecas
    require_once('lib/conta.php');
    use Cadastro\validaCodigo;
    //Declarar variaveis
    //String 
    $Codigo = $_GET['Codigo'];
    $Email = $_GET['Email'];
    //Objetos
    $Valida  = null;

    $Valida = new ValidaCodigo(Codigo: $Codigo, Email: $Email);
    //Define o horario 
    date_default_timezone_set('America/Sao_Paulo');
    switch($Valida->validaCodigo()){
        case 'OK':
            $_SESSION['TrocaSenha'] = date('Y-m-d H:i:s') . ';' . $Email;
            echo 'OK';
            break;
        case 'DATA':
            echo 'DATA';
            break;
        case 'CODIGO':
            echo 'CODIGO';
            break;
        case 'EMAIL':
            echo 'EMAIL';
            break;
    }
?>
