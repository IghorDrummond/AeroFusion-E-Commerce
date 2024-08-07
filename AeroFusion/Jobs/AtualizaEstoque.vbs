Set objFSO = CreateObject("Scripting.FileSystemObject")
Set WshShell = CreateObject("WScript.Shell")

' Caminho para o arquivo de bloqueio
lockFilePath = "C:\Users\Drummond\Downloads\lockfile.lock"

' Verifica se o arquivo de bloqueio já existe
If Not objFSO.FileExists(lockFilePath) Then
    ' Cria o arquivo de bloqueio
    Set lockFile = objFSO.CreateTextFile(lockFilePath, True)
    lockFile.WriteLine "locked"
    lockFile.Close

    ' Executa o script BAT
    WshShell.Run """C:\Users\Drummond\Downloads\Jobs.bat""", 0, True

    ' Remove o arquivo de bloqueio após a execução
    objFSO.DeleteFile(lockFilePath)
Else
    ' Se o arquivo de bloqueio existir, exibe uma mensagem e sai
    WScript.Echo "O script já está em execução."
End If