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
		$Tamanho = new Tamanhos($Produtos[0]['tamanho']);
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
								<img class="d-block m-auto img-fluid" src="img/<?php echo($Produtos[0]['img1']); ?>">
							</div>
						<?php
							for($nCont = 2; $nCont <= 5; $nCont++) {
								if($Produtos[0]['img'. strval($nCont)] === ''){
									continue;
								}
								$Quant++;
						?>
							<div class="carousel-item">
								<img class="d-block m-auto img-fluid " src="img/<?php echo($Produtos[0]['img' . strval($nCont)]); ?>">
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
					<h3 ><?php echo mb_convert_case($Produtos[0]['nome'], MB_CASE_TITLE, 'UTF-8'); ?></h3>
					<h6 class="text-info">Categoria: <?php echo ucfirst(strtolower($Produtos[0]['categoria'])); ?></h6>
					<h1 id="preco">R$ <?php echo ucfirst(strtolower($Produtos[0]['preco'])); ?></h1>
					<p>
						ou<br>
						12x de <span id="parcela">R$ <?php print(number_format( (float)$Produtos[0]['preco'] / 12, 2)) ?></span> sem juros!
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
					<input onchange="alteraQuant()" id="quantidade" class="form-control w-25 d-inline border" type="range" min="1" max="<?php echo($Produtos[0]['estoque']); ?>">
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
						<?php echo ucfirst(strtolower($Produtos[0]['descricao'])); ?>		
					</p>
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