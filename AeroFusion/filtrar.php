<?php
	// Inicia a sessão se ainda não estiver iniciada
	if (session_status() == PHP_SESSION_NONE) {
		session_start();
	}
	//Bibliotecas
	require_once('lib/produtos.php');
	use Produto\Filtrar;
	use Produto\Favoritos;	
	//Declaração de variaveis
	//String
	$Produto = isset($_GET['Pesq']) ? $_GET['Pesq'] : '';
	$Categoria = isset($_GET['categoria']) ? $_GET['categoria'] : '';
	$Preco = isset($_GET['preco']) ? $_GET['preco'] : '';
	$Data = isset($_GET['data']) ? $_GET['data'] : '';
	$Tamanho = isset($_GET['tamanho']) ? $_GET['tamanho'] : '';
	//Array
	$Produtos = [];
    //Ojeto
    $Filtros = null;
	$Favoritos = null;
    //--------Escopo
    $Filtro = new Filtrar($Preco, $Data, $Categoria, $Tamanho, $Produto);
    $Produtos = $Filtro->retornaValores();
	//Valida se está favorito os produtos
	if(isset($_SESSION['Login']) and $_SESSION['Login']){
		$Favoritos = new Favoritos(Email: $_SESSION['Email']);
	}	
    catalogaProdutos($Produtos, $Favoritos);
	//----------Funções
	function catalogaProdutos($Produtos, $Favoritos){
		if(isset($Produtos[0]['Produto'])){
			foreach ($Produtos as $nCont2 => $Prod) {
?>
	<div 
		class="produto" 
		onmouseover="passaImagens(this)" 
		onmouseleave="paraImagens(this)"
		style="cursor: pointer;" 
	>
		<div
			onclick="maisDetalhes(<?php echo(strval($Prod['IdProd'])) ?>)"
		>
			<img src="img/<?php echo($Prod['img1']); ?>" class="img-fluid">

			<!-- Inicio do carousel -->
			<div class="d-none carousel slide" id="img-prod" data-ride="carousel" data-interval="1000" data-pause="false">
				<div class="carousel-inner">
					<!-- Imagens do carousel -->
					<?php 
						for($i = 1; $i <= 5; $i++){
							if($Prod['img' . strval($i)] === ''){
								continue;
							}
					?>	
					<div class="carousel-item <?php print($i === 2 ? "active" : "") ?>">
						<img src="img/<?php echo($Prod['img' . strval($i)]); ?>" class="d-block w-100 img-fluid">
					</div>
					<?php
						}
					?>
				</div>
			</div>
			<h6 class="font-weight-bold"><?php print(ucfirst(strtolower($Prod['Produto']))) ?></h6>
		</div>
		<span class="d-inline-block text-info"><?php print(ucfirst($Prod['Categoria'])) ?></span>
		<span class="d-inline-block w-50 text-right" onclick="favorito(this, <?php echo(strval($Prod['IdProd'])) ?> )">
				<!-- Valida se usuário está logado -->
				<?php
					$Ret = false;

					if(!is_null($Favoritos)){
						$Ret = $Favoritos->retornaValores($Produtos[$nCont2]['IdProd']);
					}
					//Valida se usuário tem o produto favoritado
					$Class = $Ret ? 'fa-solid fa-star' : 'fa-regular fa-star';
				?>
				<i class="<?php echo($Class); ?>"></i>
		</span>
		<span class="d-block mt-1">R$ <?php echo(strval($Prod['Preco'])) ?></span>
	</div>
<?php
			}
		}
		else{
			echo('<h1 class="text-center">Não foi encontrado!</h1>');
		}
	}
?>

<!-- Botões -->
<div class="btn-group mt-auto flex-fill w-100 p-1" role="group" aria-label="Basic example">
	<button class="btn btn-primary "><|</button>
	<button class="btn btn-primary">1</button>
	<button class="btn btn-primary">2</button>
	<!-- Ajustar o tamanho facilmente  - LIMITE DE 12 POR PAGINA -->
	<button class="btn btn-primary">|></button>
</div>