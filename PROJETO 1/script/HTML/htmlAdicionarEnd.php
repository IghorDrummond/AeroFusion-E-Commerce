<div class="w-100 d-flex p-3 justify-content-center align-items-center end_body">
	<div class="bg-white rounded p-2 end_dados">
		<form class="form-group" onsubmit="cadastrarEnd()">
			<fieldset>
				<legend>Insira seu Endereço:</legend>

				<div class="form-group">
					<label for="cep">Insira seu Cep:</label>
					<input class="form-control" placeholder="xxxxx-xxx" name="cep" maxlength="9" required>
				</div>

				<div class="form-group">
					<label for="rua">Rua:</label>
					<input class="form-control" name="rua" required>
				</div>

				<div class="form-group">
					<label for="bairro">Bairro:</label>
					<input class="form-control" name="bairro" required>
				</div>

				<div class="form-group">
					<label for="cidade">Cidade:</label>
					<input class="form-control" name="cidade" required>
				</div>

				<div class="form-group">
					<label for="estado">Estado:</label>
					<input class="form-control" name="estado" maxlength="2" required>
				</div>

				<div class="form-group">
					<label for="numero">Número:</label>
					<input type="number" class="form-control" name="numero" required>
				</div>

				<div class="form-group">
					<label for="referencia">Referência:</label>
					<input class="form-control" name="referencia">
				</div>

				<div class="form-group">
					<label for="complemento">Complemento:</label>
					<input class="form-control" name="complemento">
				</div>
			</fieldset>
			<fieldset class="form-group text-center">
				<label>Escolha uma das duas opções abaixo: </label><br>
				<input type="submit" name="Enviar" value="Cadastrar" class="btn btn-primary text-white rounded">
				<input type="button" name="Cancelar" value="Cancelar" class="btn btn-danger text-white rounded" onclick="fecharEnd()">
			</fieldset>
		</form>
	</div>
</div>