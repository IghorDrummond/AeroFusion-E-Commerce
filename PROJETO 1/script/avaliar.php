<?php
    session_start();
    require_once('lib/compras.php');
    use Pedido\novoPedido;

    $pedido = new novoPedido();

    $_SESSION['pedido'] = $_GET['pedido'];
?>
<div class="end_body p-3 d-flex justify-content-center align-items-center">
    <div class="end_dados p-1 rounded bg-white">
        <form id="avaliacao" class="form-group" method="POST" enctype="multipart/form-data">
            <!-- Selecionar o produto a ser avaliado -->
            <fieldset class="d-flex w-100 sticky-top">
                <div class="dropdown w-100">
                    <a class="btn btn-white border border-secondary rounded dropdown-toggle d-block w-100" href="#" role="button" data-toggle="dropdown" aria-expanded="false">
                        Selecione um produto
                    </a>
                    <!-- Carrega produtos a ser avaliados -->
                    <div class="dropdown-menu w-100">
                        <?php
                            foreach ($pedido->getPedido($_GET['pedido'] , $_SESSION['Email']) as $Prod) {
                        ?>
                           <div class="d-flex align-items-center flex-wrap produtos" data-toggle="<?php echo($Prod['id_prod']) ?>">
                                <img src="img/<?php echo($Prod['img1']) ?>" class="img-fluid rounded border border-secondary p-1 m-2" width="100" height="100">
                                <h6><?php echo mb_convert_case($Prod['nome'] , MB_CASE_TITLE, 'UTF-8') ?></h6>
                            </div>
                        <?php
                            }
                        ?>
                    </div>
                </div>
                <button type="button" onclick="fecharAba()" class="d-block btn btn-danger mx-1">x</button>
            </fieldset>
            <!-- Avaliar com estrelas -->
            <fieldset class="text-center">
                <h1 class="text-warning">Avaliar</h1>
                <div class="text-warning" style="cursor: pointer;">
                    <i class="fa-solid fa-star stars"></i>
                    <i class="fa-regular fa-star stars"></i>
                    <i class="fa-regular fa-star stars"></i>
                    <i class="fa-regular fa-star stars"></i>
                    <i class="fa-regular fa-star stars"></i>
                </div>
            </fieldset>
            <!-- Titulo da avaliação -->
            <fieldset class="form-group">
                <label class="font-weight-bold" for="titulo">Título:</label>
                <input type="text" name="titulo" class="form-control font-weight-bold"
                    placeholder="Insira o título da avaliação" required>
            </fieldset>
            <!-- mensagem da avaliação -->
            <fieldset class="form-group">
                <label class="font-weight-bold" for="descricao">Descrição:</label>
                <textarea class="form-control descricao" maxlength="1000" name="descricao" rows="5"
                    placeholder="Insira sua descrição" required></textarea>
                <span id="quant-carac" class="text-warning">Restam 1000 caracteres disponíveis.</span>
            </fieldset>
            <!-- imagens da avaliação -->
            <fieldset class="form-group">
                <label class="font-weight-bold" for="imagens">Imagens:</label>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="inputGroupFileAddon01">Upload</span>
                    </div>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="inputGroupFile01"
                            aria-describedby="inputGroupFileAddon01" accept="image/*" multiple>
                        <label class="custom-file-label" for="inputGroupFile01">Choose file</label>
                    </div>
                </div>
                <div class="text-center py-2 d-flex justify-content-center align-items-center">
                    <div>
                        <img src="img/inserir_img.jpg" width="100" height="100"
                            class="img-fluid rounded border border-secondary p-1 m-2 imagens" name="imagem1">
                        <button data-toggle="0" type="button"
                            class="btn btn-danger p-1 rounded d-none deletar m-auto">deletar</button>
                    </div>
                    <div>
                        <img src="img/inserir_img.jpg" width="100" height="100"
                            class="img-fluid rounded border border-secondary p-1 m-2 imagens" name="imagem2">
                        <button data-toggle="1" type="button"
                            class="btn btn-danger p-1 rounded d-none deletar m-auto">deletar</button>
                    </div>
                    <div>
                        <img src="img/inserir_img.jpg" width="100" height="100"
                            class="img-fluid rounded border border-secondary p-1 m-2 imagens" name="imagem3">
                        <button data-toggle="2" type="button"
                            class="btn btn-danger p-1 rounded d-none deletar m-auto">deletar</button>
                    </div>
                </div>
                <ul class="list-unstyled text-warning">
                    <li>Imagem não pode ser maior que 500kb</li>
                    <li>Pode até 5 imagens</li>
                    <li>Apenas formatos JPG, JPGE, PNG e GIF</li>
                </ul>
            </fieldset>
            <!-- Enviar -->
            <div class="text-center">
                <button type="submit" class="btn btn-primary mt-3">Enviar</button>
            </div>
        </form>
    </div>
    <script type="text/javascript" src="js/avalicao.js"></script>
</div>