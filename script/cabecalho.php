<?php
//Importar bibliotecas  
require_once ('script/lib/categoria.php');
//$_SESSION['Login'] = false;
use Categoria\TelaCategoria;

//Numerico
$nCont = 0;
//String
$categoriaHtml = '';
//Objetos
$categoria = null;
//Array
$listaCategoria = [];
//Boolean
$logado = false;

//--------Escopo
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//Busca Categorias Novas
$categoria = new TelaCategoria();
$listaCategoria = $categoria->retornarValores();//Retorna oque foi encontrado na querys
$categoria->__destruct();//Destrói o objeto após seu uso

//Carrega Tags Html com as Categorias existentes
foreach ($listaCategoria as $cat) {
	$categoriaHtml .= "<a onclick='selecionaCategoria(" . '"' . $cat['nome_cat'] . '"' . ")' class='text-warning p-1'>" . $cat['nome_cat'] . "</a>" . PHP_EOL;
}

if(isset($_SESSION['Login']) and $_SESSION['Login']){
	$logado = true;
}
?>
<!-- Menu para Mobiles -->
<div class="collapse d-lg-none text-center border rounded p-1" id="navegacao">
	<!--Navegação -->
	<ul class="navbar-nav ml-auto text-warning">
		<li class="nav-item text-center">
			<?php
				if ($logado) {
			?>
					<img src="img/<?php echo ($_SESSION['Foto']); ?>" class="rounded-circle border border-dark img-fluid mr-2"
						title="Usuário: <?php echo ($_SESSION['Nome']); ?>" width="30" heigth="30">
					<h6 class="mt-2">Olá, <?php echo ($_SESSION['Nome']); ?>!</h6>
			<?php
				} else {
			?>
					<a class="nav-link text-warning" href="login.php">Entrar</a>
			<?php
				}
			?>
			<hr>
		</li>
		<li class="nav-item">
			<a class="text-warning" href="index.php">Inicio</a>
			<hr>
		</li>
		<li class="nav-item">
			<a class="nav-link text-warning" onclick="ligaCab()">Categorias</a>
			<hr>
		</li>
		<li class="nav-item">
		</li>
		<?php
		if ($logado) {
			?>
			<li class="nav-item d-flex flex-column">
				<button class="text-warning" onclick="solicitacao(0)">Pedidos</button>
				<hr>
				<button class="text-warning" onclick="solicitacao(1)">Protocolos/Garantia</button>
				<hr>
				<button class="text-warning" onclick="solicitacao(2)">Favoritos</button>
				<hr>
				<button class="text-warning" onclick="solicitacao(3)">Configuração</button>
				<hr>
				<button class="text-warning" onclick="solicitacao(4)">
					Sair <i class="fa-solid fa-right-to-bracket"></i>
				</button>
				<hr>
			</li>
			<?php
		}
		?>
	</ul>
</div>
<!-- Fim do Menu para mobiles -->

<!-- Tela de Carregamento -->
<div id="tela_carregamento" class="d-none flex-column justify-content-center align-items-center">
	<div class="custom-loader"></div>
</div>

<!-- Alertas -->
<div class="alertas p-2 alert-warning d-none" role="alert">
	<strong>Ops!</strong>&nbsp;<span class="alerta_texto"></span>
	<button type="button" class="close" onclick="fecharAlerta(0)" data-dismiss="alert" aria-label="Close">
		<span aria-hidden="true">&times;</span>
	</button>
</div>
<div class="alertas p-2 alert-success d-none" role="alert">
	<strong>Sucesso!</strong>&nbsp;<span class="alerta_texto"></span>
	<button type="button" class="close" onclick="fecharAlerta(1)" data-dismiss="alert" aria-label="Close">
		<span aria-hidden="true">&times;</span>
	</button>
</div>

