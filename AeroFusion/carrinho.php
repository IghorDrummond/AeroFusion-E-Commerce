<?php
	//Iniciando a sessão
	session_start();
	//Biblioteca
	require_once('lib/compras.php');
	use Compra\AdicionarCarrinho;
	use Compra\VerCarrinho;
	use Compra\QuantCarrinho;
	use Compra\ApagarItem;

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
			$Carrinho = new QuantCarrinho(Email: $_SESSION['Email']);
			echo ($Carrinho->retornarValores()[0]['quant_item']);
		}elseif($Opc === "4"){
			$Carrinho = new ApagarItem(IdCar: $Prod, Cliente: $_SESSION['Email']);
			echo $Carrinho->apagaDados();
		}else{
			$Carrinho = new VerCarrinho(Cliente: $_SESSION['Email']);
			retornaProd($Carrinho->retornaValores());
		}
	}else{
		if($Opc != "1"){
			echo 'LOGIN';
		}elseif($Opc === "3"){
			echo('0');
		}else{
		echo '
			<i onclick="fecharCarrinho()" class="fa-solid fa-x w-100 border border-warning rounded text-center mb-auto"></i>
			<h6 class="text-center text-warning">Faça um login para adicionar produtos ao carrinho</h6>
			<a href="login.php" class="btn btn-md btn-primary text-white p-1 rounded">Fazer Login</a>
			';
		}
	}

	function retornaProd($Produtos){

		if(!empty($Produtos)){
			echo '
			<i onclick="fecharCarrinho()" class="fa-solid fa-x w-100 border border-warning rounded text-center mb-auto"></i>
			<div id="carrinho_produtos" class="mt-1" onscroll="guardaScroll()">';
			foreach ($Produtos as $Prod) {
				//Valida se o produto que está no carrinho está fora de estoque
				$Class = $Prod['estoque'] < $Prod['quant'] ? 'bg-secondary' : 'bg-white';
?>
	<div class="d-flex border border-warning p-1 flex-column align-items-center text-center p-2 <?php echo($Class) ?>">
		<?php
			if($Prod['estoque'] >= $Prod['quant']){
		?>		
		Selecionar para comprar
		<input type="checkbox" name="CarProd" value="<?php echo($Prod['id_car']) ?>" onchange="selecionarProduto()">
		<?php
			}else{
		?>
		<h6>Estoque insuficiente. <br> Por favor, ajuste a quantidade clicando em 'Modificar itens do carrinho' ou remova o produto do seu carrinho.</h6>
		<?php
			}
		?>
		<div onclick="maisDetalhes(<?php echo($Prod['id_prod']) ?>)" onscroll="guardaScroll()" style="cursor: pointer;" title="Ver detalhes">
			<img src="img/<?php echo($Prod['img']) ?>" width="120" height="120" class="img-fluid rounded">
			<h6><?php echo( mb_convert_case($Prod['produto'], MB_CASE_TITLE, 'UTF-8') ) ?></h6>
			<?php
				//Valida se tem promoção ativa para este item
				if($Prod['promocao_ativo'] === 1){
					echo('<span class="bandeira_promocao_carrinho badge badge-pill badge-dark">Promoção</span><br>');
					echo('<del>R$ '. $Prod['preco'] .'</del><br>');
				}
			?>
			Preço do item:R$ <?php echo($Prod['total_item']) ?>
			<br>
			Tamanho: <?php echo($Prod['tamanho']) ?>
			<br>
			Quant: <?php echo($Prod['quant']) ?>
			<br>
			<button class="text-warning">Mais Detalhes</button>
		</div>
		<i onclick="deletaItem(<?php echo($Prod['id_car']) ?>)" class="fa-regular fa-trash-can" style="color: red; cursor: pointer;" title="Deletar Item"></i>
	</div>
<?php
			}
			echo '</div>
				<h6>Total: R$ '. $Produtos[0]['total_carrinho'] .'</h6>
				<button class="bg-warning text-white p-1 rounded" onclick="adicionarPedido()">Comprar</button>
				ou
				<a class="bg-warning text-white p-1 rounded" href="home.php#carrinho">Modificar items do carrinho</a>
			';
		}else{
			echo '
			<i onclick="fecharCarrinho()" class="fa-solid fa-x w-100 border border-warning rounded text-center mb-auto"></i>
			<h1>Não há Produtos</h1>';
		}
	}
?>