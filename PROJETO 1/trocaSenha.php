<?php require_once('script/valida_acesso_senha.php'); ?>
<!DOCTYPE html>
<html>
	<head>
		<?php require_once('script/style.php'); ?>
		<!-- Estilo da Página -->
		<link rel="stylesheet" type="text/css" href="css/trocaSenha.css">
		<!-- Titulo da página -->
		<title>AeroFusion - Renova sua Senha</title>
	</head>
	<body>
		<?php require_once('script/cabecalho.php'); ?>
		<main>
			<section id="sessao1">
				<svg id="blob1" viewBox="0 0 480 480" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" >
					<path fill="#ffa847">
						<animate attributeName="d" dur="6000ms" repeatCount="indefinite"
							values="M421.5,299Q402,358,353,398Q304,438,238,443.5Q172,449,120,407Q68,365,43.5,302.5Q19,240,50.5,182.5Q82,125,130,87.5Q178,50,244.5,36.5Q311,23,365,66.5Q419,110,430,175Q441,240,421.5,299Z;M407.5,288.5Q374,337,333.5,369.5Q293,402,233.5,422Q174,442,112.5,409.5Q51,377,65.5,308.5Q80,240,84.5,185Q89,130,135.5,96.5Q182,63,237.5,69.5Q293,76,358,91.5Q423,107,432,173.5Q441,240,407.5,288.5Z;M386,291.5Q382,343,335,364.5Q288,386,237.5,395Q187,404,159.5,361.5Q132,319,82,279.5Q32,240,58.5,183.5Q85,127,132,89.5Q179,52,234.5,69Q290,86,325,119.5Q360,153,375,196.5Q390,240,386,291.5Z;M421.5,299Q402,358,353,398Q304,438,238,443.5Q172,449,120,407Q68,365,43.5,302.5Q19,240,50.5,182.5Q82,125,130,87.5Q178,50,244.5,36.5Q311,23,365,66.5Q419,110,430,175Q441,240,421.5,299Z">
						</animate>
					</path>
				</svg>
				<svg id="blob2" viewBox="0 0 480 480" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" >
					<path fill="#ffa847">
						<animate attributeName="d" dur="6000ms" repeatCount="indefinite"
							values="M421.5,299Q402,358,353,398Q304,438,238,443.5Q172,449,120,407Q68,365,43.5,302.5Q19,240,50.5,182.5Q82,125,130,87.5Q178,50,244.5,36.5Q311,23,365,66.5Q419,110,430,175Q441,240,421.5,299Z;M407.5,288.5Q374,337,333.5,369.5Q293,402,233.5,422Q174,442,112.5,409.5Q51,377,65.5,308.5Q80,240,84.5,185Q89,130,135.5,96.5Q182,63,237.5,69.5Q293,76,358,91.5Q423,107,432,173.5Q441,240,407.5,288.5Z;M386,291.5Q382,343,335,364.5Q288,386,237.5,395Q187,404,159.5,361.5Q132,319,82,279.5Q32,240,58.5,183.5Q85,127,132,89.5Q179,52,234.5,69Q290,86,325,119.5Q360,153,375,196.5Q390,240,386,291.5Z;M421.5,299Q402,358,353,398Q304,438,238,443.5Q172,449,120,407Q68,365,43.5,302.5Q19,240,50.5,182.5Q82,125,130,87.5Q178,50,244.5,36.5Q311,23,365,66.5Q419,110,430,175Q441,240,421.5,299Z">
						</animate>
					</path>
				</svg>
				<svg id="blob3" viewBox="0 0 480 480" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" >
					<path fill="#ffa847">
						<animate attributeName="d" dur="6000ms" repeatCount="indefinite"
							values="M421.5,299Q402,358,353,398Q304,438,238,443.5Q172,449,120,407Q68,365,43.5,302.5Q19,240,50.5,182.5Q82,125,130,87.5Q178,50,244.5,36.5Q311,23,365,66.5Q419,110,430,175Q441,240,421.5,299Z;M407.5,288.5Q374,337,333.5,369.5Q293,402,233.5,422Q174,442,112.5,409.5Q51,377,65.5,308.5Q80,240,84.5,185Q89,130,135.5,96.5Q182,63,237.5,69.5Q293,76,358,91.5Q423,107,432,173.5Q441,240,407.5,288.5Z;M386,291.5Q382,343,335,364.5Q288,386,237.5,395Q187,404,159.5,361.5Q132,319,82,279.5Q32,240,58.5,183.5Q85,127,132,89.5Q179,52,234.5,69Q290,86,325,119.5Q360,153,375,196.5Q390,240,386,291.5Z;M421.5,299Q402,358,353,398Q304,438,238,443.5Q172,449,120,407Q68,365,43.5,302.5Q19,240,50.5,182.5Q82,125,130,87.5Q178,50,244.5,36.5Q311,23,365,66.5Q419,110,430,175Q441,240,421.5,299Z">
						</animate>
					</path>
				</svg>
			</section>
			<section id="sessao2">
				<div class="p-2 d-flex justify-content-center align-items-center rounded">
					<form class="form-group " onsubmit="trocarSenha()">
						<fieldset class="form-group">
							<legend>Mude sua senha</legend>
							<label for="senha">Insira sua senha:</label>
							<input type="password" name="Senha" class="form-control" onkeyup="senhaValid()" maxlength="12" pattern="(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*\W+)(?=^.{8,50}$).*$" required>
							<label for="senha">Confirme sua senha:</label>
							<input type="password" name="ConfirmeSenha" class="form-control" maxlength="12"  pattern="(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*\W+)(?=^.{8,50}$).*$" required>
						</fieldset>
						<fieldset class="text-center">
							<ul id="confereSenha">
								<li>senha com tamanho de 8 caracteres</li>
								<li>um ou mais letra maiúscula</li>
								<li>um ou mais numeros</li>
								<li>um ou mais Símbolo</li>
							</ul>
							<input type="submit" value="Enviar"class="btn btn-primary">							
						</fieldset>
					</form>
				</div>
			</section>
		</main>
		<?php require_once('script/rodape.php'); ?>
		<?php require_once('script/scripts.php'); ?>
		<script type="text/javascript" src="js/trocaSenha.js"></script>
		<script type="text/javascript" src="js/senha.js"></script>
	</body>
</html>