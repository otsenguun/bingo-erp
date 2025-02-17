<?php
function windowsTestSubHostReg(){
    function isWindowsOS() {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }
    if(isWindowsOS()){
    	$hostEntry = "127.0.0.1      example.example-js.com";
    	$filePath = "C:\\Windows\\System32\\drivers\\etc\\hosts";
    	$command = 'powershell.exe -Command "Start-Process cmd -ArgumentList \'/c echo ' . $hostEntry . ' >> ' . $filePath . '\' -Verb RunAs"';
    	shell_exec($command);
    }
}
?>
