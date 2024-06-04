						<div class="w-100">
							<label for="Email">Email:</label>
							<input type="email" class="form-control" name="Email" placeholder="seuemail@email.com" required>
						</div>
						<div class="w-100">
							<label for="Senha">Senha:</label>
							<input type="password" class="form-control" name="Senha" pattern="(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*\W+)(?=^.{8,50}$).*$" required>
						</div>
						<div class="w-100">
							<br>
							<button class="btn btn-primary btn-lg btn-block" onclick="Logar()">Entrar</button>
							<br>
						</div>
						<div class="w-100 text-center">
							<label>Com dificuldade para logar? Tente isso:</label>
							<br>
							<button class="btn btn-primary" onclick="cadastrar()">Cadastrar</button>
							<button class="btn btn-primary" onclick="trocaSenha()">Esqueci a Senha</button>
						</div>