<?php
session_start();
require_once('lib/produtos.php');
use Produto\AvalicaoProduto;
//Declaração de variaveis
//String
$titulo = '';
$descricao = '';
$uploadDir = '';
$produto = '';
//Numerico
$quantidadeEstrelas = 0;
//Array
$json = [];
//Objeto
$avalicao = null;
//constantes
define('TAM_LIMITE', 500 * 1024);


/*
	Data - 17/08/2024
	Eu (Ighor Drummond) implementei segurança de dados contra ataques XSS, SQL INJECTION e arquivos maliciosos. 
	Para mais detalhes desses usos, consulte a documentação do PHP.
*/

// Verificação de dados recebidos - Evita SQL INJECTION
if (
    isset($_POST['titulo']) && !empty($_POST['titulo']) &&
    isset($_POST['descricao']) && !empty($_POST['descricao']) &&
    isset($_POST['quantidadeEstrelas']) && !empty($_POST['quantidadeEstrelas']) &&
    isset($_POST['produto']) && !empty($_POST['produto'])

) {
    // Sanitização dos dados - Evita ataque XSS
    $titulo = santizacaoDados($_POST['titulo']);
    $descricao = santizacaoDados($_POST['descricao']);
    $quantidadeEstrelas = santizacaoDados($_POST['quantidadeEstrelas']);
    $produto = santizacaoDados($_POST['produto']);

    // Validação dos dados - Evita SQL Injection 
    if (empty($titulo) || empty($descricao) || !validateInteger($quantidadeEstrelas, 1, 5) || !validateInteger($produto, $_POST['produto'] , $_POST['produto'])) {
    	die('Tentativa de SQL Injection detectada!');
    }

    //Prepara a classe que irá inserir no banco de dados
    $avaliacao = new AvalicaoProduto('');

    if($avaliacao->existe($produto, $_SESSION['Email'], $_SESSION['pedido'])){
    	die('Tentativa de SQL Injection detectada!');
    }

    //após validar se o pedido se trata do cliente logado, mata a posição para prosseguir com a demanda
    unset($_SESSION['pedido']);

    // Processamento de arquivos
    $uploadDir = '../img/avaliacao/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    //Movimenta a imagem para a pasta destino
    for ($nCont = 1; $nCont <= count($_FILES); $nCont++) {
        if (isset($_FILES["imagem$nCont"]) && $_FILES["imagem$nCont"]['error'] == UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES["imagem$nCont"]['tmp_name'];
            $fileName = $_FILES["imagem$nCont"]['name'];
            $fileSize = $_FILES["imagem$nCont"]['size'];
            $fileType = $_FILES["imagem$nCont"]['type'];

            // Verificar o tamanho do arquivo (máximo 500KB)
            if ($fileSize > TAM_LIMITE) {
            	$Ret[$nCont]['error'] = true;
                $Ret[$nCont]['mensagem'] =  'A imagem não pode ser maior que 500KB.';
                $Ret[$nCont]['imagem'] = $_FILES["imagem$nCont"]['name'];
              	continue;
            }

            // Verificar o tipo de arquivo (somente imagens)
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($fileType, $allowedTypes)) {
            	$Ret[$nCont]['error'] = true;
               	$Ret[$nCont]['mensagem'] = 'Formato de imagem inválido. Apenas JPG, PNG e GIF são permitidos.';
               	$Ret[$nCont]['imagem'] = $_FILES["imagem$nCont"]['name'];
                continue;
            }

            // Gera um nome único para o arquivo
            $filePath =  $uploadDir . uniqid() . '-' . basename($fileName);

            // Move o arquivo para o diretório de upload
            if (move_uploaded_file($fileTmpPath, $filePath)) {
            	$avaliacao->setImagem($filePath);
            	$Ret[$nCont]['error'] = false;
                $Ret[$nCont]['mensagem'] = 'Imagem enviada com sucesso.';
                $Ret[$nCont]['imagem'] = $_FILES["imagem$nCont"]['name'];
            } else {
            	$Ret[$nCont]['error'] = true;
                $Ret[$nCont]['mensagem'] = 'Erro ao enviar a imagem.';
                $Ret[$nCont]['imagem'] = $_FILES["imagem$nCont"]['name'];
                continue;
            }
        }else{
        	$Ret[$nCont]['mensagem'] = 'imagem não foi transitada corretamente por rede: ' . $_FILES["imagem$nCont"]['error'];
        }
    }
    //Ejeta a imagens ao qual deu certo
   // $avaliacao->setAvaliaProd($_POST['produto']);
} else {
    echo "Preencha todos os campos";
}

// Função para sanitizar entradas
function santizacaoDados($dado) {
    return htmlspecialchars(strip_tags(trim($dado)));
}

// Função para validar se um valor é um número inteiro
function validateInteger($value, $min, $max) {
    return filter_var($value, FILTER_VALIDATE_INT, [
        'options' => [
            'min_range' => $min,
            'max_range' => $max
        ]
    ]) !== false;
}
?>
