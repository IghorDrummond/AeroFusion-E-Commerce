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

	/*
		Falta atualizar favoritos removido
		Falta Criar o Cron Job

	*/


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
									<?php
										if($Ped['nome'] === 'Pendente'){
									?>
										<button class="btn btn-primary rounded" onclick="prosseguirPedido(<?php echo($Ped['id_ped']) ?>)">
											Prosseguir para o pagamento
										</button>
									<?php
										}
									?>

									<?php
										if($Ped['nome'] === 'Pendente' or $Ped['nome'] === 'Aguardando Envio'){
									?>
										<button class="btn btn-danger rounded" onclick="cancelaPedido(<?php echo($Ped['id_ped']) ?>)">
											Cancelar Pedido
										</button>
									<?php
										}
									?>

									<?php
										if($Ped['nome'] === 'Entregue'){
									?>
										<button class="btn btn-info rounded" onclick="cancelaPedido(<?php echo($Ped['id_ped']) ?>)">
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

			<article class="mt-2 bg-warning rounded p-1">
				<div>
					<h4 id="pedidos">Favoritos</h4>
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
							if($Fav['disponibilidade'] != "SIM"){
								$Class = " bg-secondary text-white ";
							}
					?>	
						<tr class="<?php echo $Class ?>">
							<td onclick="maisDetalhes(<?php echo( $Fav['id_prod'] ) ?>)">
								<!-- Inicio do carousel -->
								<div class="carousel slide" data-ride="carousel" data-interval="3000" data-pause="false">
									<div class="carousel-inner">
										<!-- Imagens do carousel -->
										<?php 
											for($i = 1; $i <= 5; $i++){
												if($Fav['img' . strval($i)] === ''){
													continue;
												}
										?>	
										<div class="carousel-item <?php print($i === 2 ? "active" : "") ?>">
											<img src="img/<?php echo($Fav['img' . strval($i)]); ?>" class="imagem_carousel img-fluid">
										</div>
										<?php
											}
										?>
									</div>
								</div>

								<?php
									if($Fav['vizu_3d'] === 1){
										echo "<span class='bg-secondary px-1 rounded text-white font-weight-bold float-left'> 3D <i class='fa-solid fa-cubes'></i></span>";
									}
								?>
							</td>
							<td class="text-center">

							<?php
								if($Fav['disponibilidade'] === "SIM"){
							?>
								<h6><?php echo mb_convert_case($Fav['nome'], MB_CASE_TITLE, 'UTF-8');?></h6>
								<small class="text-info"><?php echo mb_convert_case($Fav['nome_cat'], MB_CASE_TITLE, 'UTF-8');?></small>
								<br>
								<?php
									if($Fav['promocao_ativo'] === 1){
								?>
									<del>De R$ <?php echo($Fav['preco']) ?></del>
									<span class="d-inline mt-1 text-success">Por R$ <?php echo(strval($Fav['promocao'])) ?></span>
								<?php
									}else{ 
								?>
									<span class="d-inline mt-1"><?php echo('R$ ' . strval($Fav['preco'])) ?></span>
								<?php
									}
								?>
								<br>
								<button class="btn btn-primary" onclick="maisDetalhes(<?php echo( $Fav['id_prod'] ) ?>)">
									Ver Produto
								</button>
								<button class="btn btn-danger" onclick="favorito(this, <?php echo($Fav['id_prod']); ?>)">
									Remover <i class="fa-solid fa-star"></i>
								</button>
							<?php
								}else{
							?>
								<p class="text-danger font-weight-bold">
									Produto fora de estoque!!!
								</p>
								<button class="btn btn-danger" onclick="favorito(this, <?php echo($Fav['id_prod']); ?>)">
									Remover <i class="fa-solid fa-star"></i>
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

			<article class="mt-2 bg-warning rounded p-1">
				<h4>Protocolos</h4>
			</article>

			<article class="mt-2 bg-warning rounded p-1">
				<h4>Carrinho</h4>
				<table>
					<thead>
						<tr>
							<th>Item</th>
						</tr>
					</thead>
				</table>	
			</article>
			
			<article class="mt-2 bg-warning rounded p-1">
				<h4>Configuração</h4>
			</article>
		</section>