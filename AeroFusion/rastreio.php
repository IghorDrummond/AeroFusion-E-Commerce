<?php
	//sessão
	session_start();
	//Blibioteca 
	require_once('lib/configuracao.php');
	use Pagamentos\Rastreio;
	//Declaração de variaveis
	//String
	$Pedido = isset($_GET['pedido']) ? $_GET['pedido'] : die('Pedido Não informado');
	//Array
	$status_rastreio = [];
	//Objeto
	$rastreio = null;

	//recebe a classe rastreio
	$rastreio = new Rastreio(Email: $_SESSION['Email']);
?>
<div class="w-100 d-flex p-3 justify-content-center align-items-center end_body">
	<div class="bg-white rounded end_dados">
		<div class="sticky-top p-3 bg-white d-flex">
			<h3>Acompanhe seu pedido</h3>
			<button onclick="fecharEnd()" class="btn btn-danger text-white font-weight-bold rounded p-1 d-block ml-auto">
				Fechar
			</button>
		</div>
		<ul class="text-dark font-weight-bold list-unstyled p-2">
			<?php 
				foreach($rastreio->getRastreio($Pedido) as $posic => $ras){
			?>
			<hr>
			Atualização desse status: <time><?php echo($ras['data_rastreio']) ?></time>
			<li class="d-flex flex-wrap align-items-center">
				<div class="bg-warning rounded-circle p-2 text-white mx-1">
					<i class="fa-solid <?php echo(mb_convert_case($ras['icone_status'], MB_CASE_FOLD, 'UTF-8')); ?>"></i>
				</div>
				<?php echo( ucfirst(mb_convert_case($ras['titulo_ras'], MB_CASE_FOLD, 'UTF-8'))); ?>:
			</li>
			<li class="p-3">
				<?php echo( ucfirst(mb_convert_case($ras['descricao_ras'], MB_CASE_FOLD, 'UTF-8'))); ?>
			</li>
			<hr>
			<?php 
				}
			?>
		</ul>
	</div>
</div>