<!-- Inicio do Cabeçalho -->
<header class="sticky-top bg-dark">
	<div class="d-flex justify-content-center align-items-center flex-lg-row flex-column">
		<!-- Navegãção do Cabeçalho -->
		<nav class="navbar-nav navbar-expand-lg align-items-center navbar-dark p-2 w-100">

			<!-- Logo para Desktop -->
			<a class="navbar-brand d-lg-block d-none" title="AeroFusion" href="index.php">
				<img src="img/logo.png" class="mx-4 img-fluid" width="60">
			</a>
			<!-- Opções da navegação para Desktop -->
			<ul class="navbar-nav ml-auto d-lg-flex d-none">
				<li class="nav-item">
					<a class="nav-link" href="index.php">Inicio</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" onmouseover="ligaCab()" onclick="ligaCab()">Categorias</a>
				</li>
				<li class="nav-item">
				</li>
				<li class="d-flex justify-content-center align-items-center mr-2 ">
					<i class="fa-solid fa-cart-shopping fa-lg" style="color: orange;" onclick="abreCarrinho(0)"></i>
					<span class="d-block badge badge-primary mt-1">0</span>
					<dialog id="carrinho" class="rounded border border-warning">
						<i onclick="fecharCarrinho()" class="fa-solid fa-x w-100 border border-warning rounded text-center"></i>
						<?php
							if(!$logado){
						?>
							<h6 class="text-center text-warning">Faça um login para adicionar produtos ao carrinho</h6>
						<?php
							}
						?>
					</dialog>
				</li>
				<li class="nav-item d-flex justify-content-center align-items-center ">
					<?php
						if($logado) {
					?>
							<img id="informacoes" src="img/<?php echo ($_SESSION['Foto']); ?>"
								class="rounded-circle border border-dark img-fluid mr-2"
								title="Usuário: <?php echo ($_SESSION['Nome']); ?>" onclick="abreConfig()" width="30" heigth="30">
							<dialog id="configuracao" class="rounded border border-warning">
								<button onclick="fecharConfig()">
									<i class="fa-solid fa-x"></i>
								</button>
								<h6><?php echo ($_SESSION['Nome']); ?></h6>
								<button class="text-warning active" onclick="solicitacao(0)">Pedidos</button>
								<button class="text-warning" onclick="solicitacao(1)">Favoritos</button>
								<button class="text-warning" onclick="solicitacao(2)">Protocolos/Garantia</button>
								<button class="text-warning" onclick="solicitacao(3)">Configuração</button>
								<button class="text-warning" onclick="solicitacao(4)">
									Sair <i class="fa-solid fa-right-to-bracket"></i>
								</button>
							</dialog>
					<?php
						} else {
					?>
						<a class="nav-link" href="login.php">Entrar</a>
					<?php
						}
					?>
				</li>
			</ul>

			<!-- Navegação para Dispositivos Moveis -->
			<div class="d-flex d-lg-none justify-content-between align-items-center w-100">
				<div>
					<!-- Carrinho -->
					<i class="fa-solid fa-cart-shopping fa-lg" style="color: orange;" onclick="abreCarrinho(1)"></i>
					<span class="d-block badge badge-primary">0</span>
				</div>
				<a class="navbar-brand d-lg-none d-block m-auto" href="index.php">
					<img src="img/logo.png" class="my-2 img-fluid" width="55">
				</a>
				<button class="navbar-toggler h-50" data-toggle="collapse" data-target="#navegacao">
					<i class="fa-solid fa-bars fa-md"></i>
				</button>
			</div>
		</nav>
		<!-- Fim da Navegação do Cabeçalho -->
		<!-- Barra de Pesquisa -->
		<form class="form-inline float-lg-right d-inline-block mt-2" onsubmit="pesquisar()">
			<input id="Pesquisa" type="text" class="w-75" placeholder="Pesquisar..." required>
			<button type="submit" class="btn btn-outline-light" onclick="pesquisaProduto()">
				<i class="fa-solid fa-magnifying-glass"></i>
			</button>
		</form>
		<dialog id="caixaPesq" class="rounded border border-warning">
			Esperando sua Pesquisa...
		</dialog>
	</div>

	<!-- Campo de Novidades -->
	<div id="carouselExampleSlidesOnly" class="carousel slide" data-ride="carousel">
		<div class="carousel-inner">
			<div class="carousel-item active novidade-cabecalho">
				Frete Gratís para todo o páis.
			</div>
			<div class="carousel-item novidade-cabecalho">
				50% de Desconto em peças únicas.
			</div>
			<div class="carousel-item novidade-cabecalho">
				Compre agora tênis para o inverno.
			</div>
		</div>
	</div>

	<!-- Categorias -->
	<div class="navegacao p-3 w-100 d-none flex-column flex-lg-row bg-white font-weight-bold text-warning justify-content-between align-items-center"
		onmousedown="desligaCab()">
		<h5 class="ml-auto text-danger d-lg-none d-block" onclick="desligaCab()"><span
				class="badge badge-transparent border">X</span></h5>
		<?php echo ($categoriaHtml); ?>
	</div>
</header>