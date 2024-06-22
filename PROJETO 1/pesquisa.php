<!DOCTYPE html>
<html>
	<head>
		<?php require_once('script/style.php'); ?>
		<!-- Estilo da Página -->
		<link rel="stylesheet" type="text/css" href="css/buscador.css">
		<!-- Titulo da página -->
		<title>AeroFusion - Produtos</title>
	</head>
	<body>
		<?php require_once('script/cabecalho.php'); ?>
		<main>
			<section class="bg-warning p-5">
				<h4>Filtrar:</h4>
				<ul class="text-left">
					<li>
						Valores:<br>
						<input type="checkbox" name="preco" value="Alto">Preço Alto
						<br>
						<input type="checkbox" name="preco" value="Baixo">Preço Baixo
					</li>
					<li>
						Ultimas Promoções:<br>
						<input type="checkbox" name="promocao" value="true">Promoções
					</li>		
					<li>
						Data de Lançamento:<br>
						<input type="checkbox" name="data" value="Recente">Recente
						<br>
						<input type="checkbox" name="data" value="Antigo">Antigo
					</li>
					<li>
						Categoria:<br>
						<input type="checkbox" name="categoria" value="Feminino">Feminino
						<br>
						<input type="checkbox" name="categoria" value="Masculino">Masculino
						<br>
						<input type="checkbox" name="categoria" value="Infantil">Infantil
					</li>	
					<li>
						Tamanhos:<br>
						<input type="checkbox" name="tamanho" value="1">34
						<input type="checkbox" name="tamanho" value="2">35
						<input type="checkbox" name="tamanho" value="3">36
						<input type="checkbox" name="tamanho" value="4">37
						<input type="checkbox" name="tamanho" value="5">38
						<input type="checkbox" name="tamanho" value="6">39
						<input type="checkbox" name="tamanho" value="7">40
						<input type="checkbox" name="tamanho" value="8">41
						<input type="checkbox" name="tamanho" value="9">42
						<input type="checkbox" name="tamanho" value="10">43
						<input type="checkbox" name="tamanho" value="11">44
					</li>				
				</ul>
				<button onclick="filtrar()" class="btn btn-primary">Filtrar</button>
			</section>
			<section id="exibeProdutos">
				<?php require_once('script/filtrar.php'); ?>
			</section>
		</main>
		<?php require_once('script/rodape.php'); ?>
		<?php require_once('script/scripts.php'); ?>
		<script type="text/javascript" src="js/buscador.js"></script>
	</body>
</html>