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
		<?php require_once('script/home_config.php'); ?>
	</main>
	<?php require_once ('script/rodape.php'); ?>
	<?php require_once ('script/scripts.php'); ?>
	<script type="text/javascript" src="js/home.js"></script>
</body>
</html>