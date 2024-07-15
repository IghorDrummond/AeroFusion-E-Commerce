<?php require_once ('script/valida_acesso_home.php'); ?>
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
			<nav id="nav_conteudo" class="p-1 h-50">
				<a class="navbar-brand mx-1" href="#">
					Seu Painel
					<img src="img/novo_usuario.png" class="rounded-circle border img-fluid" width="50" height="50"> 
				</a>
				<ul class="navegacao_lista">
					<li>
						<a class="nav-link" >Pedidos</a>
					</li>
					<li>
						<a class="nav-link" >Favoritos</a>
					</li>
					<li>
						<a class="nav-link" >Protocolos</a>
					</li>
					<li>
						<a class="nav-link" >Carrinho</a>
					</li>
					<li>
						<a class="nav-link" >Configuração</a>
					</li>
				</ul>
			</nav>
		</header>
		<!-- Inicio dos conteúdos -->
		<section class="container-fluid" data-bs-spy="scroll" data-bs-target="#nav_conteudo" data-bs-offset="0" tabindex="0">
			<h4 id="pedidos">Pedidos</h4>
			<article class="mt-2 bg-warning rounded p-1">
				<div class="container-fluid">
					<div class="row">
						<div class="col-lg-6">
							<h1 class="d-inline">ID DO PEDIDO</h1>
							<h3 class="d-inline">Status do Pedido</h3>
							<br>
							<time>Data Pedido</time>
							<h3>Forma de pagamento</h3>
							<h3>valor total</h3>
						</div>
						<div class="col-lg-6">
							dados de cada item do pedido
						</div>
					</div>
				</div>
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