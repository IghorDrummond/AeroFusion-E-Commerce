<?php
	//Iniciando a sessão
	session_start();
	//Biblioteca
	require_once('lib/compras.php');
	use Compra\AdicionarCarrinho;
	use Compra\VerCarrinho;

	//Declaração de variaveis
	//String
	$Opc = $_GET['Opc'];
	$Prod = isset($_GET['Prod']) ? $_GET['Prod'] : '';
	$Quant = isset($_GET['Quant']) ? $_GET['Quant'] : '';
	$Tam = isset($_GET['Tam']) ? $_GET['Tam'] : '';
	//Objeto
	$Carrinho = null;

	//-----------------Escopo
	if(isset($_SESSION['Email']) and !empty($_SESSION['Email'])){
		if($Opc === "2"){
			$Carrinho = new AdicionarCarrinho(Produto: $Prod, Quantidade: $Quant, Tam: $Tam, Cliente: $_SESSION['Email']);
			echo $Carrinho->guardaProduto();
		}else{
			$Carrinho = new VerCarrinho(Cliente: $_SESSION['Email']);
			retornaProd($Carrinho->retornaValores());
		}
	}else{
		echo 'LOGIN';
	}

	function retornaProd($Produtos){

		if(!empty($Produtos)){
			echo '
			<i onclick="fecharCarrinho()" class="fa-solid fa-x w-100 border border-warning rounded text-center"></i>
			<div id="carrinho_produtos">';
			foreach ($Produtos as $Prod) {
?>
	<div class="d-flex border border-warning p-1 flex-column carrino_prod align-items-center text-center" onclick="maisDetalhes(<?php echo($Prod['id_prod']) ?>)">
		<div>
			<img src="img/<?php echo($Prod['img']) ?>" width="50" height="50" class="img-fluid rounded">
			<h6><?php echo(ucfirst(strtolower($Prod['produto']))) ?></h6>
			Preço do item:R$ <?php echo($Prod['total_item']) ?>
			<br>
			Quant: <?php echo($Prod['quant']) ?>
			<br>
			<button class="text-warning">Mais Detalhes</button>
		</div>
	</div>
<?php
			}
			echo '</div>
				<h6>Total: R$ '. $Produtos[0]['total_carrinho'] .'</h6>
			';
		}else{
			echo "<h1>Não há Produtos</h1>";
		}
	}
?>