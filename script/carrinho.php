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
		}elseif($Opc === "3"){
			//Contar quantos items tem dentro do carrinho
		}else{
			$Carrinho = new VerCarrinho(Cliente: $_SESSION['Email']);
			retornaProd($Carrinho->retornaValores());
		}
	}else{
		if($Opc != "1"){
			echo 'LOGIN';
		}else{
		echo '
			<i onclick="fecharCarrinho()" class="fa-solid fa-x w-100 border border-warning rounded text-center"></i>
			<h6 class="text-center text-warning">Faça um login para adicionar produtos ao carrinho</h6>
			<a href="login.php" class="btn btn-md btn-primary text-white p-1 rounded">Fazer Login</a>
			';
		}
	}

	function retornaProd($Produtos){

		if(!empty($Produtos)){
			echo '
			<i onclick="fecharCarrinho()" class="fa-solid fa-x w-100 border border-warning rounded text-center"></i>
			<div id="carrinho_produtos" class="mt-1">';
			foreach ($Produtos as $Prod) {
?>
	<div class="d-flex border border-warning p-1 flex-column carrino_prod align-items-center text-center" onclick="maisDetalhes(<?php echo($Prod['id_prod']) ?>)">
		<div>
			<img src="img/<?php echo($Prod['img']) ?>" width="50" height="50" class="img-fluid rounded">
			<h6><?php echo(ucfirst(strtolower($Prod['produto']))) ?></h6>
			Preço do item:R$ <?php echo($Prod['total_item']) ?>
			<br>
			Tamanho: <?php echo($Prod['tamanho']) ?>
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
				<button class="bg-warning text-white p-1 rounded" onclick="modificarCarrinho()">Modificar items do carrinho</button>
			';
		}else{
			echo "<h1>Não há Produtos</h1>";
		}
	}
?>