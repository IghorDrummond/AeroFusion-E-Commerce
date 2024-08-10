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
	$rastreio = Rastreio(Email: $_SESSION['Email']);
?>
<div class="w-100 d-flex p-3 justify-content-center align-items-center end_body">
	<div class="bg-white rounded p-2 end_dados">
		<ul class="text-dark font-weight-bold">
			<?php 
				foreach($rastreio->getRastreio($Pedido) as $posic => $ras){
			?>
			<li class="d-flex flex-wrap">
				<div class="bg-warning rounded-circle p-3">
					<i class="fa-solid <?php echo(mb_convert_case($ras['icone_status'], MB_CASE_FOLD, 'UTF-8')); ?>"></i>
				</div>
				<?php echo(mb_convert_case($ras['titulo_ras'], MB_CASE_FOLD, 'UTF-8')); ?>:
				<br>
				<?php echo(mb_convert_case($ras['descricao_ras'], MB_CASE_FOLD, 'UTF-8')); ?>
			</li>
			<?php 
				}
			?>
		</ul>
	</div>
	<button class="btn btn-danger text-white font-weight-bold rounded p-1 d-block m-auto">
		Fechar
	</button>
</div>