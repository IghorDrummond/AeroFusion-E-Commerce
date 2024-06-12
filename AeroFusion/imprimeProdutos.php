<?php
	require_once('script/lib/promocao.php');
	use Promocao\ProdutoTela;
	//Declaração de variavel
	//Numerico
	$nCont = 0;
	$nCont2 = 0;
	$posicAnt = 0;
	//Array
	$Produtos = [];
	//Objeto
	$ProdutoOjb = null;
	//Instancia Produto
	$ProdutoObj = new ProdutoTela();
	//Retorna Produtos
	$Produtos = $ProdutoObj->retornaProdutos();

	if(count($Produtos) === 0){
		echo('Houve um erro com o banco de dados! Retornaremos o mais breve possivel!');
		die();
	}

	for($nCont = 0; $nCont <= 2; $nCont++){
?>
	<!-- Carregamento Back End dos Produtos -->
	<div class="row justify-content-center">
<?php
		for($nCont2 = $posicAnt; $nCont2 <= ($posicAnt + 3); $nCont2++){
?>
		<div class="produto" 
			onclick="maisDetalhes(<?php echo(strval($Produtos[$nCont2]['IdProd'])) ?>)"
			onmouseover="passaImagens(this)" 
			onmouseleave="paraImagens(this)"
		>
			<img src="img/<?php echo($Produtos[$nCont2]['img1']); ?>" class="img-fluid">

			<!-- Inicio do carousel -->
			<div class="d-none carousel slide p-2 bg-dark" id="img-prod" data-ride="carousel">
				<div class="carousel-inner">
					<!-- Imagens do carousel -->
					<?php 
						for($i = 1; $i <= 5; $i++){
							if($Produtos[$nCont2]['img' . strval($i)] === ''){
								continue;
							}
					?>	
					<div class="carousel-item">
						<img src="img/<?php echo($Produtos[$nCont2]['img' . strval($i)]); ?>" class="d-block w-100 img-fluid">
					</div>
					<?php
						}
					?>
				</div>
			</div>
			<h6 class="font-weight-bold"><?php print(ucfirst(strtolower($Produtos[$nCont2]['Produto']))) ?></h6>
			<span class="d-inline-block text-info"><?php print(ucfirst($Produtos[$nCont2]['Categoria'])) ?></span>
			<span class="d-inline-block w-50 text-right" onclick="favoritar('<?php echo($Produtos[$nCont2]['Produto']); ?>')">
				<i class="fa-solid fa-star"></i>
			</span>
			<span class="d-block mt-1">R$ <?php echo(strval($Produtos[$nCont2]['Preco'])) ?></span>
		</div>
<?php
		}
?>
	</div>
<?php
		$posicAnt = $nCont2;
	}
?>