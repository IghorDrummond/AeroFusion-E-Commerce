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
		<?php require_once('script/home_config.php'); ?>
	</main>
	<?php require_once ('script/rodape.php'); ?>
	<?php require_once ('script/scripts.php'); ?>
	<script type="text/javascript" src="js/home.js"></script>
</body>
</html>