<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
	<?php require_once ('script/style.php'); ?>
	<!-- Estilo da Página -->
	<link rel="stylesheet" type="text/css" href="css/index.css">
	<!-- Titulo da Página -->
	<title>AeroFusion</title>
</head>

<body>
	<?php require_once('script/cabecalho.php'); ?>

	<div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel" style="height: 50vh;">
			<!-- Indicadores inferior do carousel -->
			<ol class="carousel-indicators">
				<li data-target="#carouselExampleIndicators" data-slide-to="0" class="indicador bg-dark rounded active"></li>
				<li data-target="#carouselExampleIndicators" data-slide-to="1" class="indicador bg-dark rounded"></li>
				<li data-target="#carouselExampleIndicators" data-slide-to="2" class="indicador bg-dark rounded"></li>
			</ol>

			<!-- Quadro 3D -->
			<div id="quadro" class="carousel-inner">
				<div class="carousel-caption">
					<p>
						Aproveite as últimas novidades com os mais recentes lançamentos dos modelos Jordan!
					</p>
				</div>
			</div>

			<!-- Controle do Carousel -->
			<a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev" onclick="trocaCena('-')">
				<span class="carousel-control-prev-icon bg-dark rounded" aria-hidden="true"></span>
				<span class="sr-only">Anterior</span>
			</a>
			<a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next" onclick="trocaCena('+')">
				<span class="carousel-control-next-icon bg-dark rounded" aria-hidden="true"></span>
				<span class="sr-only">Próximo</span>
			</a>
	</div>	


	<section class="d-flex justify-content-center align-items-center flex-wrap p-5">
		<h3 class="font-weight-bold">Nosso Diferencial!</h3>
		<div class="container-fluid">
			<div class="row">
				<!-- Inicio da Primeira Coluna -->
				<div class="col-md-4 text-center">
					<div class="card bg-transparent p-2 d-inline-block">
						<img src="img/design.png" class="card-img-top img-fluid rounded" width="100" height="100">
						<div class="card-body">
							<details class="d-inline-block">
								<summary class="mb-2">
									<strong>Experimente nosso rercuso 3D.</strong>
								</summary>
								<p class="w-50 m-auto">
									Nossa inovadora ferramenta 3D online permite que você visualize e interaja com os tênis diretamente do seu dispositivo. Experimente diferentes ângulos e estilos em tempo real, proporcionando uma experiência de compra mais imersiva e informada.
								</p>
							</details>	
						</div>
					</div>
				</div>
				<!-- Fim da Primeira Coluna -->
				<!-- Inicio da Segunda Coluna -->
				<div class="col-md-4 text-center">
					<div class="card bg-transparent p-2 mt-2 d-inline-block">
						<img src="img/design2.jpg" class="card-img-top img-fluid rounded">
						<div class="card-body">
							<details class="d-inline-block">
								<summary class="mb-2">
									<strong>Edição Limitada e Exclusiva</strong>
								</summary>
								<p class="w-50 m-auto">
									Nossa loja se orgulha de oferecer edições limitadas e exclusivas de sneakers que você não encontrará em nenhum outro lugar. Trabalhamos diretamente com os principais fabricantes e designers para trazer peças únicas que destacam seu estilo e exclusividade.
								</p>
							</details>	
						</div>
					</div>
				</div>
				<!-- Fim da Segunda Coluna -->
				<!-- Inicio da Terceira Coluna -->
				<div class="col-md-4 text-center">
					<div class="card bg-transparent p-2 mt-2 d-inline-block">
						<img src="img/design3.jpg" class="card-img-top img-fluid rounded" width="100" height="100">
						<div class="card-body">
							<details class="d-inline-block">
								<summary class="mb-2">
									<strong>Entrega Rápida e Segura</strong>
								</summary>
								<p class="w-50 m-auto">
									Oferecemos um serviço de entrega rápida e segura, garantindo que seus sneakers cheguem até você no menor tempo possível. Utilizamos as melhores transportadoras e oferecemos opções de rastreamento em tempo real para que você possa acompanhar seu pedido desde a compra até a entrega.
								</p>
							</details>	
						</div>
					</div>
				</div>
				<!-- Fim da Terceira Coluna -->
			</div>
		</div>
	</section>
	<!-- Iniciando o Corpo do Site-->
	<main class="p-1">
		<!-- Cartões -->
		<section class="text-center w-100 mt-3">
			<h3 class="font-weight-bold">Os melhores em um só lugar</h3>
			<p class="w-50 m-auto">
				A marca Jordan na AeronFusion é a escolha certa para os amantes de sneakers que buscam estilo e conforto.
			</p>
		</section>
		<section class="d-flex justify-content-center align-items-center flex-wrap">
			<?php require_once('script/imprimeProdutos.php'); ?>
		</section>
	</main>
	<!-- Rodapé -->
	<?php require_once('script/rodape.php'); ?>
	<!-- Adicionando o Scripts ao Site -->
	<?php require_once('script/scripts.php'); ?>
	<?php require_once('script/3dRequest.php'); ?>
	<!-- Script Obrigatório -->
	<script type="text/javascript" src="js/index_3d.js"></script>
</body>
</html>