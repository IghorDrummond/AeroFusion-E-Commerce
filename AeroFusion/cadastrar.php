<?php
	require_once('lib/conta.php');
	use Cadastro\Cadastrar;//Importa as classe de cadastros de usuários
	//Declaração de Variaveis
	//String
	$Email = $_GET['Email'];
	$Senha = $_GET['Senha'];
	$ConfirmeSenha = $_GET['ConfirmaSenha'];
	$Nome = mb_strtoupper($_GET['Nome'], 'UTF-8');
	$Sobrenome = mb_strtoupper($_GET['Sobrenome'], 'UTF-8');
	$Data = $_GET['Data'];
	$Cep = $_GET['Cep'];
	$Cidade = mb_strtoupper($_GET['Cidade'], 'UTF-8');
	$Uf = mb_strtoupper($_GET['Estado'], 'UTF-8');
	$Rua = mb_strtoupper($_GET['Rua'], 'UTF-8');
	$Bairro = mb_strtoupper($_GET['Bairro'], 'UTF-8');
	$Numero = $_GET['Numero'];
	$Complemento = mb_strtoupper($_GET['Complemento'], 'UTF-8');
	$Referencia = mb_strtoupper($_GET['Referencia'], 'UTF-8');
	$Genero = $_GET['Sexo'];
	$Celular = $_GET['Celular'];
	$Cpf = $_GET['Cpf'];
	$ret = '';
	//Objeto
	$Cadastrar = null;

	//--Escopo
	//Instancia objeto 
	$Cadastrar = new Cadastrar(Email: $Email, Senha: $Senha, ConfirmeSenha: $ConfirmeSenha, Genero: $Genero, Celular: $Celular);
	//Cadastra Dados Principais
	$Cadastrar->setDados($Nome, $Sobrenome, $Data);
	//Cadastra Endereço 
	$Cadastrar->setEndereco($Cep, $Cidade, $Uf, $Rua, $Bairro, $Numero, $Complemento, $Referencia);
	//Informa o Cpf
	$Cadastrar->setCpf($Cpf);
	//Envia dados para o banco
	print($Cadastrar->enviar());
?>	