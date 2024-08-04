<?php
	//Bibliotecas
	require_once ('lib/compras.php');
	require_once ('lib/conta.php');
	require_once ('lib/configuracao.php');
	require_once ('lib/produtos.php');

	use Acesso\Endereco;
	use Cadastro\RenovarSenha;
	use Cadastro\Cartao;
	use Compra\ApagarItem;
	use Compra\VerCarrinho;
	use Compra\atualizaCarrinho;
	use Pedido\novoPedido;
	use Produto\Favoritos;
	use Configuracao\Configuracao;
	use Configuracao\AtualizaUsuario;
	use Configuracao\ConfigEndereco;

	if (session_status() === PHP_SESSION_NONE) {
		session_start();
	}

	//Declaração de variaveis
	//String
	$Opc = (!isset($_GET['Opc']) or empty($_GET['Opc'])) ? '' : $_GET['Opc'];
	//Objetos
	$Compras = null;
	$Cartao = null;
	$Carrinho = null;
	$ConfigCarrinho = null;
	$Senha = null;
	$Endereco = null;
	$ItemPed = null;
	$Favoritos = null;

	/*
		Falta Criar o Cron Job
		Falta apagar cartão 
		Falta "apagar" endereço
	*/

	switch ($Opc) {
		case '2':
			Pedidos();
			break;
		case '3':
			Favoritos();
			break;
		case '4':
			Protocolos();
			break;
		case '5':
			Carrinho();
			break;
		case '6':
			Configuracao();
			break;
		case '7':
			AtualizarImagem();
			break;
		case '8':
			AtualizaNome();
			break;
		case '9':
			deletarEnd();
			break;
		default:
			Pedidos();
			Favoritos();
			Protocolos();
			Carrinho();
			Configuracao();
			break;
	}

	//Escopo
	function Pedidos(){
		$Compras = new novoPedido();
?>
		<!-- Campo de seleção de areas -->
		<aside class="p-3">
			<nav id="nav_conteudo" class="p-1 h-50">
				<a class="navbar-brand mx-1" href="#">
					Seu Painel
					<img id="foto_perfil" src="img/<?php echo($_SESSION['Foto']); ?>" class="rounded-circle border img-fluid" width="50" height="50"> 
				</a>
				<ul class="navegacao_lista">
					<li>
						<a class="nav-link" href="#Pedidos">Pedidos</a>
					</li>
					<li>
						<a class="nav-link" href="#Favoritos">Favoritos</a>
					</li>
					<li>
						<a class="nav-link" href="#Protocolos">Protocolos</a>
					</li>
					<li>
						<a class="nav-link" href="#Carrinho">Carrinho</a>
					</li>
					<li>
						<a class="nav-link" href="#Configuracao">Configuração</a>
					</li>
				</ul>
			</nav>
		</aside>
		<!-- Inicio dos conteúdos -->
		<section class="w-100">
			<article id="Pedidos" class="mt-2 bg-warning rounded p-1">
				<h1>Pedidos Efetuados</h1>
				<table class="w-100 bg-white shadow rounded text-center">
					<thead>
						<tr>
							<th>Código do Pedido</th>
							<th>Data de Abertura</th>
							<th>Status</th>
							<th>Valor</th>
							<th>Mais Detalhes</th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ($Compras->getPedidos($_SESSION['Email']) as $key => $Ped) {
							?>
							<tr <?php echo ($key % 2 === 0 ? 'class="bg-light linha_ped"' : '') ?>>
								<td>#<?php echo $Ped['id_ped']; ?></td>
								<td><time><?php echo date('d/m/Y H:i', strtotime($Ped['data_pedido'])); ?></time></td>
								<td>
									<?php
									//Valida qual é o status do pedido
									switch ($Ped['nome']) {
										case 'Cancelado':
											echo ("<i class='fa-solid fa-ban mx-1' style='color: red; margin-top: 15px;'></i>Cancelado");
											break;
										case 'Pendente':
											echo ("<i class='fa-solid fa-spinner mx-1' style='color: orangered; margin-top: 15px;'></i>Pendente");
											break;
										case 'Aguardando Envio':
											echo ("<i class='fa-solid fa-dolly mx-1' style='color: orange; margin-top: 15px;'></i>" . $Ped['nome']);
											break;
										case 'Transportando':
											echo ("<i class='fa-solid fa-truck-fast mx-1' style='color: blueviolet; margin-top: 15px;'></i>" . $Ped['nome']);
											break;
										case 'Saiu para entrega':
											echo ("<i class='fa-solid fa-truck-arrow-right mx-1' style='color: blue; margin-top: 15px;'></i>" . $Ped['nome']);
											break;
										case 'Entregue':
											echo ("<i class='fa-solid fa-check-to-slot mx-1' style='color: green; margin-top: 15px;'></i>" . $Ped['nome']);
											break;
									}
									?>
								</td>
								<td class="text-right"><?php echo $Ped['valor_total']; ?></td>
								<td class="p-3">
									<button class="btn btn-transparent text-center w-100 d-block"
										onclick="ativarItens(<?php echo $key; ?>, event)">
										<i class="fa-solid fa-chevron-down"></i>
									</button>
								</td>
							</tr>
							<tr class="itens d-none">
								<td colspan="5" class="shadow itens_tabela">
									<?php
									$ItemPed = $Compras->getPedido($Ped['id_ped'], $_SESSION['Email']);
									foreach ($ItemPed as $Item) {
										?>
										<div class="d-flex flex-wrap border border-dark rounded my-1">
											<div class="mr-auto">
												<img src="img/<?php echo $Item['img1'] ?>" class="img-fluid rounded mr-auto"
													width="180">
											</div>
											<div class="d-flex flex-lg-column text-lg-right text-center p-2">
												<h6>Nome: <?php echo mb_convert_case($Item['nome'], MB_CASE_TITLE, 'UTF-8') ?></h6>
												<h6>Quant: <?php echo $Item['quant']; ?></h6>
												<h6>preço total do produto: R$ <?php echo $Item['preco_item']; ?></h6>
											</div>
										</div>
										<?php
									}
									//Fazer validações sobre os botãos para cada status do pedido
									?>
									<div class="text-lg-right text-left px-2">
										<?php
										if ($Ped['nome'] === 'Pendente') {
											?>
											<button class="btn btn-primary rounded"
												onclick="prosseguirPedido(<?php echo ($Ped['id_ped']) ?>)">
												Prosseguir para o pagamento
											</button>
											<?php
										}
										?>

										<?php
										if ($Ped['nome'] === 'Pendente' or $Ped['nome'] === 'Aguardando Envio') {
											?>
											<button class="btn btn-danger rounded"
												onclick="cancelaPedido(<?php echo ($Ped['id_ped']) ?>)">
												Cancelar Pedido
											</button>
											<?php
										}
										?>

										<?php
										if ($Ped['nome'] === 'Entregue') {
											?>
											<button class="btn btn-info rounded"
												onclick="cancelaPedido(<?php echo ($Ped['id_ped']) ?>)">
												Avaliar Pedido
											</button>
											<?php
										}
										?>
									</div>
								</td>
							</tr>
							<?php
						}
						?>
					</tbody>
				</table>
			</article>
<?php
	}

	function Favoritos(){
		$Favoritos = new Favoritos($_SESSION['Email']);
?>
			<article id="Favoritos" class="mt-2 bg-warning rounded p-1">
				<h1>Favoritos</h1>
				<div>
					<table class="w-100 mt-2 bg-white">
						<thead class="text-center">
							<th>Imagens</th>
							<th>Detalhes</th>
						</thead>
						<tbody>
							<?php
							$Favoritos = new Favoritos($_SESSION['Email']);
							foreach ($Favoritos->getFavoritos() as $Key => $Fav) {

								$Class = "";
								if ($Fav['disponibilidade'] != "SIM") {
									$Class = " bg-secondary text-white ";
								}
								?>
								<tr class="<?php echo $Class ?>">
									<td onclick="maisDetalhes(<?php echo ($Fav['id_prod']) ?>)">
										<!-- Inicio do carousel -->
										<div class="carousel slide" data-ride="carousel" data-interval="3000" data-pause="false">
											<div class="carousel-inner">
												<!-- Imagens do carousel -->
												<?php
												for ($i = 1; $i <= 5; $i++) {
													if ($Fav['img' . strval($i)] === '') {
														continue;
													}
													?>
													<div class="carousel-item <?php print ($i === 2 ? "active" : "") ?>">
														<img src="img/<?php echo ($Fav['img' . strval($i)]); ?>"
															class="imagem_carousel img-fluid">
													</div>
													<?php
												}
												?>
											</div>
										</div>

										<?php
										if ($Fav['vizu_3d'] === 1) {
											echo "<span class='bg-secondary px-1 rounded text-white font-weight-bold float-left'> 3D <i class='fa-solid fa-cubes'></i></span>";
										}
										?>
									</td>
									<td class="text-center">

										<?php
										if ($Fav['disponibilidade'] === "SIM") {
											?>
											<h6><?php echo mb_convert_case($Fav['nome'], MB_CASE_TITLE, 'UTF-8'); ?></h6>
											<small
												class="text-info"><?php echo mb_convert_case($Fav['nome_cat'], MB_CASE_TITLE, 'UTF-8'); ?></small>
											<br>
											<?php
											if ($Fav['promocao_ativo'] === 1) {
												?>
												<del>De R$ <?php echo ($Fav['preco']) ?></del>
												<span class="d-inline mt-1 text-success">Por R$
													<?php echo (strval($Fav['promocao'])) ?></span>
												<?php
											} else {
												?>
												<span class="d-inline mt-1"><?php echo ('R$ ' . strval($Fav['preco'])) ?></span>
												<?php
											}
											?>
											<br>
											<button class="btn btn-primary" onclick="maisDetalhes(<?php echo ($Fav['id_prod']) ?>)">
												Ver Produto
											</button>
											<button class="btn btn-danger" onclick="deletarFav(<?php echo ($Fav['id_prod']); ?>)">
												Remover
											</button>
											<?php
										} else {
											?>
											<p class="text-danger font-weight-bold">
												Produto fora de estoque!!!
											</p>
											<button class="btn btn-danger"
												onclick="deletarFav(<?php echo $Key ?>, <?php echo ($Fav['id_prod']); ?>)">
												Remover
											</button>
											<?php
										}
										?>
									</td>
								</tr>
								<?php
							}
							?>
						</tbody>
					</table>
			</article>
<?php
	}

	function Protocolos(){
		$Favoritos = new Favoritos($_SESSION['Email']);
?>
			<article id="Protocolos" class="mt-2 bg-warning rounded p-1">
				<h1>Protocolos</h1>
			</article>
<?php
	}

	function Carrinho(){
		$Carrinho = new VerCarrinho($_SESSION['Email']);
		$Carrinho = $Carrinho->retornaValores();
		$Total = isset($Carrinho[0]['total_carrinho']) ? $Carrinho[0]['total_carrinho'] : 0;
?>
			<article id="Carrinho" class="mt-2 bg-warning rounded p-1">
				<h1>Carrinho</h1>
				<table class="w-100 bg-white">
					<thead>
						<tr class="text-center">n
							<th>Imagens</th>
							<th>Detalhes do Carrinho</th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ($Carrinho as $Car) {
							?>
							<tr class=" <?php echo $Car['estoque'] <= 0 ? 'bg-secondary' : '' ?>">
								<td class="px-2" onclick="maisDetalhes(<?php echo ($Car['id_prod']) ?>)">
									<!-- Inicio do carousel -->
									<div class="carousel slide" data-ride="carousel" data-interval="3000" data-pause="false">
										<div class="carousel-inner">
											<!-- Imagens do carousel -->
											<div class="carousel-item active">
												<img src="img/<?php echo ($Car['img']); ?>" class="imagem_carousel img-fluid">
											</div>
											<?php
											for ($i = 2; $i <= 5; $i++) {
												if ($Car['img' . strval($i)] === '') {
													continue;
												}
												?>
												<div class="carousel-item <?php print ($i === 2 ? "active" : "") ?>">
													<img src="img/<?php echo ($Car['img' . strval($i)]); ?>"
														class="imagem_carousel img-fluid">
												</div>
												<?php
											}
											?>
										</div>
									</div>
									<?php
									if ($Car['vizu_3d'] === 1) {
										echo "<span class='bg-secondary px-1 rounded text-white font-weight-bold float-left'> 3D <i class='fa-solid fa-cubes'></i></span>";
									}
									?>
								</td>
								<td class="text-center">
									<h6><?php echo mb_convert_case($Car['produto'], MB_CASE_TITLE, 'UTF-8'); ?></h6>
									<h6>Preço do Item:
										<?php
										if ($Car['promocao_ativo'] === 1) {
											?>
											<del>De R$ <?php echo ($Car['preco']) ?></del>
											<span class="d-inline mt-1 text-success">Por R$
												<?php echo (strval($Car['promocao'])) ?></span>
											<?php
										} else {
											?>
											<span class="d-inline mt-1"><?php echo ('R$ ' . strval($Car['preco'])) ?></span>
											<?php
										}
										?>
									</h6>
									<h6>Tamanho: <?php echo ('R$ ' . strval($Car['tamanho'])) ?></h6>
									<h6>Quantidade: <?php echo ($Car['quant']) ?></h6>
									<h5>Total do Item: <?php echo ('R$ ' . strval($Car['total_item'])) ?></h5>
									<?php
									if ($Car['estoque'] > 0) {
										?>
										<div class="btn-group text-white font-weight-bold bg-primary rounded" role="group">
											<button class="btn btn-primary rounded"
												onclick="atualizaQuant('+', <?php echo ($Car['id_car']); ?>, this)">
												+
											</button>
											<span class="mx-2 quantidade_acao"><?php echo ($Car['quant']); ?></span>
											<button type="button" class="btn btn-primary rounded"
												onclick="atualizaQuant('-', <?php echo ($Car['id_car']); ?>, this)">
												-
											</button>
										</div>
										<?php
									} else {
										echo "<h5 class='text-danger'>Produto fora de estoque.</h6>";
									}
									?>
									<button class="btn btn-danger text-white font-weight-bold"
										onclick="deletaProdCar(<?php echo ($Car['id_car']); ?>)">
										Deletar <i class="fa-solid fa-trash-can"></i>
									</button>
								</td>
							</tr>
							<?php
						}
						?>
					</tbody>
					<tfoot class="bg-white border rounded mt-2">
						<h2>Total do Carrinho: <?php echo ('R$ ' . strval($Total)) ?></h2>
					</tfoot>
				</table>
			</article>
			<?php
	}

	function Configuracao(){
		?>
			<article id="Configuracao" class="mt-2 bg-warning rounded p-1">
				<h1>Configuração</h1>
				<div class="bg-white rounded m-3 p-1">
					<form class="form-group" id="FormularioImagem" method="POST" enctype="multipart/form-data">
						<fieldset>
							<legend for="foto_perfil">Atualize sua foto de perfil</legend>
							Preview da Imagem:
							<img id="foto_perfil" name="foto_perfil" src="img/<?php echo ($_SESSION['Foto']) ?>"
								class="rounded-circle border border-dark img-fluid">
							<br><br>
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">Upload</span>
								</div>
								<div class="custom-file">
									<input id="arquivo" type="file" class="custom-file-input" name="Arquivo"
										aria-describedby="inputGroupFileAddon01" accept=".png,.jpeg,.jpg,.gif" required>
									<label class="custom-file-label" for="Arquivo">Procurar...</label>
								</div>
							</div>
							<p class="text-warning">
								Só pode formatos PNG, JPEG, JPG e GIF.<br>
								Imagem de no máximo 500kb.
							</p>
							<button type="submit" class="btn btn-primary rounded mt-2">
								Enviar <i class="fa-solid fa-paper-plane"></i>
							</button>
						</fieldset>
					</form>
					<hr>
					<form class="form-gorup" onsubmit="atualizarNome()">
						<fieldset>
							<legend>Atualizar Nome:</legend>
							<label for="nome">Nome atual: <b><?php echo ($_SESSION['Nome']); ?></b>.</label>
							<input type="text" name="nome" class="form-control" placeholder="Exemplo: Fulano de Tal " required>
							<button type="submit" class="btn btn-primary rounded mt-2">
								Enviar <i class="fa-solid fa-paper-plane"></i>
							</button>
						</fieldset>
					</form>
					<hr>
					<div class="mx-3">
						<legend>Adicionar Endereço:</legend>
						<div id="enderecos_cadastrados">
							<?php
							$Endereco = new Endereco($_SESSION['Email']);

							foreach ($Endereco->getEndereco() as $End) {
								?>
								<div class="p-2 border rounded my-1">
									<h6>Cep: <?php echo (mb_convert_case($End['cep'], MB_CASE_TITLE, 'UTF-8')) ?></h6>
									<h6>Rua: <?php echo (mb_convert_case($End['rua'], MB_CASE_TITLE, 'UTF-8')) ?></h6>
									<h6>Numero: <?php echo (mb_convert_case($End['numero'], MB_CASE_TITLE, 'UTF-8')) ?></h6>
									<h6>Cidade: <?php echo (mb_convert_case($End['cidade'], MB_CASE_TITLE, 'UTF-8')) ?></h6>
									<h6>Bairro: <?php echo (mb_convert_case($End['bairro'], MB_CASE_TITLE, 'UTF-8')) ?></h6>
									<h6>Estado: <?php echo (mb_convert_case($End['uf'], MB_CASE_TITLE, 'UTF-8')) ?></h6>
									<h6>Complemento: <?php echo (mb_convert_case($End['complemento'], MB_CASE_TITLE, 'UTF-8')) ?>
									</h6>
									<h6>Referência: <?php echo (mb_convert_case($End['referencia'], MB_CASE_TITLE, 'UTF-8')) ?></h6>
									<button class="btn btn-sm btn-danger" onclick="deletarEnd(this)">Deletar</button>
								</div>
								<?php
							}
							?>
						</div>
						<p class="text-warning">Selecione um endereço valido para cadastrar em sua conta.</p>
						<button type="button" class="btn btn-primary rounded mt-2" onclick="adicionarEnd()">
							Cadastrar um novo endereço <i class="fa-solid fa-paper-plane"></i>
						</button>
					</div>
					<hr>
					<div class="mx-3">
						<legend>Adicionar Cartão:</legend>

						<div class="caixa_cartao">
							<div id="cartao">
								<!-- Parte frontal do Cartão -->
								<div class="cartaoLado d-flex flex-column">
									<img class="img-fluid ml-auto" src="https://cdn-icons-png.flaticon.com/512/2695/2695969.png"
										name="operadora" alt="Operadora Cartão" width="50">
									<label for="">Nome impresso</label>
									<input type="text" id="nome_cartao" placeholder="FULANO DE TAL" name="nome_cartao"
										onchange="guardaNome(this)" required>
									<label>Número do cartão</label>
									<input id="numero_cartao" name="numero_cartao" type="text" placeholder="0000 0000 0000 0000"
										maxlength="19" maxlength="19" required onchange="bandeira(this)"
										onkeyup=" maskNumero(this)">
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
											<input id="vencimento" name="vencimento" type="text" placeholder="00/0000"
												maxlength="7" onkeyup="maskValidade(this)" required>
										</div>
										<div>
											<label>CVV</label><br>
											<input id="cvv" name="cvv" type="text" placeholder="000" maxlength="3" required
												onchange="guardaCvv(this)">
										</div>
									</div>
									<button type="button" title="Virar cartão" class="ml-auto" onclick="virarCartao(0)">
										Virar<i class="fa-solid fa-hand-point-right fa-xl m-3"></i>
									</button>
									<br>
								</div>

							</div>
						</div>
						<div class="text-center">
							<label class="mt-4">Escolha um outro cartão:</label>
							<div class="dropdown">
								<button class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
									arial-expanded="false">Cartões</button>
								<div class="dropdown-menu">
									<?php
									$Cartao = new Cartao(Email: $_SESSION['Email']);
									$Cartao = $Cartao->getCartao('');
									if (isset($Cartao[0]['numero_cartao'])) {
										foreach ($Cartao as $nCont => $Cartoes) {
											?>
											<div class="dropdown-tem p-2" id="#X<?php print ($Cartoes['id_card']) ?>">
												<h3>Cartão:
													<?php echo (substr($Cartoes['numero_cartao'], 0, 4)) . ' **** **** **' . substr($Cartoes['numero_cartao'], 17, 19) ?>
												</h3>
												<small>Nome: <?php echo strtoupper($Cartoes['nome_cartao']); ?></small><br>
												<small>Vencimento: <time>
														<?php echo $Cartoes['validade_formatada'] ?></time></small><br>
												<small>Bandeira: <img src="<?php echo ($Cartoes['img_ban']) ?>" class="img-fluid" width="50"
														height="50" alt="<?php print retornaClasseCartao($Cartoes['nome_ban']); ?>"></small>
												<br>
												<button type="button" class="btn btn-primary btn-sm mt-2"
													onclick="cartaoSelecionado(this)">Selecionar</button>
												<button class="btn btn-danger btn-sm mt-2" onclick="deletarCartao(this)">Deletar</button>
											</div>
											<hr>
											<?php
										}
									}
									?>
								</div>
							</div>
							OU<br>
							<button type="submit" class="btn btn-primary rounded mt-2" onclick="addCartao()">
								Adicionar Cartão<i class="fa-solid fa-paper-plane"></i>
							</button>
							</duv>
						</div>
					</div>
			</article>
		</section>
		<?php
	}

	function AtualizarImagem()
	{
		//Constantes
		define('MAXTAMANHO', (500 * 1024));
		define('FORMATO', ['image/png', 'image/jpeg', 'image/jpg', 'image/gif']);
		//Declaração de Variaveis
		//Objeto
		$AttImagem = null;

		//Verifica se foi enviado o arquivo
		if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {

			//Valida se o tamanho é maior que o permitido
			if ($_FILES['file']['size'] > MAXTAMANHO) {
				echo "TAMANHO";
				exit;
			}
			//Valida se o formato não é o padrão exigido
			if (!in_array($_FILES['file']['type'], FORMATO)) {
				echo "FORMATO";
				exit;
			}
			//Valida se houve problemas no envio do arquivo
			if ($_FILES['file']['error'] === UPLOAD_ERR_OK) {
				if (!is_dir('../img/')) {
					mkdir('../img/', 0777, true);//Da permissão de criar o diretorio e ainda com escrita
				}

				$diretorio = '../img/' . $_FILES['file']['name'];

				// Move o arquivo para o diretório de upload
				if (move_uploaded_file($_FILES['file']['tmp_name'], $diretorio)) {
					$AttImagem = new Configuracao($_SESSION['Email']);
					$AttImagem->setImagem($_FILES['file']['full_path']);
					$_SESSION['Foto'] = $_FILES['file']['full_path'];
					echo "SUCESSO";
				} else {
					echo "ERRO";
				}
			} else {
				echo "ARQUIVO";
			}
		}
	}

	function AtualizaNome()
	{
		$Perfil = new AtualizaUsuario(Email: $_SESSION['Email']);

		if (!empty($_GET['Nome']) and isset($_GET['Nome'])) {
			$Perfil->setNome(strtoupper($_GET['Nome']));
			$_SESSION['Nome'] = mb_convert_case(strtoupper($_GET['Nome']), MB_CASE_TITLE, 'UTF-8');
		}
	}

	function retornaClasseCartao($Ban)
	{
		$Bandeiras = "MASTERCARD;ELO;AMERICAN EXPRESS;DISCOVER;DINERS;JCB;JCB15;MAESTRO;UNIONPLAY;VISA";
		$BanClasses = "mastercard;elo;amex;discover;diners;jcb;jcb15;maestro;unionplay;visa";
		$BanVetor = [[], []];

		$BanVetor[0] = explode(';', $Bandeiras);
		$BanVetor[1] = explode(';', $BanClasses);

		for ($nCont = 0; $nCont <= count($BanVetor[0]) - 1; $nCont++) {
			if ($BanVetor[0][$nCont] === $Ban) {
				return $BanVetor[1][$nCont];
			}
		}
	}


	function deletarEnd(){
	    //Palavras a serem validadas
	    $palavrasParaRemover = ["CEP:", "RUA:", "ESTADO:", "REFERENCIA:", "COMPLEMENTO:", "NUMERO:", "CIDADE:", "BAIRRO:"];
	    $nCont = 0;

	    // Verificar se as palavras estão presentes nos dados
	    foreach ($_POST as $valor) {
	    	if(stripos($palavrasParaRemover[$nCont], trim(strtoupper($valor))) != false){
	    		echo 'PALAVRA';
	    		return null;
	    	}
	    	$nCont++;
	    }

		//Valida se todos os dados estão correspondentes ao necessário
		if(
			isset($_POST['cep']) and isset($_POST['rua']) and isset($_POST['numero']) 
			and isset($_POST['bairro']) and isset($_POST['cidade']) and isset($_POST['estado'])
			and isset($_POST['complemento']) and isset($_POST['referencia'])
		){	
			//Deleta o endereço caso for encontrado
			$Endereco = new ConfigEndereco($_SESSION['Email']);
			$Endereco->delEnd($_POST['cep'], $_POST['rua'], $_POST['numero'], $_POST['bairro'], $_POST['cidade'], $_POST['estado'], $_POST['complemento'], $_POST['referencia']);
		}else{
			echo 'DADOS';
			return null;
		}
	}
?>