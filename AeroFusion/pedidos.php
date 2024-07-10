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
	use Cadastro\Cartao;

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
		case '10':
			finalizarPedido();
			break;
		case '11':
			retornaCartao();
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
				<h1>Total: R$ <?php echo ($Pedido[0]['valor_total']); ?> </h1>
				<!-- Forma de Pagamento -->
				<form class="w-100 form-group mt-2" method="post" onsubmit="Pedido()">
					<?php
					if ($Pedido[0]['forma_pag'] === 'PIX') {
						?>
						<fieldset class="form-group">
							<legend>Pagamento: Pix</legend>
							<div class="input-group mb-3">
									<select class="custom-select" id="Metodo">
										<option selected value="0">Mudar Pagamento</option>
										<option value="2">Cartão</option>
										<option value="3">Boleto</option>
										</option>
									</select>
									<div class="input-group-prepend">
										<label class="input-group-text bg-primary text-white" style="cursor: pointer;" for="Metodo" onclick="mudarPagamento()">Selecionar</label>
									</div>
								</div>
								<br><br>
							<br><br>
							<img src="img/qrcode_pix.svg" class="img-fluid rounded" alt="QR CODE PARA PAGAMENTO PIX">
						</fieldset>
				<?php
					} else if ($Pedido[0]['forma_pag'] === 'CARTÃO') {
						//Recupera cartões cadastrados deste usuário
						$Cartao = new Cartao(Email: $_SESSION['Email']);
						$Cartao = $Cartao->getCartao('');
				?>
							<fieldset>
								<legend>Pagamento: Cartão</legend>
								<div class="input-group mb-3">
									<select class="custom-select" id="Metodo">
										<option selected value="0">Mudar Pagamento</option>
										<option value="1">Pix</option>
										<option value="3">Boleto</option>
										</option>
									</select>
									<div class="input-group-prepend">
										<label class="input-group-text bg-primary text-white" style="cursor: pointer;" for="Metodo" onclick="mudarPagamento()">Selecionar</label>
									</div>
								</div>
								<br><br>

								<div class="caixa_cartao">
									<div id="cartao" class="<?php print retornaClasseCartao($Cartao[0]['nome_ban']); ?>">

										<!-- Parte frontal do Cartão -->
										<div class="cartaoLado d-flex flex-column">
											<img class="img-fluid ml-auto" src="<?php echo(is_null($Cartao[0]['nome_cartao']) || empty($Cartao[0]['nome_cartao']) ? 'https://cdn-icons-png.flaticon.com/512/2695/2695969.png' : $Cartao[0]['img_ban']);   ?>" name="operadora" alt="Operadora Cartão" width="50">
											<label for="">Nome impresso</label>
											<input type="text" id="nome_cartao" placeholder="FULANO DE TAL" name="nome_cartao" value="<?php print !empty($Cartao[0]['nome_cartao']) ? $Cartao[0]['nome_cartao'] : ''?>" required>
											<label>Número do cartão</label>
											<input id="numero_cartao" name="numero_cartao" type="text" placeholder="0000 0000 0000 0000" maxlength="19" maxlength="19" required value="<?php print !empty($Cartao[0]['numero_cartao']) ? $Cartao[0]['numero_cartao'] : ''?>" onchange="bandeira(this)" onkeyup=" maskNumero(this)">
											<button type="button" title="Virar cartão" class="ml-auto" onclick="virarCartao(1)">
												Virar<i class="fa-solid fa-hand-point-right fa-xl m-3"></i>
											</button>
										</div>

										<!-- Parte traseira do Cartão -->
										<div class="cartaoLado transforma d-none flex-column">
											<div class="bg-dark p-4 w-100 mt-3"></div>
											<div class="d-flex flex-wrap data-info">
												<div>
													<label>Data Expiração</label><br>
													<input id="vencimento" name="vencimento" type="text" placeholder="00/0000" maxlength="7" onkeyup="maskValidade(this)" required value="<?php print !empty($Cartao[0]['validade_formatada']) ? $Cartao[0]['validade_formatada'] : ''?>">
												</div>
												<div>
													<label>CVV</label><br>
													<input id="cvv" name="cvv" type="text" placeholder="000" maxlength="3" required value="<?php print !empty($Cartao[0]['cvv']) ? $Cartao[0]['cvv'] : ''?>">
												</div>
											</div>
											<button type="button" title="Virar cartão" class="ml-auto" onclick="virarCartao(0)">
												Virar<i class="fa-solid fa-hand-point-right fa-xl m-3"></i>
											</button>
											<br>
										</div>

									</div>
								</div>
								<label class="mt-4">Escolha um outro cartão:</label>
								<div class="dropdown">
									<button class="btn btn-secondary dropdown-toggle btn-sm btn-block" data-toggle="dropdown" arial-expanded="false">Cartões</button>
									<div class="dropdown-menu">
								<?php
									if(isset($Cartao[0]['numero_cartao'])){
										foreach($Cartao as $nCont => $Cartoes) {
								?>
										<div class="dropdown-tem p-2" id="#X<?php print($Cartoes['id_card']) ?>">
											<h3>Cartão: <?php echo (substr($Cartoes['numero_cartao'], 0, 4)) . ' **** **** **' . substr($Cartoes['numero_cartao'], 17, 19)  ?> </h3>
											<small>Nome: <?php echo strtoupper($Cartoes['nome_cartao']); ?></small><br>
											<small>Vencimento: <time> <?php echo $Cartoes['validade_formatada']  ?></time></small><br>
											<small>Bandeira: <img src="<?php echo($Cartoes['img_ban']) ?>" class="img-fluid" width="50" height="50" alt="<?php print retornaClasseCartao($Cartoes['nome_ban']); ?>"></small>
											<button type="button" class="btn btn-primary btn-sm btn-block mt-2" onclick="cartaoSelecionado(this)">Selecionar</button>
										</div>
										<hr>
								<?php
										}
									}
								?>
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
								<div class="input-group mb-3">
									<select class="custom-select" id="Metodo">
										<option selected value="0">Mudar Pagamento</option>
										<option value="1">Pix</option>
										<option value="2">Cartão</option>
										</option>
									</select>
									<div class="input-group-prepend">
										<label class="input-group-text bg-primary text-white" style="cursor: pointer;" for="Metodo" onclick="mudarPagamento()">Selecionar</label>
									</div>
								</div>
								<br><br>
								<!-- Implementa Boleto -->
								 <h2>Boleto Gerado:</h2>
								<ul class="list-group text-dark">
									<li class="list-group-item">
										<b>Número do Cliente -</b> #<?php echo($Pedido[0]['id_cliente']) ?>
									</li>
									<li class="list-group-item">
										<b>Vencimento - </b><time></time>
									</li>
									<li class="list-group-item">
										<b>Items:</b><br> 
										<?php 
											foreach ($Pedido as $Ped) {
												echo(mb_convert_case($Ped['nome'], MB_CASE_TITLE, 'UTF-8'));
												if($Ped['promocao_ativo'] === 1){
													echo "
														- De:<del> R$ {$Ped['preco']} </del> Por: R$ {$Ped['promocao']}
													";
												}else{
													echo "
														- R$ {$Ped['preco']}
													";												
												}
												echo('<br>');
											}
										?>
									</li>
									<li class="list-group-item">
										<b>AeroFusion Company S.A</b>
										<b>CNPJ: 61.416.543/0001-89</b><br>
										<b>Cliente - </b><?php echo($_SESSION['Nome']) ?> <b>Email - </b><?php echo($_SESSION['Email']) ?>
									</li>
									<li class="list-group-item text-center">
										<button type="button" class="btn btn-primary p-2" onclick="baixaBoleto()">Baixar Boleto</button>
									</li>
								</ul>
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

	function finalizarPedido(){

	}

	function retornaClasseCartao($Ban){
		$Bandeiras = "MASTERCARD;ELO;AMERICAN EXPRESS;DISCOVER;DINERS;JCB;JCB15;MAESTRO;UNIONPLAY;VISA";
		$BanClasses =  "mastercard;elo;amex;discover;diners;jcb;jcb15;maestro;unionplay;visa";
		$BanVetor = [[], []];

		$BanVetor[0] = explode(';', $Bandeiras);
		$BanVetor[1] = explode(';', $BanClasses);

		for($nCont = 0; $nCont <= count($BanVetor[0]) -1; $nCont++){
			if($BanVetor[0][$nCont] === $Ban){
				return $BanVetor[1][$nCont];
			}
		}
	}

	function retornaCartao(){
		$_GET['Card'] = substr($_GET['Card'], 2, strlen($_GET['Card']));;
		$Cartao = new Cartao(Email: $_SESSION['Email']);
		$Ret = $Cartao->getCartao($_GET['Card']);
		$Ret[0]['bandeira'] = retornaClasseCartao($Ret[0]['nome_ban']);
		print(json_encode($Ret));//Retorna um JSON dos dados
	}
?>