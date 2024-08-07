<?php
    // Biblioteca
    require_once('C:/Users/Drummond/Documents/FullStack/Gits/AeroFusion-E-Commerce/AeroFusion/lib/configuracao.php');
    use Jobs\AtualizaProduto;

    // Declaração de variáveis
    $Produto = null;
    $LogMsg = null;

    $Produto = new AtualizaProduto();
    $LogMsg = $Produto->arrumaEstoque();

	// Imprimir log no console
	echo $LogMsg;

    // Função para guardar Log
	/*
	guardaLog($LogMsg);
    function guardaLog($msg) {
        // Obter a data atual
        $DataHoje = date('d_m_Y');
        
        // Nome do arquivo de log
        $LogArq = "log_$DataHoje.txt";
        
        // Verificar se o arquivo já existe
        if (!file_exists($LogArq)) {
            // Criar o arquivo se não existir
            $handle = fopen($LogArq, 'w') or die("Não foi possível criar o arquivo de log.");
        } else {
            // Abrir o arquivo para adicionar conteúdo se ele já existir
            $handle = fopen($LogArq, 'a') or die("Não foi possível abrir o arquivo de log.");
        }
        
        // Escrever a mensagem no arquivo de log
        fwrite($handle, "$msg");
        
        // Fechar o arquivo de log
        fclose($handle);
    }*/
?>
