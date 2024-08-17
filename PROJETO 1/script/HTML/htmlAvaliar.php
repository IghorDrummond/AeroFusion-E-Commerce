<?php
$_SESSION['avaPed'] = $_GET['pedido'];
?>
<div class="end_body p-3 d-flex justify-content-center align-items-center">
    <div class="w-50 p-1 rounded bg-white">
        <form class="form-group" action="script/avaliacao.php" method="POST" enctype="multipart/form-data">
            <fieldset class="text-center">
                <h1 class="text-warning">Avaliar</h1>
                <div class="text-warning" style="cursor: pointer;">
                    <i class="fa-regular fa-star"></i>
                    <i class="fa-regular fa-star"></i>
                    <i class="fa-regular fa-star"></i>
                    <i class="fa-regular fa-star"></i>
                    <i class="fa-regular fa-star"></i>
                </div>
            </fieldset>
            <fieldset class="form-group">
                <label class="font-weight-bold" for="titulo">Título:</label>
                <input type="text" name="titulo" class="form-control" placeholder="Insira o título da avaliação"
                    required>
            </fieldset>
            <fieldset class="form-group">
                <label class="font-weight-bold" for="descricao">Descrição:</label>
                <textarea class="form-control descricao" maxlength="1000" name="descricao" rows="5" placeholder="Insira sua descrição"
                    required></textarea>
                    <span id="quant-carac" class="text-warning">Restam 1000 caracteres disponíveis.</span>
            </fieldset>
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
                        <img src="img/inserir_img.jpg" width="100" height="100" class="img-fluid rounded border border-secondary p-1 m-2 imagens" name="imagem1">          
                        <button data-toggle="0" type="button" class="btn btn-danger p-1 rounded d-none deletar m-auto">deletar</button>
                    </div>
                    <div>
                        <img src="img/inserir_img.jpg" width="100" height="100" class="img-fluid rounded border border-secondary p-1 m-2 imagens" name="imagem2">          
                        <button data-toggle="1" type="button" class="btn btn-danger p-1 rounded d-none deletar m-auto">deletar</button>
                    </div>
                    <div>
                        <img src="img/inserir_img.jpg" width="100" height="100" class="img-fluid rounded border border-secondary p-1 m-2 imagens" name="imagem3">          
                        <button data-toggle="2" type="button" class="btn btn-danger p-1 rounded d-none deletar m-auto">deletar</button>
                    </div>
                </div>
                <ul class="list-unstyled text-warning">
                    <li>Imagem não pode ser maior que 500kb</li>
                    <li>Pode até 5 imagens</li>
                    <li>Apenas formatos JPG, JPGE, PNG e GIF</li>
                </ul>
            </fieldset>
            <div class="text-center">
                <button type="submit" class="btn btn-primary mt-3">Enviar</button>
            </div>
        </form>
    </div>
    <script type="text/javascript" src="js/avalicao.js"></script>
</div>