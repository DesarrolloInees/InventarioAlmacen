@echo off

cd /d C:\xampp\htdocs\solicitudProsegur

"C:\xampp\php\php.exe" "C:\xampp\htdocs\solicitudProsegur\procesar_alertas_vencidas.php" >> "C:\xampp\htdocs\solicitudProsegur\logs\alertas.log" 2>&1