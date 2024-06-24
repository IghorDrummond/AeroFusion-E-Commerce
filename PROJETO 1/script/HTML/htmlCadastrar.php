						<!-- Campo para cadastro -->
						<pre class="w-100">
							<p class="text-danger text-center">
								Aviso Importante:<br>
								Por favor, não cadastre informações confidenciais ou sensíveis neste site. Este site é um projeto pessoal desenvolvido por mim, Ighor Drummond. É importante destacar que qualquer problema ou comprometimento das informações inseridas será de sua inteira responsabilidade. Agradeço a compreensão.
							</p>
							<form class="w-100" onsubmit="cadastrarUsuario()">
								<fieldset class="form-group">
									<legend>Dados Iniciais</legend>
									<label for="Email">Insira seu Email: <span class="text-danger">*</span></label>
									<input type="email" name="Email" class="form-control" placeholder="seuemail@email.com" required>
									<label for="Nome">Insira seu Nome: <span class="text-danger">*</span></label>
									<input type="text" name="Nome" class="form-control" placeholder="Exemplo: José" maxlength="30" required>
									<label for="Sobrenome">Insira seu Sobrenome: <span class="text-danger">*</span></label>
									<input type="text" name="Sobrenome" class="form-control" placeholder="Exemplo: silva" maxlength="30" required>
									<label for="Data">Insira sua Data de Nascimento: <span class="text-danger">*</span></label>
									<input type="date" name="Data" class="form-control" placeholder="Exemplo: silva" maxlength="10" required>
									<label for="Celular">Insira seu numero de celular: <span class="text-danger">*</span></label>
									<input type="text" name="Celular" class="form-control" onkeypress="mascaraCel()" placeholder="(XX) XXXXX-XXXX" maxlength="15" required>
									<label for="Senha">Insira sua Senha: </label>
									<input type="password" maxlength="12" name="Senha" onkeyup="senhaValid()" required pattern="(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*\W+)(?=^.{8,50}$).*$" class="form-control">
									<label for="ConfirmeSenha">Confirme sua Senha: </label>
									<input type="password" maxlength="12" name="ConfirmeSenha" required pattern="(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*\W+)(?=^.{8,50}$).*$" class="form-control">
									<ul id="confereSenha">
										<li>senha com tamanho de 8 caracteres</li>
										<li>um ou mais letra maiúscula</li>
										<li>um ou mais numeros</li>
										<li>um ou mais Símbolo</li>
									</ul>
									<label for="Sexo">Escolha seu sexo: </label>
									<select class="form-control" required>
										<option selected>Selecione um gênero</option>
										<option value="M">Masculino</option>
										<option value="F">Feminino</option>
									</select>
								</fieldset>
								<fieldset class="form-group">
									<legend>Endereço</legend>
									<label for="Cep">Insira seu Cep <span class="text-danger">*</span></label>
									<input type="text" maxlength="9" class="form-control" name="Cep" onkeydown="mascaraCep()" onchange="buscaEndereco()" required placeholder="xxxxx-xxx">
									<label for="Cidade">Insira sua Cidade <span class="text-danger">*</span></label>
									<input type="text" class="form-control" name="Cidade" placeholder="Cidade" required maxlength="20">
									<label for="Estado">Insira seu Estado <span class="text-danger">*</span></label>
									<input type="text" class="form-control" name="Estado" placeholder="UF" maxlength="2" required>
									<label for="Rua">Insira sua rua/avenida <span class="text-danger">*</span></label>
									<input type="text" class="form-control" name="Rua" placeholder="Rua" maxlength="60" required>
									<label for="Bairro">Insira seu bairro <span class="text-danger">*</span></label>
									<input type="text" class="form-control" name="Bairro" placeholder="Bairro" maxlength="50" required>
									<label for="Numero">Insira seu numero residencial <span class="text-danger">*</span></label>
									<input type="number" class="form-control" name="Numero" placeholder="000" required>
									<label for="Complemento">Insira seu complemento</label>
									<input type="text" class="form-control" name="Complemento" maxlength="50" placeholder="...">
									<label for="Referencia">Insira uma referencia</label>
									<input type="text" class="form-control" name="Referencia" maxlength="50" placeholder="...">
								</fieldset>
								<fieldset class="form-group text-left">
									<legend>Dados Pessoais</legend>
									<label for="Cpf">Insira seu CPF <span class="text-danger">*</span></label>
									<input type="text" class="form-control" name="Cpf" onkeyup="mascaraCpf()"  maxlength="14" placeholder="xxx.xxx.xxx-xx" required>
									<p class="text-center text-danger">
										Atenção! Não Insira seu CPF aqui, utilize um site gerador de cpf para isto.
										recomenda-mos este aqui: <a href="https://www.4devs.com.br/gerador_de_cpf" target="_blank">Acessar site</a>
									</p>
								</fieldset>
								<fieldset class="form-group">
									<input type="checkbox" name="termos" required>Eu aceito e concordo com os Termos de Uso e a Política de Privacidade.
									<input type="submit" class="btn btn-primary btn-lg btn-block" value="Prosseguir" required>
									<input type="button" onclick="login()" class="btn btn-primary btn-lg btn-block" value="Voltar">
								</fieldset>
							</form>							
						</pre>