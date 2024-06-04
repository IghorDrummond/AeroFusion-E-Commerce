<?php require_once('script/valida_sessao.php'); ?>
<!DOCTYPE html>
<html>
<head>
	<?php require_once ('script/style.php'); ?>
	<!-- Estilo da Página -->
	<link rel="stylesheet" type="text/css" href="css/login.css">
	<!-- Titulo da Página -->
	<title>AeroFusion - Login</title>
</head>
<body onresize="ajustaTam()">
	<?php require_once('script/cabecalho.php'); ?>
	<!-- Iniciando o Corpo do Site-->
	<main class="w-100">
		<section id="tela_operacao">
			<div class="container-fluid">
				<div class="row">
					<div id="tela_info" class="col-md-6 d-none d-lg-flex p-5 flex-column justify-content-center align-items-center">
						<h1 class="text-center text-info">
							Pronto para dar aquele passo sneakerístico? Desate seus cadarços e venha conosco nessa jornada de estilo e conforto! 👟✨
						</h1>
					</div>
					<div id="area_dados" class="col-md-6 bg-warning p-5 text-white font-weight-bold d-flex flex-column justify-content-center align-items-center">
						<div class="w-100">
							<label for="Email">Email:</label>
							<input type="email" class="form-control" name="Email" placeholder="seuemail@email.com" required>
						</div>
						<div class="w-100">
							<label for="Senha">Senha:</label>
							<input type="password" class="form-control" name="Senha" pattern="(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*\W+)(?=^.{8,50}$).*$" required>
						</div>
						<div class="w-100">
							<br>
							<button class="btn btn-primary btn-lg btn-block" onclick="Logar()">Entrar</button>
							<br>
						</div>
						<div class="w-100 text-center">
							<label>Com dificuldade para logar? Tente isso:</label>
							<br>
							<button class="btn btn-primary" onclick="cadastrar()">Cadastrar</button>
							<button class="btn btn-primary" onclick="trocaSenha()">Esqueci a Senha</button>
						</div>
					</div>
				</div>
			</div>
		</section>
	</main>
	<!-- Rodapé -->

	<?php require_once('script/rodape.php'); ?>
	<!-- Adicionando o Scripts ao Site -->
	<?php require_once ('script/scripts.php'); ?>
	<!-- Script Obrigatório -->
	<script type="text/javascript" src="js/conta.js"></script>
	<script type="text/javascript" src="js/senha.js"></script>
</body>
</html>