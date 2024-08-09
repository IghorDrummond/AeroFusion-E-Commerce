@echo off
REM Caminho para o executável do PHP
set PHP_PATH=C:\xampp\php\php.exe

REM Caminho para o script PHP
set SCRIPT_PATH=C:\Users\Drummond\Documents\FullStack\Gits\AeroFusion-E-Commerce\AeroFusion\Jobs\AtualizaPedidos.php

REM Define o caminho base para o arquivo de log
set BASE_LOG_PATH=C:\Users\Drummond\Downloads

REM Obtém a data atual no formato YYYY-MM-DD
for /f "tokens=2 delims==" %%I in ('wmic os get localdatetime /value') do set datetime=%%I
set DATE=%datetime:~0,4%-%datetime:~4,2%-%datetime:~6,2%

REM Define o nome do arquivo de log com a data
set LOGFILE=%BASE_LOG_PATH%\log_%DATE%.txt

REM Verifica se o arquivo de log já existe e, se não existir, cria um
if not exist %LOGFILE% (
    echo Criando arquivo de log para %DATE% >> %LOGFILE%
)

REM Adiciona uma linha ao log com a data e hora da execução
echo. >> %LOGFILE%
echo ============================= >> %LOGFILE%
echo Log gerado em %DATE% %TIME% >> %LOGFILE%
echo ============================= >> %LOGFILE%

REM Executa o script PHP e redireciona a saída e erros para o arquivo de log
%PHP_PATH% %SCRIPT_PATH% >> %LOGFILE% 2>&1

REM Exibe a saída do PHP no console também
type %LOGFILE%

pause
