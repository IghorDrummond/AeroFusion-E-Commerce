<!DOCTYPE html>
<html>
	<head>
        <?php require_once ('script/style.php'); ?>
        <!-- Titulo da Página -->
        <title>AeroFusion - Error</title>

        <style>
            #Quadro{
                width: 100vw;
                height: 100vh;
                position: absolute;
                z-index: 0;
            }
            .Letreiro{
                position: absolute;
                width: 100vw;
                height: 100vh;
                z-index: 1;
            }
        </style>
	</head>
	<body>
        <div id="Quadro"></div>
        <div class="Letreiro text-center d-flex flex-column justify-content-start">
            <h1 class="text-white font-weight-bold">Error <?php echo($_GET['Error']); ?></h1>
            <p class="text-white">
                Ocorreu um erro com a aplicação, nossos desenvolvedores foram avisados!
            </p>
            <a class="btn btn-secondary fixed-bottom" href="index.php">Voltar para Inicio</a>
        </div>
		<?php require_once('script/3dRequest.php'); ?>
	    <script type="module"  src="js/error_3d.js"></script>
	</body>
</html>