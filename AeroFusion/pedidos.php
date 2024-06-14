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
			$_SESSION['Produtos'] = $_POST['Prod'];
			break;
		case '2':
			montaTela();
			break;
	}

	function montaTela(){
		//Monta tela responsavel por iniciar pedido
		/*
		$Endereco = new Endereco($_SESSION['Email']);	
		$FormPagamento = new FormaPagamento();
		$Pedido = new solicitaPedido(Email: $_SESSION['Email'], Produtos: $_SESSION['Produtos']);
		*/
?>
	<section class="d-flex justify-content-center align-items-center p-1">
		<article class="p-5 bg-warning border">
			<?php

			?>
		</article>
		<article class="p-5 bg-white d-flex flex-column align-items-center text-warning">
			<form class="w-100">
				<h1>R$ 0000,00</h1>
				<h6>Escolha um endereço</h6>
				<select class="form-control w-50">
					<option selected>Escolha um Endereço</option>
					<option>Endereço 1</option>
					<option>Endereço 2</option>
				</select>
				<h6>Forma de Pagamento:</h6>
				<select class="form-control w-50" required>
					<option selected>Escolha uma opção</option>
					<option value="1">Pix</option>
					<option value="2">Cartão</option>
					<option value="3">Boleto</option>
					<option value="4">Parcelamento</option>
				</select>
				<input type="submit" class="btn btn-warning text-center font-weight-bold p-2 rounded w-50 mt-3" value="Comprar">
				<hr class="w-50">
				<h6 class="mt-2">Tem um cupom? Insira aqui:</h6>
				<input  name="cupom" class="form-control w-50 rounded" placeholder=".....">				
			</form>
		</article>
	</section>
<?php
	}
?>