<?php
	//Inicia a sessão 
	session_start();

	//Biblioteca
	require_once('lib/compras.php');
	require_once('lib/conta.php');
	require_once('lib/configuracao.php');
	use Compra\atualizaCarrinho;
	use Pedido\solicitaPedido;
	use Pedido\validaEstoque;
	use Pedido\novoPedido;
	use Pedido\validaCupom;
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
	if(!isset($_SESSION['Login']) or !$_SESSION['Login']){
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
	}

	function montaTela(){
		//Caso não houver produtos, ele retorna vazio
		if((isset($_SESSION['Produtos']) and empty($_SESSION['Produtos'])) or !isset($_SESSION['Produtos'])){
			semProdutos();
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
			<div class="w-100 rounded area_produtos my-3 border border-secondary p-2 <?php echo($Class); ?> ">
				<img src="img/<?php echo($Prod['img1']); ?>" width="200" class="rounded img-fluid">
				<div>
					<h6><?php echo( mb_convert_case($Prod['nome'], MB_CASE_TITLE, 'UTF-8') ); ?></h6>
					<h6>Quantidade: <?php echo($Prod['quant']) ?></h6>
					<span>Tamanho: <?php echo($Prod['nome_tam']); ?></span><br>
					<?php
						if($Prod['promocao_ativo'] === 1){
							echo "Preço Solitário: 
							De <del>R$ ". $Prod['preco'] . "</del> Por
							<mark class='bg-transparent text-warning'>R$ ". $Prod['promocao'] ."</mark>
							";
						}else{
							echo "Preço Solitário: R$ ". $Prod['preco'];							
						}
					?>
					<h6>Total do Item: R$ <?php echo($Prod['total_item']); ?></h6>
				</div>
				<!-- Campo para operação -->
				<div class="ml-auto">
					<button class="btn btn-danger text-white font-weight-bold" onclick="deletaItem(<?php echo( $Prod['id_car'] ); ?>)">
						Deletar <i class="fa-solid fa-trash-can"></i>
					</button>
					<div class="btn-group text-white font-weight-bold bg-primary" role="group">
						<button class="btn btn-primary rounded" onclick="atualizaQuantidade('+', <?php echo($Prod['id_car']);?>, this)">
							+
						</button>		
						<span class="mx-2 quantidade_acao"><?php echo( $Prod['quant'] ); ?></span>		
						<button type="button" class="btn btn-primary rounded" onclick="atualizaQuantidade('-', <?php echo($Prod['id_car']);?>, this)">
							-
						</button>	
					</div>
				</div>
			</div>
			<?php
				}

				if(!isset($Prod['total_carrinho'])){
					return null;
				}
			?>
		</article>
		<article class="p-5 bg-white d-flex flex-column align-items-center text-warning">
			<form id="compra" onsubmit="finalizarCompra()">
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
						<a class="dropdown-item" id="<?php echo($End['id_end']); ?>" onclick="selecionaEndereco(this)" data-title="Endereço <?php echo(($i+1)); ?>">
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
				<input  name="cupom" class="form-control rounded" placeholder="....." onchange="validaCupom()">				
			</form>
		</article>
	</section>
<?php
	}

	function removeProduto($Prod){
		$aux = null;
		$aux = explode(',', $_SESSION['Produtos']);
		unset($aux[array_search($Prod, $aux)]);
		$_SESSION['Produtos'] = implode(',', $aux);
	}

	function atualizaQuantidade($IdCar, $Quant){
 		$Estoque = new validaEstoque();

		//Valida se a quantidade inserida existe de acordo com o estoque
		if($Estoque->verificaEstoque($IdCar, $Quant, $_SESSION['Email'])){
			$Carrinho = new atualizaCarrinho(IdCar: $IdCar, Quant: $Quant);
			$Carrinho->atualizaQuantidade($_SESSION['Email']);
		}else{
			echo "ESTOQUE";
		}
	}

	function novoPedido(){
		//Cria objeto para inserir novo pedido
		$Pedido = new novoPedido(Email: $_SESSION['Email'], Produtos: $_SESSION['Produtos'],Endereco:  $_GET['Endereco'], Pagamento: $_GET['Pagamento'], Cupom: $_GET['Cupom']);
		//Cadastra novo pedido
		$Ret = $Pedido->setPedido();
		if($Ret['Inclusão']){
			echo 'S';
			unset($_SESSION['Produtos']);//Apaga o registro de produtos
		}else{
			echo 'N';
		}
	}

	function semProdutos(){
?>
	<section>
		<article class="bg-white border border-info d-flex flex-column align-items-center justify-content-center text-center p-2 w-100">
			<h1 class="text-warning">
				Sem Produtos para continuar...
			</h1>
			<p class="text-info">
				Lamentamos informar que seu pedido atual está sem produtos selecionados, por isso não podemos prosseguir com a compra. Para continuar explorando nossa variedade de produtos ou iniciar uma nova pesquisa, recomendamos visitar nossa página de pesquisa de produtos ou retornar à página principal.
			</p>
			<a class="btn btn-info text-white text-center font-weight-bold my-1" href="pesquisa.php">Conheçer nossos Produtos</a>
			<a class="btn btn-info text-white text-center font-weight-bold" href="index.php">Voltar para página Inicial</a>
		</article>
	</section>
<?php
	} 

	function cadastrarEnd(){
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
		}else{
			echo 'ERROR';
		}
	}

	function ativaCupom(){
		$Cupom  = new validaCupom(Email: $_SESSION['Email'], Produtos: $_SESSION['Produtos'], Cupom: $_GET['Cupom']);
		echo $Cupom->validaCupom();
	}
?>