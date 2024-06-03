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

<body >
	<?php require_once ('script/cabecalho.php'); ?>
	<main>
		<header>
			<nav id="nav_conteudo" class="navbar navbar-light bg-light">
				<a class="navbar-brand" href="#">
					Seu Painel
					<img src="img/novo_usuario.png" class="rounded-circle border img-fluid" width="50" height="50"> 
				</a>
				<ul class="nav nav-pills">
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
						<a class="nav-link" href="#configuracao">Configuração</a>
					</li>
				</ul>
			</nav>
		</header>
		<!-- Inicio dos conteúdos -->
		<section class="container-fluid" data-spy="scroll" data-target="#nav_conteudo" data-offset="0">
			<h4 id="pedidos">Pedidos</h4>
			<article class="mt-2 bg-warning rounded">
				Pedidos
			</article>
			<h4 id="favoritos">Favoritos</h4>
			<article class="mt-2 bg-warning rounded">
				Favoritos
			</article>
			<h4 id="protocolos">Protocolos</h4>
			<article class="mt-2 bg-warning rounded">
				Protocolos
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