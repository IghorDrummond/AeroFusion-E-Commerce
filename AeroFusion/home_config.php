<?php
	//Bibliotecas
	require_once('lib/compras.php');
	require_once('lib/conta.php');
	require_once('lib/configuracao.php');
	require_once('lib/produtos.php');

	use Acesso\Endereco;
	use Cadastro\RenovarSenha;
	use Cadastro\Cartao;
	use Compra\ApagarItem;
	use Compra\VerCarrinho;
	use Compra\atualizaCarrinho;
	use Pedido\novoPedido;
	use Produto\Favoritos;

	//Declaração de variaveis
	//Objetos
	$Compras = null;
	$Cartao = null;
	$Carrinho = null;
	$ConfigCarrinho = null;
	$Senha = null;
	$Endereco = null;
	$ItemPed = null;
	$Favoritos = null;

	//Escopo
	$Compras = new novoPedido();
	$Favoritos = new Favoritos($_SESSION['Email']);
?>
		<!-- Inicio dos conteúdos -->
		<section class="w-100">
			<article class="mt-2 bg-warning rounded p-1">
				<h4 id="pedidos">Pedidos Efetuados</h4>
				<table class="w-100 bg-white shadow rounded text-center">
					<thead >
						<tr>
							<th>Pedido</th>
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
							<td><time><?php echo date('d/m/Y H:i:s', strtotime($Ped['data_pedido'])); ?></time></td>
							<td><?php echo $Ped['nome']; ?></td>
							<td class="text-right"><?php echo $Ped['valor_total']; ?></td>
							<td class="p-3">
								<button class="btn btn-transparent text-center w-100 d-block" onclick="ativarItens(<?php echo $key; ?>, event)">
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
										<img src="img/<?php echo $Item['img1'] ?>" class="img-fluid rounded mr-auto" width="180">
									</div>
									<div class="d-flex flex-lg-column text-lg-right text-center p-2">
										<h6>Nome: <?php echo mb_convert_case($Item['nome'], MB_CASE_TITLE, 'UTF-8') ?></h6>
										<h6>Quant: <?php echo $Item['quant']; ?></h6>	
										<h6>preço total do produto: R$ <?php echo $Item['preco_item']; ?></h6>	
									</div>						
								</div>
							<?php 
								}

								//Fazer validaç~eos sobre os bot~eos para cada status do pedido
							?>
								<div class="text-lg-right text-left px-2">
									<button class="btn btn-primary rounded">
										Prosseguir para o pagamento
									</button>
									<button class="btn btn-danger rounded">
										Cancelar Pedido
									</button>
								</div>
							</td>
						</tr>
						<?php
							}
						?>
					</tbody>
				</table>
			</article>

			<article>
				
			</article>
		</section>