<?php
    session_start();

    if(!isset($_SESSION['Login']) or !$_SESSION['Login']){
        header('Location: login.php');
    }else{
        if(!isset($_SESSION['Produtos']) or empty($_SESSION['Produtos'])){
            header('Location: pesquisa.php');
        }
    } 
?>