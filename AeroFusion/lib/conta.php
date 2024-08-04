<?php	
	//Classes e seus Namespace
	namespace Acesso{
		require_once('conexao.php');//Importa a Biblioteca de Conexão ao Banco

		/*
		*Interfaces: ProjetaQuery
		*Descrição: Seta Interfaces padrões para varias classes
		*Data: 17/05/2024
		*Programador(a): Ighor Drummond
		*/
		//Interfaces
		interface ProjetaQuery{
			public function retornaValores();
		}
		
		/*
		*Classe: Error
		*Descrição: Erros Customizados
		*Data: 17/05/2024
		*Programador(a): Ighor Drummond
		*/
		//Classes
		class Error extends \Exception{
			//Atributos
			private $error = '';

			//Construtor
			public function __construct($titulo, $error){
				$this->error = '
				<div class="alert alert-warning alert-dismissible fade show sticky-top" role="alert">
					<strong>'. $titulo .'</strong>' . $error .
					'<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>';

				$this->retornaError();
			}
			//Destruidor
			public function __destruct(){
				return 'Destruido com Sucesso!';
			}

			//Métodos
			/*
			*Metodo: retornaError
			*Descrição: Responsavel o erro desejado pela função
			*Data: 17/05/2024
			*Programador(a): Ighor Drummond
			*/
			public function retornaError(){
				return $this->error;
			}
		}

		/*
		*Classe: login
		*Descrição: Responsavel por retornar dados do login
		*Data: 17/05/2024
		*Programador(a): Ighor Drummond
		*/
		class Login extends Error implements ProjetaQuery {
			public $email = '';
			public $stmt = [];
			private $query = '';
			private $conexao = null;
			protected $senha = '';
			protected $logado = false;
			
			//Construtor
			public function __construct($email, $senha) {
				try {
					if (empty($email) or empty($senha)) {
						throw new Error("Error Processing Request", 'Não foi passado os parametros corretos!');
					}
					//Guarda Valores do Email e Senha
					$this->email = $email;
					$this->senha = $senha;					

					$construtorConect = new \IniciaServer();//Inicia classe responsavel para construção
					$this->conexao = $construtorConect->conexao();//Inicia Conexão PDO
					$this->montaQuery();//Monta a Query necesária
					$this->buscarValor();//Pesquisa no banco de dados
				} catch (Error $e) {
					echo $e->retornaError();
				}
			}
			//Destruidor
			public function __destruct() {
				return 'Destruido com Sucesso!';
			}
			//Getters
			public function getFoto(){
                if($this->logado){
                    if(isset($this->stmt[0]['email']) and $this->logado){
                        return $this->stmt[0]['foto'];
                    }
                }
			}

			public function getNome(){
				if($this->logado){
					return $this->stmt[0]['nome_user'];
				}
			}

			//Métodos
			/*
			*Metodo: retornaValores()
			*Descrição: retorna se usuário existe ou não além de validar senha incorreta
			*Data: 17/05/2024
			*Programador(a): Ighor Drummond
			*/
			public function retornaValores(){
				$ret = '';

				if(isset($this->stmt[0]['senha']) and isset($this->stmt[0]['email'])){
					if($this->stmt[0]['email'] === $this->email and $this->stmt[0]['senha'] === $this->senha){
						$ret = 'Logado';
						$this->logado = true;
					}else if($this->stmt[0]['email'] === $this->email and $this->stmt[0]['senha'] != $this->senha){
						$ret = 'Senha';
					}
				}else{
					$ret = 'NaoExiste';
				}

				return $ret;
			}
			/*
			*Metodo: montaQuery()
			*Descrição: monta e estrutura a query para fazer uma busca no banco de dados
			*Data: 17/05/2024
			*Programador(a): Ighor Drummond
			*/
			private function montaQuery() {
				$this->query = " 
					SELECT 
						TRIM(email) as email,
						TRIM(senha) as senha,
						CONCAT(nome, ' ', sobrenome) as nome_user,
						foto
					FROM 
						cliente
					WHERE
						email = '$this->email' 
				";
			}
			/*
			*Metodo: buscaValor()
			*Descrição: faz uma busca por um valor determinado no banco de dados
			*Data: 17/05/2024
			*Programador(a): Ighor Drummond
			*/
			private function buscarValor(){
				try{
					$this->stmt	= $this->conexao->query($this->query);//Recebe os dados retornados  caso houver
					$this->stmt = $this->stmt->fetchAll();//Formata Resposta para Array
				}catch(\PDOException $e){
					echo 'Error: ' . $e;
				}
			}
		}


		/*
		*Classe: Endereco
		*Descrição: Responsavel por retornar ou incluir novos endereços
		*Data: 15/06/2024
		*Programador(a): Ighor Drummond
		*/
		class Endereco{
			//Atributos
			private $con = null;
			private $query = null;
			private $stmt = null;
			private $IdCli = null;
			private $IdEnd = null;

			//Construtor
			public function __construct(
				public $Email = '',
			){
				try{
					//Inicia conexão com o banco de dados
					$this->con = new \IniciaServer();
					$this->con = $this->con->conexao();
					//Recupera o Id do cliente cadastrado
					$this->montaQuery(2);
					$this->IdCli = $this->retornaValores()[0]['id'] ;
				}catch(\PDOException $e){
					echo $e->getMessage();
				}
			}

			//Métodos
			/*
			*Metodo: retornaError
			*Descrição: Responsavel por recuperar endereços cadastrados do usuário
			*Data: 15/06/2024
			*Programador(a): Ighor Drummond
			*/
			public function getEndereco(){
				$this->montaQuery(0);
				$this->stmt = $this->retornaValores();	
				return $this->stmt;
			}

			/*
			*Metodo: setEndereco()
			*Descrição: Responsavel por inserir um novo endereço para usuário
			*Data: 15/06/2024
			*Programador(a): Ighor Drummond
			*/
			public function setEndereco( $Rua, $Complemento, $Cep, $Referencia, $Bairro, $Uf, $Numero, $Cidade){				
				$EndExiste = false;
				
				try{
					//Valida se foi passado os endereço corretamente
					if(!empty($Rua) and !empty($Cep) and !empty($Bairro) and !empty($Uf) and !empty($Numero) and !empty($Cidade)){

						//Valida se o endereço já existe
						$this->montaQuery(0);
						$this->stmt = $this->retornaValores();
						
						foreach ($this->stmt as $end) {
							if($end['cep'] === $Cep and trim(strtoupper($end['rua'])) === trim(strtoupper($Rua)) and strval($end['numero']) === $Numero){
								if($end['end_ativo'] === 1){
									$EndExiste = true;
									break;
								}else{
									//Caso o usuário inserir o mesmo endereço após desativa-lo/excluir, iremos reativa-lo novamente.
									$this->IdEnd = $end['id_end'];
									$this->montaQuery(3);
									$this->reativaEnd();
									return null;//Retorna Null após a conclusão do processo
								}
							}
						}
						
						//Se caso o endereço não existir, ele adiciona o mesmo
						if(!$EndExiste){
							$this->montaQuery(1);//Monta query para inserir novo endereço
							//Prepara a inserção de dados
							$this->query .= "
								VALUES('$Cidade', '$Referencia', '$Uf', '$Bairro', '$Rua', '$Cep', '$Numero', '$Complemento', $this->IdCli)
							";
		
							$this->con->beginTransaction();
							if( ($this->con->exec($this->query)) > 0 ){
								$this->con->commit();
								echo 'OK';
							}else{
								throw new \PDOException("Não Inseriu dados na query");
							}
						}else{
							echo 'EXISTE';
						}
					}else{
						echo 'FALTADADOS';
					}
				}catch(\PDOException $e){
					echo $e->getMessage();
					$this->con->rollback();
				}
			}

			/*
			*Metodo: montaQuery(Opção)
			*Descrição: Responsavel por montar as querys
			*Data: 15/06/2024
			*Programador(a): Ighor Drummond
			*/
			private function montaQuery($Val){
				if($Val === 0){
					$this->query = "
						SELECT
							ende.cidade,
						    ende.referencia,
						    ende.uf,
						    ende.bairro,
						    ende.rua,
						    ende.cep,
						    ende.numero,
						    ende.complemento,
						    cli.id,
						    ende.id_end,
							ende.end_ativo
						FROM
							endereco as ende
						INNER JOIN
							cliente as cli ON cli.id = ende.id_cliente
						WHERE
							cli.email = '$this->Email'
					";
				}else if($Val === 1){
					$this->query = "
						INSERT INTO endereco(cidade, referencia, uf, bairro, rua, cep, numero, complemento, id_cliente)
					" . PHP_EOL;				
				}else if($Val === 2){
					$this->query = "
						SELECT
							cli.id
						FROM
							cliente as cli
						WHERE
							cli.email = '$this->Email'
					";
				}else if($Val === 3){
					$this->query = "
						UPDATE
							endereco
						SET
							end_ativo = true
						WHERE
							id_end = $this->IdEnd
					";
				}
			}
			
			/*
			*Metodo: retornaValores()
			*Descrição: Responsavel por retorna a consulta no banco
			*Data: 26/06/2024
			*Programador(a): Ighor Drummond
			*/
			private function retornaValores(){
				try{
					$this->stmt = $this->con->query($this->query);
				}catch(\PDOException $e){
					echo $e->getMessage();
				}finally{
					return $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
				}
			}
			/*
			*Metodo: reativaEnd()
			*Descrição: Responsavel por reativar o endereço deletado/desativado
			*Data: 04/07/2024
			*Programador(a): Ighor Drummond
			*/
			private function reativaEnd(){
				$this->montaQuery(3);
				try{
					$this->con->beginTransaction();
					$this->con->exec($this->query);
					$this->con->commit();
					echo 'OK';//da status de Ok após o cadastro
				}catch(\PDOException $e){
					echo $e->getMessage();
					$this->con->rollBack();
				}
			}
		}
	}

	namespace Cadastro{
		//Importa as Bibliotecas
		require_once('conexao.php');
		require_once('Exception.php');
		require_once('SMTP.php');
		require_once('PHPMailer.php');

		use PHPMailer\PHPMailer\PHPMailer;
		use PHPMailer\PHPMailer\SMTP;
		use PHPMailer\PHPMailer\Exception;
		/*
		*Classe: Error
		*Descrição: Erros Customizados
		*Data: 17/05/2024
		*Programador(a): Ighor Drummond
		*/
		//Classes
		class Error extends \Exception{
			//Atributos
			private $error = '';

			//Construtor
			public function __construct($titulo, $error){
				$this->error = '
				<div class="alert alert-warning alert-dismissible fade show sticky-top" role="alert">
					<strong>'. $titulo .'</strong>' . $error .
					'<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>';

				$this->retornaError();
			}
			//Destruidor
			public function __destruct(){
				return 'Destruido com Sucesso!';
			}

			//Métodos
			/*
			*Metodo: retornaError
			*Descrição: Responsavel o erro desejado pela função
			*Data: 17/05/2024
			*Programador(a): Ighor Drummond
			*/
			public function retornaError(){
				return $this->error;
			}
		}

		/*
		*Interfaces: ProjetaQuery
		*Descrição: Seta Interfaces padrões para varias classes
		*Data: 20/05/2024
		*Programador(a): Ighor Drummond
		*/
		//Interfaces
		interface EstruturaCadastrar{
			public function enviar();
		}

		/*
		*Classe: Cadastrar
		*Descrição: Responsavel por cadastrar o novo usuário e fazer validações
		*Data: 20/05/2024
		*Programador(a): Ighor Drummond
		*/
		class Cadastrar extends Error implements EstruturaCadastrar{
			//Atributos
			public $stmt = [];
			private $Query = '';
			private $conexao = null;
			protected $Nome = '';
			protected $Sobrenome = '';
			protected $Data = ''; 
			protected $Endereco = [];
			protected $Cpf = '';
			//Construtor
			public function __construct(
				private $Genero = '',
				protected $Email = '',
				protected $Senha = '',
				protected $ConfirmeSenha = '',
				protected $Celular = ''
			){
				try{
					//Valida dados e parametros enviados
					if(empty($this->Email) or empty($this->Senha) or empty($this->ConfirmeSenha) or empty($this->Genero) or empty($this->Celular)){
						throw new Error("Error Processing Request", 'Não foi passado os parametros corretos!');
					}
					//Retira quebra de linha na senha
					$this->Senha = preg_replace('/\r\n|\r|\n/', '', $this->Senha);
					$this->ConfirmeSenha = preg_replace('/\r\n|\r|\n/', '', $this->ConfirmeSenha);

					$this->conexao = new \IniciaServer();//Inicia classe responsavel para construção
					$this->conexao = $this->conexao->conexao();//Passa toda operação de conexão para variavel
				}catch(Error $e){
					echo $e->retornaError();
					$this->__destruct();//Destrói operação
				}
			}

			//Destruidor
			public function __destruct(){
				return 'Destruido com sucesso';
			}

			//Setters
			public function setDados($Nome, $Sobrenome, $Data){
				try{
					//Valida dados e parametros enviados
					if(empty($Nome) or empty($Sobrenome) or empty($Data)){
						throw new Error("Error Processing Request", 'Não foi passado os parametros corretos setDados!');
					}
					//Passa dados para os atributos
					$this->Nome = $Nome;
					$this->Sobrenome = $Sobrenome;
					$this->Data = $Data;
				}catch(Error $e){
					echo $e->retornaError();
					$this->__destruct();//Destrói operação
				}
			}

			public function setEndereco($Cep, $Cidade, $Uf, $Rua, $Bairro, $Numero, $Complemento, $Referencia){
				try{
					foreach (func_get_args() as $i => $value){
						if(empty($value) and $i < 6){
							throw new Error("Error Processing Request", 'Não foi passado os parametros corretos no setEndereco!');
							break;
						}else{
							$this->Endereco[$i] = $value;//Recebe todos os dados do endereços enviados
						}
					}
				}catch(Error $e){
					echo $e->retornaError();
				}			
			}

			public function setCpf($Cpf){
				try{
					if(empty($Cpf)){
						throw new Error("Error Processing Request", 'Não foi passado o parametro correto no setCpf!');
					}
					$this->Cpf = $Cpf;
				}catch(Error $e){
					echo $e->retornaError();
				}					
			}

			//Métodos Publicos
			/*
			*Metodo: enviar()
			*Descrição: responsavel por cadastrar dados do novo usuário
			*Data: 20/05/2024
			*Programador(a): Ighor Drummond
			*/
			public function enviar(){
				$status = $this->relatorio();
				$IdCliente = 0;
				$Query = [];
				
				if($status === 'OK'){
					try{
						//Inicia begin transaction para segurança
						$this->conexao->beginTransaction();
						//Monta as Querys para incluir novo usuário
						$Query[0] = " INSERT INTO cliente(email, senha, nome, sobrenome, data_nascimento, genero, celular, cpf, opc) 
						VALUES('$this->Email', '$this->Senha', '$this->Nome', '$this->Sobrenome', '$this->Data', '$this->Genero', '$this->Celular', '$this->Cpf', 4)";

						$Query[1] = " INSERT INTO endereco(rua, bairro, cidade, numero, complemento, referencia, cep, uf, id_cliente) ";
						//Mande executar as querys
						for($nCont = 0; $nCont <= 1; $nCont++){
							$alteracao = $this->conexao->exec($Query[$nCont]);
							//Finaliza a transação
							if($alteracao > 0){
								//Se ocorrer tudo certo com a inclusão, ele finaliza a transação de dados ao banco
								$IdCliente = $this->conexao->lastInsertId();
								$Query[1] .= "VALUES('" . $this->Endereco[3] ."', '". $this->Endereco[4] . "', '" . $this->Endereco[1] . "', '" . $this->Endereco[5] . "', '" . $this->Endereco[6] . "', '". $this->Endereco[7] . "', '" . $this->Endereco[0] . "' , '" . $this->Endereco[2] . "', '" . $IdCliente . "');
								";
							}else{
								//Cria erro ao não conseguir incluir
								throw new \PDOException("ERROR");
								break;
							}
						}	
						//Se ocorrer tudo certo com a inclusão, ele finaliza a transação de dados ao banco
						$this->conexao->commit();
					}catch(\PDOException $e){
						//Cancela gravação ao banco de dados
						$this->conexao->rollBack();
						echo 'Erro: '.$e->getCode() . 'Mensagem: ' . $e->getMessage();
					}
				}

				return $status;
			}	

			//Métodos Privados ou protegidos
			/*
			*Metodo: existeEmail()
			*Descrição: valida se o email inserido já existe
			*Data: 20/05/2024
			*Programador(a): Ighor Drummond
			*/
			protected function existeEmail(){
				//Monta Query para busca
				$Query = " 
					SELECT 
						email
					FROM 
						cliente
					WHERE
						email = '$this->Email'
				";

				//Manda Buscar dados no servidor
				$this->stmt = $this->buscaValor($Query);
				//Valida se não deu nada errado com a operação exigida
				if(!empty($this->stmt[0])){
					return true;
				}else if(isset($this->stmt[0]) and $this->stmt[0] === false){	
					$this->__destruct();//Destrói operação por algum erro no caminho
				}else{
					return false;
				}
			}
			/*
			*Metodo: validaSenha()
			*Descrição: valida se as senhas se correspondem
			*Data: 20/05/2024
			*Programador(a): Ighor Drummond
			*/
			protected function validaSenha(){
				if($this->Senha != $this->ConfirmeSenha){
					return false;
				}
				return true;
			}
			/*
			*Metodo: validaSenha()
			*Descrição: valida se a data de nascimento é maior que 18 anos
			*Data: 20/05/2024
			*Programador(a): Ighor Drummond
			*/
			private function validaData(){
				//Declaração de Variaveis
				//Numericos
				$dataInicial = null;
				$dataFinal = null;
				$diferenca = null;

				if(!empty($this->Data)){
					//Define o horario 
					date_default_timezone_set('America/Sao_Paulo');
					//Pega a Data Inicial
					$dataInicial = new \DateTime($this->Data);
					//Pega a Data final
					$dataFinal = new \DateTime(Date('Y-m-d'));
					//Faz a Diferença
					$diferenca = $dataInicial->diff($dataFinal);
					//Valida se a data inicial e final é maior ou igual a 18 anos
					if($diferenca->y >= 18){
						return true;
					}
				}

				return false;
			}
			/*
			*Metodo: validaCpf()
			*Descrição: valida se o cpf inserido é valido
			*Data: 20/05/2024
			*Programador(a): Ighor Drummond
			*/
			protected function validaCpf(){
				//Declaração de Variaveis
				//Numericos
				$total = 0;
				//Array
				$digitos = [];
				$valoresMulti = [
					[10,11],
					[8,9]
				];
				
				if(!empty($this->Cpf)){
					//Valida se todos os numeros são Iguais
					for($nCont = 0; $nCont <= strlen($this->Cpf)-1; $nCont++){
						if(substr($this->Cpf, $nCont, 1) != substr($this->Cpf, 0, 1) ){
							break;
						}
					}
					if($nCont < 10){
						for ($i=0; $i <=1 ; $i++) { 
							$total = $this->retornaMultiplicacao($valoresMulti[0][$i] , $valoresMulti[1][$i]);
							//Calcula o Resto da divisão para validar os digitos
							$digitos[$i] = (($total * 10) % 11);
						}

						$digitos[2] = strval($digitos[0]) . strval($digitos[1]);

						if(substr($this->Cpf, 9,2)  === $digitos[2]){
							return true;
						}
					}
				}	

				return false;
			}
			/*
			*Metodo: buscaValor(Query a ser usada na busca)
			*Descrição: ira fazer uma consulta ao banco de dados
			*Data: 20/05/2024
			*Programador(a): Ighor Drummond
			*/
			private function buscaValor($Query){
				try{
					//Busca Dados dentro do banco
					$StmtAux = $this->conexao->query($Query);
					//Retorna para Usuário
					return $StmtAux->fetchAll();					
				}catch(\PDOException $e){
					echo 'Error: ' . $e;
					return [false];//Retorna Vazio caso conexão falhar;
				}
			}
			/*
			*Metodo: retornaMultiplicacao(numero a ser multiplicado, limite do laço)
			*Descrição: ira multiplicar os valores até o limite definido
			*Data: 20/05/2024
			*Programador(a): Ighor Drummond
			*/
			protected function retornaMultiplicacao($multi, $fim){
				$ret = 0;

				for($nCont = 0; $nCont <= $fim; $nCont++){
					$ret += (intval(substr($this->Cpf, $nCont, 1)) * $multi );
					$multi--;
				}

				return $ret;
			}
			/*
			*Metodo: relatorio()
			*Descrição: ira validar todos os dados passado pelo usuário
			*Data: 20/05/2024
			*Programador(a): Ighor Drummond
			*/
			private function relatorio(){

				if($this->existeEmail()){
					return 'EMAIL';
				}else if(!$this->validaSenha()){
					return 'SENHA';
				}else if(!$this->validaData()){
					return 'DATA';
				}else if(!$this->validaCpf()){
					return 'CPF';
				}else{
					return 'OK';
				}
			}
		}

		/*
		*Classe: CodigoEmail
		*Descrição: Responsavel por cadastrar o novo usuário e fazer validações
		*Data: 21/05/2024
		*Programador(a): Ighor Drummond
		*/
		class CodigoEmail extends Error
		{
			//Atributos
			private $conexao = '';
			private $stmt = [];
			private $Codigo = '';
			private $Caracteres = "ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890#_@%;";

			//Construtor			
			function __construct(
				public $Email = ''
			){
				try{
					if(empty($this->Email)){
						throw new Error("Parametros não informados corretamentes", 1);
					}
					//Inicia a Conexão
					$this->conexao = new \IniciaServer();
					$this->conexao = $this->conexao->conexao();
				}catch(Error $e){
					echo $e->retornaError();
				}
			}
			//Métodos
			/*
			*Metodo: enviaEmail()
			*Descrição: envia o email para geração de código
			*Data: 21/05/2024
			*Programador(a): Ighor Drummond
			*/
			public function enviaEmail(){
				if($this->existeEmail()){
					//Inicia o Envio do Email
					$mail = new PHPMailer(true);

					try {
						//Gera o código 
						for($nCont = 0; $nCont <= 7; $nCont++){
							$this->Codigo .= substr($this->Caracteres, random_int(0, 39), 1);
						}
						//Guarda Código no banco de dados
						if($this->guardaCodigo() === false){
							return false;
						};
						//Configuarações do Servidor
						//$mail->SMTPDebug = 2; 
						$mail->isSMTP();                                            
						$mail->SMTPAuth   = true;                                   
						$mail->Username   = 'seuemail';                     
						$mail->Password   = 'suasenha';                               
						$mail->SMTPSecure = 'tls';            
						$mail->SMTPAutoTLS = false;
						$mail->SMTPOptions = array(
							'ssl' => array(
								'verify_peer' => false,
								'verify_peer_name' => false,
								'allow_self_signed' => true
							)
						);
						$mail->CharSet="UTF-8";
						$mail->Host       = 'smtp.email.com';                     
						$mail->Port       = 587;
						//Destinatário e Remetente
						$mail->setFrom('seuemail', 'Não Responda - Código');
						$mail->addAddress($this->Email, $this->stmt[0]['nome_user']);     

						//Corpo do Email
						$mail->isHTML(true);                                  
						$mail->Subject = 'Envio do Código de Recuperação da Senha';
						$mail->Body    = $this->MontaCorpo();
						$mail->AltBody = 'Este é o seu Código para Recuperar a Senha: ' . $this->Codigo;
						//Envio do Email
						$mail->send();

						return true;
					} catch (\Exception $e) {
						echo "Email não foi Enviado, ocorreu problema: {$mail->ErrorInfo}";
						return false;
					}			
				}else{	
					return false;
				}
			}

			//Métodos Privados ou protegidos
			/*
			*Metodo: existeEmail()
			*Descrição: valida se o email inserido já existe
			*Data: 21/05/2024
			*Programador(a): Ighor Drummond
			*/
			private function existeEmail(){
				try{
					//Monta Query para busca
					$Query = " 
						SELECT 
							id,
							email,
							CONCAT(nome, ' ', sobrenome) as nome_user
						FROM 
							cliente
						WHERE
							email = '$this->Email'
					";

					$this->stmt = $this->conexao->query($Query);
					$this->stmt = $this->stmt->fetchAll();
					//Manda Buscar dados no servidor
					//Valida se não deu nada errado com a operação exigida
					if(!empty($this->stmt[0])){
						return true;
					}else if(isset($this->stmt[0]) and $this->stmt[0] === false){	
						$this->__destruct();//Destrói operação por algum erro no caminho
					}else{
						return false;
					}
				}catch(\PDOException $e){
					echo $e->getMessage();
				}
			}
			/*
			*Metodo: guardaCodigo(
			*Descrição: guarda código
			*Data: 21/05/2024
			*Programador(a): Ighor Drummond
			*/
			private function guardaCodigo(){
				try{
					//Define o horario 
					date_default_timezone_set('America/Sao_Paulo');

					$Query = "
						INSERT INTO codigos(codigo, data_codigo, id_cliente)
						VALUES('$this->Codigo', '". date('Y-m-d H:i:s') ."', '". $this->stmt[0]['id'] ."' );
					";

					$this->conexao->beginTransaction();

					$Alterado = $this->conexao->exec($Query);

					if($Alterado > 0){
						$this->conexao->commit();
						return true;
					}else{
						throw new \PDOException('Não foi adicionado o código ao banco de dados');
					}
				}catch(\PDOException $e){
					$this->conexao->rollBack();
					echo $e->getMessage();
					return false;
				}
			}
			/*
			*Metodo: MontaCorpo
			*Descrição: Monta corpo do Email
			*Data: 21/05/2024
			*Programador(a): Ighor Drummond
			*/
			private function MontaCorpo(){
				$Email = "
				<head>
					<style type='text/css'>
						* {
							padding: 0;
							margin: 0;
						}
		
						header {
							height: 10vh;
							background: orange;
							text-align: center;
							color: white;
							padding: 15px;
							-webkit-box-sizing: border-box;
									box-sizing: border-box;
						}
						header>h1{
							margin: auto;
						}
						section>h1{
							background: gray;
							letter-spacing: 15px;
							width: 50%;
							margin: 25px auto;
							color: white;
						}
						section, footer, header{
							text-align: center;
							font-family: arial;
							padding: 25px;
							-webkit-box-sizing: border-box;
									box-sizing: border-box;
						}
						footer{
							margin-top: 35px;
						}
					</style>
				</head>

				<body>
					<header>
						<h1>AeroFusion</h1>
					</header>
					<main>
						<section>
							<h3>Olá! ". $this->stmt[0]['nome_user']  . "</h3>
							<h1>". $this->Codigo ."</h1>
						</section>
						<section>
							<p>
								<strong>Atenção usuário:</strong><br> Ao receber um código de segurança para trocar sua senha, tenha
								cautela. Mantenha o código privado e não o compartilhe com ninguém. Verifique sempre a autenticidade da
								fonte antes de inserir o código. Sua segurança é primordial.<br><br>
								A segurança da senha é fundamental para proteger nossas informações pessoais e manter nossa privacidade
								online. Aqui estão algumas razões pelas quais a segurança da senha é tão importante:
								<br><br> Proteção de Dados Pessoais: Senhas fortes ajudam a proteger nossas informações pessoais, como
								dados bancários, endereços, histórico de compras e comunicações privadas. Sem uma senha segura, essas
								informações podem ser facilmente acessadas por pessoas não autorizadas.
								<br><br> Prevenção contra Acesso Não Autorizado: Uma senha forte é a primeira linha de defesa contra
								hackers e cibercriminosos que tentam acessar nossas contas online. Se nossas senhas forem fracas ou
								fáceis de adivinhar, nossa conta se torna vulnerável a ataques de força bruta, phishing e outras
								técnicas de hacking.
								<br><br> Evitar Roubos de Identidade: Senhas seguras ajudam a evitar o roubo de identidade, onde alguém
								pode usar nossas informações pessoais para se passar por nós e cometer fraudes em nosso nome. Uma senha
								forte dificulta a tentativa de roubo de identidade e protege nossa reputação online.
								<br><br> Manter a Confidencialidade: Senhas são usadas para proteger informações confidenciais, como
								dados comerciais, propriedade intelectual e segredos comerciais. Sem uma senha forte, essas informações
								podem ser expostas e comprometer a competitividade e a segurança de uma organização.
								<br><br> Preservar a Segurança Financeira: Muitos serviços online estão vinculados a contas bancárias e
								cartões de crédito. Uma senha segura é essencial para proteger nossas finanças e evitar que criminosos
								acessem e abusem de nossos fundos.
								<br><br> Garantir a Integridade das Comunicações: Senhas protegem nossas contas de e-mail, mensagens e
								redes sociais, garantindo que apenas pessoas autorizadas possam acessar nossas comunicações privadas.
								Isso é especialmente importante para manter a confiança e a privacidade em relacionamentos pessoais e
								profissionais.
								<br><br> Em resumo, a segurança da senha desempenha um papel crucial na proteção de nossas informações
								pessoais, financeiras e profissionais. Portanto, é essencial criar e manter senhas fortes e únicas para
								cada uma de nossas contas online.
							</p>
						</section>
					</main>
					<footer>
						<h5>Desenvolvido Por Ighor Drummond</h5>
					</footer>
				</body>
				";

				return $Email;
			}
		}

		/*
		*Classe: validaCodigo
		*Descrição: Responsavel por validar o código enviado via email
		*Data: 21/05/2024
		*Programador(a): Ighor Drummond
		*/
		class validaCodigo extends Error{
			//Atributos
			private $conexao = '';
			private $stmt = [];

			//Construtor
			public function __construct(
				public $Codigo,
				public $Email
			){
				$this->conexao = new \IniciaServer();
				$this->conexao = $this->conexao->conexao();
			}
			/*
			*Metodo: validaCodigo()
			*Descrição: Valida se existe algum código gerado pelo usuário
			*Data: 21/05/2024
			*Programador(a): Ighor Drummond
			*/
			public function validaCodigo(){
				try{
					$Query = "
						SELECT 
							cod.codigo as cod,
							cod.data_codigo as data,
							cli.email
						FROM
							codigos as cod
						INNER JOIN 
							cliente AS cli ON cli.id = cod.id_cliente
						WHERE 
							cli.email = '$this->Email'
						ORDER BY
                        	cod.data_codigo DESC
						LIMIT
							1 
					";

					$this->stmt = $this->conexao->query($Query);
					$this->stmt = $this->stmt->fetchAll();

					if(!empty($this->stmt[0])){
						if($this->Codigo === $this->stmt[0]['cod']){
							if($this->validaData()){
								return 'OK';
							}else{
								return 'DATA';
							}
						}else{
							return 'CODIGO';
						}
					}else{
						return 'EMAIL';
					}
				}catch(\PDOException $e){
					return $e->getMessage();
				}
			}
			/*
			*Metodo: validaData()
			*Descrição: Valida Data do código gerado
			*Data: 21/05/2024
			*Programador(a): Ighor Drummond
			*/
			private function validaData(){
				//Declaração de Variaveis
				//Numericos
				$dataInicial = null;
				$dataFinal = null;
				$diferenca = null;
				//Define o horario 
				date_default_timezone_set('America/Sao_Paulo');
				//Pega a Data Inicial
				$dataInicial = new \DateTime($this->stmt[0]['data']);
				//Pega a Data final
				$dataFinal = new \DateTime(Date('Y-m-d  H:i:s'));
				//Faz a Diferença
				$diferenca = $dataInicial->diff($dataFinal);
				//Retorta validação
				return $diferenca->d < 1 ? true: false;
			}
		}

		/*
		*Classe: RenovarSenha
		*Descrição: Responsavel atualizar a senha do usuário
		*Data: 22/05/2024
		*Programador(a): Ighor Drummond
		*/
		class RenovarSenha extends Error
		{
			protected $conexao = '';

			function __construct(
				public $Senha = '',
				public $Email = ''
			)
			{
				try{
					//Retira quebra de linha na senha
					$this->Senha = preg_replace('/\r\n|\r|\n/', '', $this->Senha);
					//Inicia conexão
					$this->conexao  = new \IniciaServer();
					$this->conexao = $this->conexao->conexao();
				}catch(\PDOException $e){
					echo $e->getMessage();
					return false;		
				}
			}

			//Métodos
			/*
			*Metodo: atualizaSenha()
			*Descrição: atualiza Senha do usuário
			*Data: 22/05/2024
			*Programador(a): Ighor Drummond
			*/
			public function atualizaSenha(){
				$Query = '';
				try{
					$Query = "
						UPDATE 
							cliente
						SET
							senha = '$this->Senha'
						WHERE 
							email = '$this->Email'
					";
					$this->conexao->beginTransaction();

					$this->conexao->exec($Query);

					$this->conexao->commit();
					return true;
				}catch(\PDOException $e){
					$this->conexao->rollback();
					echo $e->getMessage();
					return false;
				}
			}
		}

		/*
		*Classe: Cadastrar cartão usuário
		*Descrição: Responsavel atualizar a senha do usuário
		*Data: 03/07/2024
		*Programador(a): Ighor Drummond
		*/
		class Cartao{
			//Atributos
			private $con = null;
			private $Query = null;
			private $stmt = null;
			private $Nome = null;
			private $Numero = null;
			private $Cvc = null;
			private $Validade = null;
			private $Operadora = null;
			private $IdCli = null;
			private $IdBan = null;
			private $IdCard = null;

			//construtor
			public function __construct(
				public $Email = null
			){
				try{
					$this->con = new \IniciaServer();
					$this->con = $this->con->conexao();
					//Recupera Id do cliente
					$this->montaQuery(2);
					$this->IdCli = $this->getDados()[0]['cliente'];
				}catch(\PDOException $e){
					echo $e->getMessage();
				}
			}

			//Métodos
			/*
			*Metodo: setCartao
			*Descrição: adiciona um novo cartão para u´suário
			*Data: 03/07/2024
			*Programador(a): Ighor Drummond
			*/
			public function setCartao(
				$Numero,
				$Nome, 
				$Ban,
				$Validade,
				$Cvc
			){
				//Recupera id da bandeira
				$this->Nome = $Nome;
				$this->Numero = $Numero;
				$this->IdBan = $Ban;
				$this->Cvc = $Cvc;

				//Formata data
				$Validade = explode('/', $Validade);
				if((int)$Validade[0] >= 1 and (int)$Validade[0] <= 12){
					$this->Validade = $Validade[1] . '-' . $Validade[0] . '-' . '01';					
				}else{
					echo 'DATA';
					return false;
				}

				//Valida se este cartão a ser inserido já existe
				$this->montaQuery(4);
				if(isset($this->getDados()[0]['id_card'])){
					echo 'EXISTE';
					return false;
				}

				//Valida se cartão já não foi vencido
				//Define o horario 
				date_default_timezone_set('America/Sao_Paulo');
				//Pega a Data Inicial
				$dataInicial = new \DateTime($this->Validade);
				//Pega a Data final
				$dataFinal = new \DateTime(Date('Y-m-d H:i:s'));
				//Faz a Diferença
				$diferenca = $dataInicial->diff($dataFinal);
				//Valida se a data inicial é menor que a data final
				if($diferenca->y > 0 and $diferenca->invert === 0){
					echo 'VENCIDO';
					return false;
				}				

				//Monta query para inserir novo cartão
				$this->montaQuery(1);
				if($this->setDados()){
					return true;
				}else{
					return false;
				}
			}
			/*
			*Metodo: getCartao
			*Descrição: retorna os cartões do usuário
			*Data: 03/07/2024
			*Programador(a): Ighor Drummond
			*/
			public function getCartao($Cartao){
				$this->IdCard = (empty($Cartao) or is_null($Cartao)) ? '' : $Cartao;
				$this->montaQuery(3);
				$Ret = $this->getDados();
				return $Ret;
			}
			/*
			*Metodo: delCartao()
			*Descrição: deleta cartão especifico
			*Data: 31/07/2024
			*Programador(a): Ighor Drummond
			*/
			public function delCartao($Cartao){
				$this->IdCard = $Cartao;
				$this->montaQuery(5);
				return $this->setDados() ? 'DELETADO' : 'ERROR';
			}
			/*
			*Metodo: setDados()
			*Descrição: atualiza dados no banco
			*Data: 03/07/2024
			*Programador(a): Ighor Drummond
			*/
			private function setDados(){
				$Ret = false;

				try{
					$this->con->beginTransaction();
					$this->con->exec($this->Query);
					$this->con->commit();
					$Ret = true;
				}catch(\PDOException $e){
					echo $e->getMessage();
					$this->con->rollBack();
				}finally{
					return $Ret;
				}
			}
			/*
			*Metodo: getDados
			*Descrição: retorna dados de uma pesquisa SQL
			*Data: 03/07/2024
			*Programador(a): Ighor Drummond
			*/
			private function getDados(){
				$Ret = [];
				try{
					$this->stmt = $this->con->query($this->Query);
					$Ret = $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
				}catch(\PDOException $e){
					echo $e->getMessage();
				}finally{
					return $Ret;
				}
			}
			/*
			*Metodo: montaQuery(Opções)
			*Descrição: monta query desejada.
			*Data: 03/07/2024
			*Programador(a): Ighor Drummond
			*/
			private function montaQuery($Opc){
				if($Opc === 1){
					$this->Query =  "
						INSERT INTO cartoes(nome_cartao, numero_cartao, validade, cvv, id_cliente, id_ban)
						VALUES('$this->Nome', '$this->Numero', '$this->Validade', $this->Cvc, $this->IdCli, $this->IdBan)
					";
				}else if($Opc === 2){
					$this->Query =  "
						SELECT
							id as cliente
						FROM
							cliente
						WHERE
							email = '$this->Email'
					";					
				}else if($Opc === 3){
					$this->Query =  " 
						SELECT
							cd.id_card,
							cd.cvv,
							CONCAT(
								SUBSTRING(cd.numero_cartao, 1, 4), ' ',
								SUBSTRING(cd.numero_cartao, 5, 4), ' ',
								SUBSTRING(cd.numero_cartao, 9, 4), ' ',
								SUBSTRING(cd.numero_cartao, 13, 4)
							) AS numero_cartao,
							cd.nome_cartao,
							bd.nome_ban,
							bd.img_ban,
							DATE_FORMAT(cd.validade, '%m/%Y') AS validade_formatada
						FROM
							cartoes cd
						INNER JOIN 
							bandeiras bd ON bd.id_ban = cd.id_ban
						WHERE
							cd.id_cliente = $this->IdCli
					";
					$this->Query .= empty($this->IdCard) ? '' : " AND cd.id_card =  $this->IdCard ";
				}else if($Opc === 4){
					$this->Query =  "
						SELECT
							*
						FROM 
							cartoes
						WHERE
							id_cliente = $this->IdCli
							AND numero_cartao = '$this->Numero'
							AND cvv = $this->Cvc
					";
				}else if($Opc === 5){
					$this->Query = "
						DELETE FROM 
							cartoes
						WHERE 
							id_card = $this->IdCard
							AND id_cliente = $this->IdCli
						;
					";
				}
			}
		}
	}
?>