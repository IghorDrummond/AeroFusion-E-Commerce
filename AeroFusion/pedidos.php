<?php
	//Inicia a sessão 
	session_start();

	//Biblioteca
	require_once ('lib/compras.php');
	require_once ('lib/conta.php');
	require_once ('lib/configuracao.php');
	use Compra\atualizaCarrinho;
	use Pedido\solicitaPedido;
	use Pedido\validaEstoque;
	use Pedido\novoPedido;
	use Pedido\validaCupom;
	use Pedido\MetodoPagamento;
	use Acesso\Endereco;
	use Pagamentos\Pagamento;

	//Declaração de variaveis
	//String
	$Opc = $_GET['Opc'];
	//Objeto
	$Pedido = null;
	$Endereco = null;
	$FormPagamento = null;

	//Finaliza operação caso usuário não estiver logado no sistema
	if (!isset($_SESSION['Login']) or !$_SESSION['Login']) {
		die();
	}

	switch ($Opc) {
		case '1':
			$_SESSION['Produtos'] = $_GET['Prod'];//Guarda os produtos selecionados pelo usuário
			break;
		case '2':
			montaTela();
			break;
		case '3':
			removeProduto($_GET['Prod']);
			break;
		case '4':
			atualizaQuantidade($_GET['IdCar'], $_GET['Quant']);
			break;
		case '5':
			novoPedido();
			break;
		case '6':
			cadastrarEnd();
			break;
		case '7':
			ativaCupom();
			break;
		case '8':
			pagamento();
			break;
		case '9':
			alterarPagamento();
			break;
	}

	function montaTela()
	{
		//Caso não houver produtos, ele retorna vazio
		if (((isset($_SESSION['Produtos']) and empty($_SESSION['Produtos'])) or !isset($_SESSION['Produtos'])) && (!isset($_SESSION['IdPed']) or empty($_SESSION['IdPed']))) {
			semProdutos();
			return null;
		} else if (!isset($_SESSION['Produtos']) and isset($_SESSION['IdPed'])) {
			pagamento();
			return null;
		}

		//Monta tela responsavel por iniciar pedido
		$Endereco = new Endereco($_SESSION['Email']);
		$FormPagamento = new Pagamento();
		$Pedido = new solicitaPedido(Email: $_SESSION['Email'], Produtos: $_SESSION['Produtos']);
		?>
		<section>
			<article class="text-warning">
				<?php
				foreach ($Pedido->imprimePedido() as $Prod) {
					$Class = $Prod['disponibilidade'] === 'FALTA ESTOQUE' ? 'bg-secondary' : 'bg-white';
					?>
					<div class="w-100 rounded area_produtos my-3 border border-secondary p-2 <?php echo ($Class); ?> ">
						<img src="img/<?php echo ($Prod['img1']); ?>" width="200" class="rounded img-fluid">
						<div>
							<h6><?php echo (mb_convert_case($Prod['nome'], MB_CASE_TITLE, 'UTF-8')); ?></h6>
							<h6>Quantidade: <?php echo ($Prod['quant']) ?></h6>
							<span>Tamanho: <?php echo ($Prod['nome_tam']); ?></span><br>
							<?php
							if ($Prod['promocao_ativo'] === 1) {
								echo "Preço Solitário: 
								De <del>R$ " . $Prod['preco'] . "</del> Por
								<mark class='bg-transparent text-warning'>R$ " . $Prod['promocao'] . "</mark>
								";
							} else {
								echo "Preço Solitário: R$ " . $Prod['preco'];
							}
							?>
							<h6>Total do Item: R$ <?php echo ($Prod['total_item']); ?></h6>
						</div>
						<!-- Campo para operação -->
						<div class="ml-auto">
							<button class="btn btn-danger text-white font-weight-bold"
								onclick="deletaItem(<?php echo ($Prod['id_car']); ?>)">
								Deletar <i class="fa-solid fa-trash-can"></i>
							</button>
							<div class="btn-group text-white font-weight-bold bg-primary" role="group">
								<button class="btn btn-primary rounded"
									onclick="atualizaQuantidade('+', <?php echo ($Prod['id_car']); ?>, this)">
									+
								</button>
								<span class="mx-2 quantidade_acao"><?php echo ($Prod['quant']); ?></span>
								<button type="button" class="btn btn-primary rounded"
									onclick="atualizaQuantidade('-', <?php echo ($Prod['id_car']); ?>, this)">
									-
								</button>
							</div>
						</div>
					</div>
					<?php
				}

				if (!isset($Prod['total_carrinho'])) {
					return null;
				}
				?>
			</article>
			<article class="p-5 bg-white d-flex flex-column align-items-center text-warning">
				<form id="compra" onsubmit="finalizarCompra()">
					<h1>Total: R$ <?php echo ($Prod['total_carrinho']); ?></h1>
					<h6>Escolha um endereço</h6>
					<div class="dropdown">
						<button class="btn btn-warning text-white font-weight-bold w-100 dropdown-toggle" type="button"
							id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							Selecione um Endereço
						</button>
						<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
							<?php
							foreach ($Endereco->getEndereco() as $i => $End) {
								?>
								<a class="dropdown-item" id="<?php echo ($End['id_end']); ?>" onclick="selecionaEndereco(this)"
									data-title="Endereço <?php echo (($i + 1)); ?>">
									<ul>
										<li><strong>Endereço <?php echo ($i + 1); ?></strong></li>
										<li>Rua: <?php echo mb_convert_case($End['rua'], MB_CASE_TITLE, 'UTF-8'); ?></li>
										<li>Bairro: <?php echo mb_convert_case($End['bairro'], MB_CASE_TITLE, 'UTF-8'); ?></li>
										<li>Número: <?php echo $End['numero']; ?></li>
										<li>CEP: <?php echo $End['cep']; ?></li>
										<li>Cidade: <?php echo mb_convert_case($End['cidade'], MB_CASE_TITLE, 'UTF-8'); ?></li>
										<li>UF: <?php echo $End['uf']; ?></li>
										<li>Complemento: <?php echo mb_convert_case($End['complemento'], MB_CASE_TITLE, 'UTF-8'); ?>
										</li>
										<li>Referência: <?php echo mb_convert_case($End['referencia'], MB_CASE_TITLE, 'UTF-8'); ?>
										</li>
									</ul>
								</a>
								<?php
							}
							?>
							<a class="dropdown-item">
								<h6 onclick="adicionarEnd()">
									➕ Adicionar Endereço
								</h6>
							</a>
						</div>
					</div>
					<h6>Forma de Pagamento:</h6>
					<select class="form-control" required>
						<option selected>Escolha uma opção</option>
						<?php
						foreach ($FormPagamento->getPagamentos() as $pag) {
							?>
							<option value="<?php echo ($pag['id_form']) ?>">
								<?php echo (mb_convert_case($pag['forma_pag'], MB_CASE_TITLE, 'UTF-8')); ?>
							</option>
							<?php
						}
						?>
					</select>
					<input type="submit" class="btn btn-warning text-center text-white font-weight-bold p-2 rounded mt-3 w-100"
						value="Comprar">
					<hr>
					<h6 class="mt-2">Tem um cupom? Insira aqui:</h6>
					<input name="cupom" class="form-control rounded" placeholder="....." onchange="validaCupom()">
				</form>
			</article>
		</section>
		<?php
	}

	function removeProduto($Prod)
	{
		$aux = null;
		$aux = explode(',', $_SESSION['Produtos']);
		unset($aux[array_search($Prod, $aux)]);
		$_SESSION['Produtos'] = implode(',', $aux);
	}

	function atualizaQuantidade($IdCar, $Quant)
	{
		$Estoque = new validaEstoque();

		//Valida se a quantidade inserida existe de acordo com o estoque
		if ($Estoque->verificaEstoque($IdCar, $Quant, $_SESSION['Email'])) {
			$Carrinho = new atualizaCarrinho(IdCar: $IdCar, Quant: $Quant);
			$Carrinho->atualizaQuantidade($_SESSION['Email']);
		} else {
			echo "ESTOQUE";
		}
	}

	function novoPedido()
	{
		//Cria objeto para inserir novo pedido
		$Pedido = new novoPedido();
		//Cadastra novo pedido
		$Ret = $Pedido->setPedido($_SESSION['Email'], $_SESSION['Produtos'], $_GET['Endereco'], $_GET['Pagamento'], $_GET['Cupom']);
		if ($Ret['Inclusão']) {
			echo 'S';
			unset($_SESSION['Produtos']);//Apaga o registro de produtos
			$_SESSION['IdPed'] = $Ret['Pedido'];
		} else {
			echo 'N';
		}
	}

	function semProdutos()
	{
		?>
		<section>
			<article
				class="bg-white border border-info d-flex flex-column align-items-center justify-content-center text-center p-2 w-100">
				<h1 class="text-warning">
					Sem Produtos para continuar...
				</h1>
				<p class="text-info">
					Lamentamos informar que seu pedido atual está sem produtos selecionados, por isso não podemos prosseguir com
					a compra. Para continuar explorando nossa variedade de produtos ou iniciar uma nova pesquisa, recomendamos
					visitar nossa página de pesquisa de produtos ou retornar à página principal.
				</p>
				<a class="btn btn-info text-white text-center font-weight-bold my-1" href="pesquisa.php">Conheçer nossos
					Produtos</a>
				<a class="btn btn-info text-white text-center font-weight-bold" href="index.php">Voltar para página Inicial</a>
			</article>
		</section>
		<?php
	}

	function cadastrarEnd()
	{
		$Endereco = new Endereco($_SESSION['Email']);

		if (isset($_GET['rua'], $_GET['complemento'], $_GET['cep'], $_GET['referencia'], $_GET['bairro'], $_GET['estado'], $_GET['numero'], $_GET['cidade'])) {
			$rua = strtoupper($_GET['rua']);
			$complemento = strtoupper($_GET['complemento']);
			$cep = str_replace('-', '', $_GET['cep']);
			$referencia = strtoupper($_GET['referencia']);
			$bairro = strtoupper($_GET['bairro']);
			$estado = strtoupper($_GET['estado']);
			$numero = strtoupper($_GET['numero']);
			$cidade = strtoupper($_GET['cidade']);

			// Certifique-se de que a função setEndereco está correta
			$Endereco->setEndereco($rua, $complemento, $cep, $referencia, $bairro, $estado, $numero, $cidade);
		} else {
			echo 'ERROR';
		}
	}

	function ativaCupom()
	{
		$Cupom = new validaCupom(Email: $_SESSION['Email'], Produtos: $_SESSION['Produtos'], Cupom: $_GET['Cupom']);
		echo $Cupom->validaCupom();
	}

	function pagamento()
	{
		$Pedido = new novoPedido();
		$Pedido = $Pedido->getPedido($_SESSION['IdPed'], $_SESSION['Email']);
		?>

		<section class="w-100 d-flex align-items-center justify-content-center">
			<div class="w-100 bg-white border px-2">
			</div>
			<article class="px-1">
				<?php
				foreach ($Pedido as $Ped) {
					?>
					<div class="w-100 rounded area_produtos my-3 border border-secondary bg-white p-2">
						<img src="img/<?php echo ($Ped['img1']) ?>" alt="Imagem do produto" width="200" class="rounded img-fluid">
						<div>
							<h6><?php echo (mb_convert_case($Ped['nome'], MB_CASE_TITLE, 'UTF-8')); ?></h6>
							<h6>Quantidade: <?php echo ($Ped['quant']) ?></h6>
							<span>Tamanho: <?php echo ($Ped['nome_tam']); ?></span><br>
							<?php
							if ($Ped['promocao_ativo'] === 1) {
								echo "Preço Solitário: 
								De <del>R$ " . $Ped['preco'] . "</del> Por
								<mark class='bg-transparent text-warning'>R$ " . $Ped['promocao'] . "</mark>
								";
							} else {
								echo "Preço Solitário: R$ " . $Ped['preco'];
							}
							?>
						</div>
					</div>


					<?php
				}
				?>
			</article>
			<article class="bg-white p-2 text-warning">
				<h5>Pedido #<?php echo ($Pedido[0]['id_ped']) ?></h5>
				<span></span>Status: <?php echo ($Pedido[0]['status_']); ?></span> -
				<span class="ml-1">
					Cupom:
					<?php
						if (is_null($Pedido[0]['nome_cupom'])) {
							echo ('Não Há!');
						} else {
							echo ($Pedido[0]['nome_cupom']);
						}
					?>
				</span>
				<br><br>
				<div class="border border-secondary rounded">
					<h6 class="mx-3">Endereço escolhido:</h6>
					<ul id="endereco_lista">
						<li>Rua: <?php echo (mb_convert_case($Ped['rua'], MB_CASE_TITLE, 'UTF-8')) ?></li>
						<li>Bairro: <?php echo (mb_convert_case($Ped['bairro'], MB_CASE_TITLE, 'UTF-8')) ?></li>
						<li>Cidade: <?php echo (mb_convert_case($Ped['cidade'], MB_CASE_TITLE, 'UTF-8')) ?></li>
						<li>Número: <?php echo (mb_convert_case($Ped['numero'], MB_CASE_TITLE, 'UTF-8')) ?></li>
						<li>Complemento: <?php echo (mb_convert_case($Ped['complemento'], MB_CASE_TITLE, 'UTF-8')) ?></li>
						<li>Referência: <?php echo (mb_convert_case($Ped['referencia'], MB_CASE_TITLE, 'UTF-8')) ?></li>
						<li>Cep: <?php echo (mb_convert_case($Ped['cep'], MB_CASE_TITLE, 'UTF-8')) ?></li>
						<li>UF: <?php echo ($Pedido[0]['uf']) ?></li>
					</ul>
					<div class="clear"></div>
				</div>
				<span class="text-primary">Mudar Endereço</span>
				<h1>Total: R$ <?php echo ($Pedido[0]['valor_total']); ?> </h1>
				<!-- Forma de Pagamento -->
				<form class="w-100 form-group mt-2" method="post" onsubmit="Pedido()">
					<?php
					if ($Pedido[0]['forma_pag'] === 'PIX') {
						?>
						<fieldset class="form-group">
							<legend>Pagamento: Pix</legend>
							<select name="metodo" class="form-control">
								<option selected value="0">Mudar Pagamento</option>
								<option value="2">Cartão</option>
								<option value="3">Boleto</option>
							</select>
							<br><br>
							<img src="img/qrcode_pix.svg" class="img-fluid rounded" alt="QR CODE PARA PAGAMENTO PIX">
						</fieldset>
						<?php
					} else if ($Pedido[0]['forma_pag'] === 'CARTÃO') {
						?>
							<fieldset>
								<legend>Pagamento: Cartão</legend>
								<select name="metodo" class="form-control">
									<option selected value="0">Mudar Pagamento</option>
									<option value="1">Pix</option>
									<option value="3">Boleto</option>
								</select>
								<br><br>
								<div id="cartao" class="rounded m-auto text-white">
									<!-- Cartão frontal -->
									<div class="p-5 cartaoLado d-flex flex-column w-100">
										<img class="img-fluid ml-auto" src="https://cdn-icons-png.flaticon.com/512/2695/2695969.png" name="operadora" alt="Operadora Cartão" width="50">
										<label>Nome impresso</label>
										<input id="numero_cartao" name="nome_cartao" type="text" placeholder="FULANO DE TAL" required value="<?php print !empty($Pedido[0]['nome_cartao']) ? $Pedido[0]['nome_cartao'] : ''   ?>">
										<label>Número do cartão</label>
										<input id="numero_cartao" name="numero_cartao" type="text" placeholder="0000 0000 0000 0000"
											maxlength="19" required value="<?php print !empty($Pedido[0]['numero_cartao']) ? $Pedido[0]['numero_cartao'] : ''   ?>" onchange="bandeira(this)" onkeyup=" maskNumero(this)">
										<button type="button" title="virar cartão" class="ml-auto" onclick="virarCartao(1)">
											Virar <i class="fa-solid fa-hand-point-right fa-xl mt-3"></i>
										</button>
									</div>
									<!-- Cartão traseiro -->
									<div class="cartaoLado transforma d-none flex-column">
										<div class="bg-dark p-4 w-100 mt-3"></div>
										<div class="d-flex p-5 w-100">
											<div>
												<label>Data Expiração</label><br>
												<input name="vencimento" type="text" placeholder="00/0000" maxlength="7" onkeyup="maskValidade(this)" required value="<?php print !empty($Pedido[0]['validade']) ? $Pedido[0]['validade'] : ''   ?>">
											</div>
											<div>
												<label>CVC</label><br>
												<input name="cvc" type="text" placeholder="000" maxlength="3" required value="<?php print !empty($Pedido[0]['cvv']) ? $Pedido[0]['cvv'] : ''   ?>">
											</div>
										</div>
										<button type="button" class="ml-auto" title="virar cartão" onclick="virarCartao(0)">
											Virar <i class="fa-solid fa-hand-point-right fa-xl mt-3"></i>
										</button>
										<br>
									</div>
								</div>
								<br>
								<labe>Parcelas:</labe>
								<select class="form-control">
									<option value="1" selected>1x de R$ <?php echo ($Pedido[0]['valor_total']); ?></option>
									<?php
									for ($nCont = 2; $nCont <= 12; $nCont++) {
										?>
										<option value="<?php echo ($nCont) ?>">
										<?php print ($nCont . 'x de R$ ' . str_replace('.', ',', number_format((float) $Pedido[0]['valor_total'] / $nCont, 2))); ?>
										</option>
									<?php
									}
									?>
								</select>
							</fieldset>
						<?php
					} else if ($Pedido[0]['forma_pag'] === 'BOLETO') {
						?>
							<fieldset class="form-group">
								<legend>Pagamento: Boleto</legend>
								<select name="metodo" class="form-control">
									<option selected value="0">Mudar Pagamento</option>
									<option value="1">Pix</option>
									<option value="2">Cartão</option>
								</select>
								<br><br>
								<img src="img/qrcode_pix.svg" class="img-fluid rounded" alt="QR CODE PARA PAGAMENTO PIX">
							</fieldset>
						<?php
					}
					?>
					<blockquote class="text-center text-warning">
						Antenção!<br>
						Este pedido tem válidade em status pedente até 7 dias, caso o pedido não sofrer alguma alteração, será
						cancelado e encerrado.
					</blockquote>
					<button type="submit" class="btn btn-primary btn-lg btn-block align-self-end">
						Finalizar Pedido <i class="fa-solid fa-check fa-xl"></i>
					</button>
					<!-- Apagar Pedido -->
					<button type="button" class="btn btn-danger btn-lg btn-block align-self-end">
						Deletar Pedido <i class="fa-solid fa-trash-can"></i>
					</button>
				</form>
			</article>
		</section>
<?php
	}
	
	function alterarPagamento(){
		if((isset($_GET['Metodo']) and !empty($_GET['Metodo'])) and (isset($_SESSION['IdPed']) and !empty($_SESSION['IdPed'])) ){
			$Pagamento = new MetodoPagamento(IdPed: $_SESSION['IdPed'], Email: $_SESSION['Email'], Metodo: $_GET['Metodo']);
			$Pagamento->setPagamento();
		}
	}