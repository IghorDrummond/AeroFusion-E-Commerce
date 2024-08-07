<?php
	//Bibliotecas
	require_once('script/lib/promocao.php');
	require_once('script/lib/produtos.php');
	//Classes para ser utilizadas
	use Promocao\ProdutoTela;
	use Produto\Favoritos;
	//Declaração de variavel
	//Numerico
	$nCont = 0;
	$nCont2 = 0;
	$posicAnt = 0;
	//Array
	$Produtos = [];
	//Objeto
	$ProdutoOjb = null;
	$Favoritos = null;
	//Instancia Produto
	$ProdutoObj = new ProdutoTela();
	//Retorna Produtos
	$Produtos = $ProdutoObj->retornaProdutos();

	if(count($Produtos) === 0){
		echo('Houve um erro com o banco de dados! Retornaremos o mais breve possivel!');
		die();
	}
	//Valida se está favorito os produtos
	if(isset($_SESSION['Login']) and $_SESSION['Login']){
		$Favoritos = new Favoritos(Email: $_SESSION['Email']);
	}

	for($nCont = 0; $nCont <= 2; $nCont++){
?>
	<!-- Carregamento Back End dos Produtos -->
	<div class="row justify-content-center">
<?php
		for($nCont2 = $posicAnt; $nCont2 <= ($posicAnt + 3); $nCont2++){
?>	
	<div 
		class="produto" 
		onmouseover="passaImagens(this)" 
		onmouseleave="paraImagens(this)"
		style="cursor: pointer;" 
	>
		<div
			onclick="maisDetalhes(<?php echo(strval($Produtos[$nCont2]['IdProd'])) ?>)"
		>
			<img src="img/<?php echo($Produtos[$nCont2]['img1']); ?>" class="img-fluid">

			<!-- Inicio do carousel -->
			<div class="d-none carousel slide" id="img-prod" data-ride="carousel" data-interval="1000" data-pause="false">
				<div class="carousel-inner">
					<!-- Imagens do carousel -->
					<?php 
						for($i = 1; $i <= 5; $i++){
							if($Produtos[$nCont2]['img' . strval($i)] === ''){
								continue;
							}
					?>	
					<div class="carousel-item <?php print($i === 2 ? "active" : "") ?>">
						<img src="img/<?php echo($Produtos[$nCont2]['img' . strval($i)]); ?>" class="d-block w-100 img-fluid">
					</div>
					<?php
						}
					?>
				</div>
			</div>
			<h6 class="font-weight-bold"><?php print mb_convert_case($Produtos[$nCont2]['Produto'], MB_CASE_TITLE, 'UTF-8') ?></h6>
		</div>
		<span class="d-inline-block text-info"><?php print(ucfirst($Produtos[$nCont2]['Categoria'])) ?></span>
		<span class="d-inline-block w-50 text-right" onclick="favorito(this, <?php echo(strval($Produtos[$nCont2]['IdProd'])) ?> )">
				<!-- Valida se usuário está logado -->
				<?php
					$Ret = false;
					if(!is_null($Favoritos)){
						$Ret = $Favoritos->retornaValores($Produtos[$nCont2]['IdProd']);
					}
					//Valida se usuário tem o produto favoritado
					$Class = $Ret ? 'fa-solid fa-star' : 'fa-regular fa-star';
				?>
				<i class="<?php echo($Class); ?>"></i>
		</span>
		<?php

			if($Produtos[$nCont2]['promocao_ativo'] === 1){
		?>
			<br>
			<i><del>De R$ <?php echo($Produtos[$nCont2]['Preco']) ?></del></i>
			<span class="d-inline mt-1 text-success">Por R$ <?php echo(strval($Produtos[$nCont2]['promocao'])) ?></span>
		<?php
			}else{ 
		?>
			<br>
			<span class="d-inline mt-1"><?php echo('R$' . strval($Produtos[$nCont2]['Preco'])) ?></span>
		<?php
			}

			if($Produtos[$nCont2]['vizu_3d'] === 1){
				echo "<span class='bg-secondary px-1 rounded text-white font-weight-bold float-right'> 3D <i class='fa-solid fa-cubes'></i></span>";
			}
		?>
	</div>
<?php
		}
?>
	</div>
<?php
		$posicAnt = $nCont2;
	}
?>