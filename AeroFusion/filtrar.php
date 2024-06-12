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
			foreach ($Produtos as $nCont2 => $Prod) {
?>
				<div class="produto" 
					onclick="maisDetalhes(<?php echo($Prod['IdProd']) ?>)"
					onmouseover="passaImagens(this)" 
					onmouseleave="paraImagens(this)"
				>
					<img src="img/<?php echo($Prod['img1']); ?>" class="img-fluid">
					<!-- Inicio do carousel -->
					<div class="d-none carousel slide" id="img-prod" data-ride="carousel" data-interval="1500">
						<div class="carousel-inner">
							<!-- Imagens do carousel -->
							<?php 
								for($i = 1; $i <= 5; $i++){
									if($Prod['img' . strval($i)] === ''){
										continue;
									}
							?>	
							<div class="carousel-item <?php print($i === 2 ? "active" : "") ?>">
								<img src="img/<?php echo($Prod['img' . strval($i)]); ?>" class="d-block w-100 img-fluid">
							</div>
							<?php
								}
							?>
						</div>
					</div>
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