<?php
	require_once('lib/produtos.php');
	use Produto\Filtrar;
	//Declaração de variaveis
	//Array
	$Produto = [];
	//Objeto
	$Filtrar = null;

	$Filtrar = new Filtrar('', '', '', '', strtoupper($_GET['Produto']));
	$Produto = $Filtrar->retornaValores();

	if(count($Produto) === 0){
		echo('<div class="p-5">Nada Encontrado...</div>');
		die();
	}

	echo ('<div id="rolagem_caixa">');
	foreach ($Produto as $i => $prod) {
?>	
	<div style="cursor: pointer;" id="item_caixa_pesq" class="border border-warning rounded d-flex flex-column text-center text-warning" onclick="maisDetalhes(<?php echo($prod['IdProd']) ?>)">
		<img src="img/<?php echo($prod['img1']); ?>" class="img-fluid d-block m-auto" width="100">
		<p>
			<?php echo($prod['Produto']); ?>
		</p>
		<h6 class="text-primary"><?php echo($prod['Preco']); ?></h6>
		<button class="text-warning" onclick="maisDetalhes(<?php echo($prod['IdProd']) ?>)"> Mais Detalhes</button>
	</div>
<?php
		if($i === 3){
			break;
		}
	}
	echo ('</div>');
?>