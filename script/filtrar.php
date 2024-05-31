<?php
	//Bibliotecas
	require_once('lib/produtos.php');
	use Produto\Filtrar;
	//Declaração de variaveis
	//String
	$Produto = isset($_GET['Pesq']) ? $_GET['Pesq'] : '';
	$Categoria = isset($_GET['categoria']) ? $_GET['categoria'] : '';
	$Preco = isset($_GET['preco']) ? $_GET['preco'] : '';
	$Data = isset($_GET['data']) ? $_GET['data'] : '';
	$Tamanho = isset($_GET['tamanho']) ? $_GET['tamanho'] : '';
	//Array
	$Produtos = [];
    //Ojeto
    $Filtros = null;

    //--------Escopo
    $Filtro = new Filtrar($Preco, $Data, $Categoria, $Tamanho, $Produto);
    $Produtos = $Filtro->retornaValores();
    catalogaProdutos($Produtos);
	//----------Funções
	function catalogaProdutos($Produtos){
		if(isset($Produtos[0]['Produto'])){
			echo "<h6 class='text-center w-100 bg-success text-white p-1 sticky-top'>Encontrado ". strval(count($Produtos)) ." Produtos</h6>";
			foreach ($Produtos as $Indice => $Prod) {
?>
				<div class="produto" onclick="maisDetalhes(<?php echo($Prod['IdProd']) ?>)">
					<img src="img/<?php echo($Prod['img1']); ?>" class="img-fluid">
					<h6 class="font-weight-bold"><?php print(ucfirst(strtolower($Prod['Produto']))) ?></h6>
					<span class="d-inline-block text-info"><?php print(ucfirst($Prod['Categoria'])) ?></span>
					<span class="d-inline-block w-50 text-right" onclick="favoritar('<?php echo($Prod['Produto']); ?>')">
						<i class="fa-solid fa-star"></i>
					</span>
					<span class="d-block mt-1">R$ <?php echo(strval($Prod['Preco'])) ?></span>
				</div>
<?php
			}
		}
		else{
			echo('<h1 class="text-center">Não foi encontrado!</h1>');
		}
	}
?>