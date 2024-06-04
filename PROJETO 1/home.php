<?php require_once ('script/valida_acesso.php'); ?>
<!DOCTYPE html>
<html>

<head>
	<?php require_once ('script/style.php'); ?>
	<!-- Estilo da Página -->
	<link rel="stylesheet" type="text/css" href="css/home.css">
	<!-- Titulo da página -->
	<title>AeroFusion - Home</title>
</head>

<body>
	<?php require_once ('script/cabecalho.php'); ?>
	<main class="d-flex flex-lg-row flex-column">
		<header>
			<nav id="nav_conteudo" class="navbar h-50">
				<a class="navbar-brand" href="#">
					Seu Painel
					<img src="img/novo_usuario.png" class="rounded-circle border img-fluid" width="50" height="50"> 
				</a>
				<ul class="nav nav-pills flex-lg-column flex-row">
					<li class="nav-item">
						<a class="nav-link active" href="#pedidos">Pedidos</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#favoritos">Favoritos</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#protocolos">Protocolos</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#carrinho">Carrinho</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#configuracao">Configuração</a>
					</li>
				</ul>
			</nav>
		</header>
		<!-- Inicio dos conteúdos -->
		<section class="container-fluid" data-bs-spy="scroll" data-bs-target="#nav_conteudo" data-bs-offset="0" tabindex="0">
			<h4 id="pedidos">Pedidos</h4>
			<article class="mt-2 bg-warning rounded p-1">
				<ul>
					<li class="d-flex flex-lg-row flex-column">
						<div class="border w-100 text-center">
							<h1>#IDdoPedido</h1>
							<time>Data do Pedido</time>
							<h6>Status do pedido</h6>
							<h6>Valor total</h6>
						</div>
						<div class="w-100">
							<h6>Nome do Produto</h6>
							<img src="">Imagem do produto
							<h6>quant</h6>
						</div>
					</li>
				</ul>
			</article>
			<h4 id="favoritos">Favoritos</h4>
			<article class="mt-2 bg-warning rounded">
				<ul>
					<li>
						
					</li>
				</ul>
			</article>
			<h4 id="protocolos">Protocolos</h4>
			<article class="mt-2 bg-warning rounded">
				Protocolos
			</article>
			<h4 id="carrinho">Carrinho</h4>
			<article class="mt-2 bg-warning rounded">
				Carrinho
			</article>
			<h4 id="configuracao">Configuração</h4>
			<article class="mt-2 bg-warning rounded">
				Configuração
			</article>
		</section>
	</main>
	<?php require_once ('script/rodape.php'); ?>
	<?php require_once ('script/scripts.php'); ?>
</body>

</html>