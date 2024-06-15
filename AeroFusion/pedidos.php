<?php
	//Inicia a sessão 
	session_start();
	//Biblioteca
	require_once('lib/compras.php');
	use Pedido\solicitaPedido;
	//use Pedido\
	//Declaração de variaveis
	//String
	$Opc = $_GET['Opc'];
	//Objeto
	$Pedido = null;
	$Endereco = null;
	$FormPagamento = null;

	switch ($Opc) {
		case '1':
			$_SESSION['Produtos'] = $_GET['Prod'];//Guarda os produtos selecionados pelo usuário
			break;
		case '2':
			$Pedido = new solicitaPedido(Email: $_SESSION['Email'], Produtos: $_SESSION['Produtos']);
			montaTela($Pedido->imprimePedido());
			break;
	}

	function montaTela($Dados){
		//Monta tela responsavel por iniciar pedido
		/*
		$Endereco = new Endereco($_SESSION['Email']);	
		$FormPagamento = new FormaPagamento();		*/
		$Pedido = new solicitaPedido(Email: $_SESSION['Email'], Produtos: $_SESSION['Produtos']);

?>
	<section>
		<article class="text-warning">
			<?php
				foreach ($Dados as $Prod) {
					$Class = $Prod['disponibilidade'] === 'FALTA ESTOQUE' ? 'bg-secondary' : 'bg-white';
			?>
			<div class="w-100 rounded area_produtos my-3 <?php echo($Class); ?> ">
				<img src="img/<?php echo($Prod['img1']); ?>" width="200" class="rounded img-fluid">
				<div>
					<h6><?php echo($Prod['nome']); ?></h6>
					<h6>Quantidade: <?php echo($Prod['quant']) ?></h6>
					<?php
						if($Prod['promocao_ativo'] === 1){
							echo "
							De <del>R$ ". $Prod['preco'] . "</del> Para
							<mark class='bg-transparent text-warning'>R$ ". $Prod['promocao'] ."</mark>
							";
						}
					?>
					<h6>Total do Item: R$ <?php echo($Prod['total_item']); ?></h6>
				</div>
			</div>
			<?php
				}
			?>
		</article>
		<article class="p-5 bg-white d-flex flex-column align-items-center text-warning">
			<form>
				<h1>R$ <?php echo($Prod['total_carrinho']); ?></h1>
				<h6>Escolha um endereço</h6>
				<select class="form-control">
					<option selected>Escolha um Endereço</option>
					<option>Endereço 1</option>
					<option>Endereço 2</option>
				</select>
				<h6>Forma de Pagamento:</h6>
				<select class="form-control" required>
					<option selected>Escolha uma opção</option>
					<option value="1">Pix</option>
					<option value="2">Cartão</option>
					<option value="3">Boleto</option>
					<option value="4">Parcelamento</option>
				</select>
				<input type="submit" class="btn btn-warning text-center text-white font-weight-bold p-2 rounded mt-3 w-100" value="Comprar">
				<hr>
				<h6 class="mt-2">Tem um cupom? Insira aqui:</h6>
				<input  name="cupom" class="form-control rounded" placeholder=".....">				
			</form>
		</article>
	</section>
<?php
	}
?>