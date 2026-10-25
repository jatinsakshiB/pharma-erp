' ==============================================================================
' DRXStore Silent Launcher (Windows)
' Runs the server completely hidden in background and opens default browser
' ==============================================================================
Set WshShell = CreateObject("WScript.Shell")
Set FSO = CreateObject("Scripting.FileSystemObject")
ScriptDir = FSO.GetParentFolderName(WScript.ScriptFullName)

WshShell.CurrentDirectory = ScriptDir
WshShell.Run "cmd.exe /c """ & ScriptDir & "\start.bat""", 0, False
