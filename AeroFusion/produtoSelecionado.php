<?php
	require_once('lib/produtos.php');
	use Produto\AvalicaoProduto;
	use Produto\Produto;
	use Produto\Tamanhos;
	use Produto\Favoritos;
	//Declaração de variaveis
	//Numerico
	$Indicador = 0;
	$quantFotos = 0;
	//Array
	$Produtos = [];
	$Avaliacoes = [];
	$Tamanhos = [];
	//Objetos
	$Produto = null;
	$Avaliacao = null;
	$Carrinho = null;
	$Favorito = null;
	$Tamanho = null;

	//Escopo==============
	$Produto = new Produto($_GET['Prod']);
	$Produtos = $Produto->retornaValores();

	if(isset($_SESSION['Login']) and $_SESSION['Login']){
		$Favorito = new Favoritos(Email: $_SESSION['Email']);//Inicia objeto favoritos
	}

	if($Produtos != false){
		//Monta avaliação do produto caso houver
		$Avaliacao = new AvalicaoProduto($_GET['Prod']);
		$Avaliacoes = $Avaliacao->retornaValores();
		//Monta tamanhos disponiveis para esse par de tênis
		$Tamanho = new Tamanhos($Produtos['tamanho']);
		$Tamanhos = $Tamanho->retornaValores();

		montaProduto($Produtos, $Avaliacoes, $Tamanhos, $Favorito);
	}else{
		faltaProduto();
	}

	//-------------Funções
	function montaProduto($Produtos, $Avaliacoes, $Tamanhos, $Favorito){
		$Quant = 1;
		
?>
			<section class="text-warning">
				<article>
					<div id="carouselExampleIndicators" class="carousel slide w-100" data-ride="carousel">
						<div class="carousel-inner">
							<!-- Inicio das Imagens -->
							<div class="carousel-item active">
								<img class="d-block m-auto img-fluid" src="img/<?php echo($Produtos['img1']); ?>">
							</div>
						<?php
							for($nCont = 2; $nCont <= 5; $nCont++) {
								if($Produtos['img'. strval($nCont)] === ''){
									continue;
								}
								$Quant++;
						?>
							<div class="carousel-item">
								<img class="d-block m-auto img-fluid " src="img/<?php echo($Produtos['img' . strval($nCont)]); ?>">
							</div>
						<?php
							}
						?>
						<!-- Fim das Imagens -->
						</div>		
						<!-- Inicio dos botões do Carousel -->
						<a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
							<span class="carousel-control-prev-icon bg-primary rounded" col aria-hidden="true"></span>
							<span class="sr-only">Anterior</span>
						</a>
						<a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
							<span class="carousel-control-next-icon bg-primary rounded" aria-hidden="true"></span>
							<span class="sr-only">Próximo</span>
						</a>
						<!-- Fim dos botões do Carousel -->			
					</div>
				</article>
				<article class="p-4 bg-white d-flex flex-column justify-content-center">
					<h3 ><?php echo mb_convert_case($Produtos['nome'], MB_CASE_TITLE, 'UTF-8'); ?></h3>
					<h6 class="text-info">Categoria: <?php echo ucfirst(strtolower($Produtos['categoria'])); ?></h6>
					<?php
						//Valida se o produto está disponivel
						if($Produtos['disponibilidade'] === 'SIM'){
							//Valida se o produto tem uma promoção ativada
							if($Produtos['promocao_ativo'] != 1){

					?>
						<h1 id="preco">R$ <?php echo $Produtos['preco']; ?></h1>
					<?php
							}else{
					?>	
						<span><del>R$ <?php echo $Produtos['preco']; ?></del></span>
						<h1 id="preco">
							R$ <?php echo $Produtos['promocao']; ?>
							<span class="bandeira_promocao badge badge-pill badge-dark">Promoção</span>
						 </h1>
					<?php
							}
					?>
					<p>
						ou<br>
						12x de <span id="parcela">R$ <?php print(number_format( ($Produtos['promocao_ativo'] === 1 ? (float)$Produtos['promocao'] :  (float)$Produtos['preco']) / 12, 2)) ?></span> sem juros!
					</p>
					Escolha um tamanho:
					<div class="d-flex w-100 rounded p-1">
						<?php 
							foreach ($Tamanhos as $tam) {
						?>
							<button class="p-2 border border-secondary rounded mx-1" onclick="escolheTam(<?php echo($tam['id_tam']); ?>)">
							<?php echo($tam['nome_tam']); ?>
							</button>
						<?php
							}
						?>
					</div>
					<h6 id="quantidade_texto">Quant: 1</h6>
					<input onchange="alteraQuant()" id="quantidade" class="form-control w-25 d-inline border" type="range" min="1" max="<?php echo($Produtos['estoque']); ?>">
					<div class="d-flex flex-wrap botoes">
						<button class="border border-warning rounded m-1 p-3 bg-warning text-white font-weight-bold" onclick="comprar(<?php echo($_GET['Prod']) ?>)">Comprar</button>
						<button class="border border-warning rounded m-1 p-3 bg-warning text-white font-weight-bold" onclick="favorito(this, <?php echo($_GET['Prod']); ?>)">Favoritar
						<?php
							$Ret = false;

							if(!is_null($Favorito)){
								$Ret = $Favorito->retornaValores($_GET['Prod']);
							}
							//Valida se usuário tem o produto favoritado
							$Class = $Ret ? 'fa-solid fa-star' : 'fa-regular fa-star';
						?>
							<i class="<?php echo($Class); ?>"></i>
						</button>
					</div>
					<p class="mt-2 border p-1 rounded">
						<?php echo ucfirst(strtolower($Produtos['descricao'])); ?>		
					</p>
					<?php
						}else{
					?>
					<h1 class="text-center">
						Este produto está temporariamente esgotado.
					</h1>
					<h4 class="text-secondary text-center">
						Pedimos desculpas pelo inconveniente. <br>
						Por favor, verifique novamente em breve ou entre em contato conosco para mais informações.
					</h4>
					<p class="text-lg-left text-center mt-2">
						Último preço registrado deste produto: R$ <?php echo($Produtos['preco']); ?>
					</p>
					<?php
						}
					?>
				</article>
			</section>
			<section class="bg-warning border border-top border-warning">
				<h2 class="text-center bg-warning">Avaliações</h2>
				<div class="border p-2 rounded bg-light text-center mensagem">
					<h5>Otimo Produto!</h5>
					<img src="img/novo_usuario.png" class="img-fluid rounded-circle border border-dark" width="60">
					<br>
					<div class="estrelas">
						<i class="fa-solid fa-star"></i>
						<i class="fa-solid fa-star"></i>
						<i class="fa-solid fa-star"></i>
						<i class="fa-solid fa-star-half-stroke"></i>
						<i class="fa-regular fa-star"></i>
					</div>
					<p class="mt-3">
						<span>
							<time>15/12/8000</time> -
							Nome Usuário
						</span>:<br> Mensagem deixada
					</p>
					<div>
						imagens caso houver
					</div>
				</div>
				<div class="border p-2 rounded bg-light text-center mensagem">
					<h5>Otimo Produto!</h5>
					<img src="img/novo_usuario.png" class="img-fluid rounded-circle border border-dark" width="60">
					<br>
					<div class="estrelas">
						<i class="fa-solid fa-star"></i>
						<i class="fa-solid fa-star"></i>
						<i class="fa-solid fa-star"></i>
						<i class="fa-solid fa-star-half-stroke"></i>
						<i class="fa-regular fa-star"></i>
					</div>
					<p class="mt-3">
						<span>
							<time>15/12/8000</time> -
							Nome Usuário
						</span>:<br> Mensagem deixada
					</p>
					<div>
						imagens caso houver
					</div>
				</div>
				<div class="border p-2 rounded bg-light text-center mensagem">
					<h5>Otimo Produto!</h5>
					<img src="img/novo_usuario.png" class="img-fluid rounded-circle border border-dark" width="60">
					<br>
					<div class="estrelas">
						<i class="fa-solid fa-star"></i>
						<i class="fa-solid fa-star"></i>
						<i class="fa-solid fa-star"></i>
						<i class="fa-solid fa-star-half-stroke"></i>
						<i class="fa-regular fa-star"></i>
					</div>
					<p class="mt-3">
						<span>
							<time>15/12/8000</time> -
							Nome Usuário
						</span>:<br> Mensagem deixada
					</p>
					<div>
						imagens caso houver
					</div>
				</div>
			</section>
<?php
	}
	function faltaProduto(){
		echo('<h1>Nenhum Produto Encontrado!</h1>');
	}
?>