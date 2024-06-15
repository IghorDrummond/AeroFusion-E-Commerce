<?php
	//Inicia a sessão 
	session_start();

	//Biblioteca
	require_once('lib/compras.php');
	require_once('lib/conta.php');
	require_once('lib/configuracao.php');
	use Pedido\solicitaPedido;
	use Acesso\Endereco;
	use Pagamentos\Pagamento;
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
		$Endereco = new Endereco($_SESSION['Email']);	
		$FormPagamento = new Pagamento();		
		$Pedido = new solicitaPedido(Email: $_SESSION['Email'], Produtos: $_SESSION['Produtos']);
?>
	<section>
		<article class="text-warning">
			<?php
				foreach ($Dados as $Prod) {
					$Class = $Prod['disponibilidade'] === 'FALTA ESTOQUE' ? 'bg-secondary' : 'bg-white';
			?>
			<div class="w-100 rounded area_produtos my-3 border border-secondary <?php echo($Class); ?> ">
				<img src="img/<?php echo($Prod['img1']); ?>" width="200" class="rounded img-fluid">
				<div>
					<h6><?php echo( mb_convert_case($Prod['nome'], MB_CASE_TITLE, 'UTF-8') ); ?></h6>
					<h6>Quantidade: <?php echo($Prod['quant']) ?></h6>
					<?php
						if($Prod['promocao_ativo'] === 1){
							echo "Preço: 
							<del>R$ ". $Prod['preco'] . "</del> Para
							<mark class='bg-transparent text-warning'>R$ ". $Prod['promocao'] ."</mark>
							";
						}else{
							echo "Preço: R$ ". $Prod['preco'];							
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
				<h1>Total: R$ <?php echo($Prod['total_carrinho']); ?></h1>
				<h6>Escolha um endereço</h6>
				<div class="dropdown">
					<button class="btn btn-warning text-white font-weight-bold w-100 dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						Selecione um Endereço
					</button>
					<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
					<?php
						foreach ($Endereco->getEndereco() as $i => $End) {
					?>	
						<a class="dropdown-item" id="<?php echo($End['id_end']); ?>" data-title="Endereço <?php echo(($i+1)); ?>">
							<ul>
								<li><strong>Endereço <?php echo ($i + 1); ?></strong></li>
		                        <li>Rua: <?php echo mb_convert_case($End['rua'], MB_CASE_TITLE, 'UTF-8'); ?></li>
		                        <li>Bairro: <?php echo mb_convert_case($End['bairro'], MB_CASE_TITLE, 'UTF-8'); ?></li>
		                        <li>Número: <?php echo $End['numero']; ?></li>
		                        <li>CEP: <?php echo $End['cep']; ?></li>
		                        <li>Cidade: <?php echo mb_convert_case($End['cidade'], MB_CASE_TITLE, 'UTF-8'); ?></li>
		                        <li>UF: <?php echo $End['uf']; ?></li>
		                        <li>Complemento: <?php echo mb_convert_case($End['complemento'], MB_CASE_TITLE, 'UTF-8'); ?></li>
		                        <li>Referência: <?php echo mb_convert_case($End['referencia'], MB_CASE_TITLE, 'UTF-8'); ?></li>
							</ul>							
						</a>
					<?php
						}
					?>
					<a class="dropdown-item" >
						<h6>
							➕ Adicionar Endereço
						</h6>
					</a>
					</div>
				</div>
				<h6>Forma de Pagamento:</h6>
				<select class="form-control" required>
					<option selected>Escolha uma opção</option>
					<?php
						foreach($FormPagamento->getPagamentos() as $pag) {
					?>
					<option value="<?php echo($pag['id_form']) ?>">
						<?php echo( mb_convert_case($pag['forma_pag'], MB_CASE_TITLE, 'UTF-8')  ); ?>
					</option>
					<?php
						}
					?>
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