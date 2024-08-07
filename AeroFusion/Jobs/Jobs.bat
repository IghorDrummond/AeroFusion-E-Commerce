@echo off
REM Caminho para o executável do PHP
set PHP_PATH=C:\xampp\php\php.exe

REM Caminho para o script PHP
set SCRIPT_PATH=C:\Users\Drummond\Documents\FullStack\Gits\AeroFusion-E-Commerce\AeroFusion\Jobs\normalizaProdutos.php

REM Define o caminho base para o arquivo de log
set BASE_LOG_PATH=C:\Users\Drummond\Downloads\logfile

REM Obtém a data atual no formato YYYY-MM-DD
for /f "tokens=2 delims==" %%I in ('wmic os get localdatetime /value') do set datetime=%%I
set DATE=%datetime:~0,4%-%datetime:~4,2%-%datetime:~6,2%

REM Define o nome do arquivo de log com a data
set LOGFILE=%BASE_LOG_PATH%_%DATE%.log

REM Executa o script PHP e adiciona a saída e erros ao arquivo de log existente
%PHP_PATH% %SCRIPT_PATH% >> %LOGFILE% 2>&1
