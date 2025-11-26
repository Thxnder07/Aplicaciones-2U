<?php
/**
 * Script para generar hash de contraseña
 * Ejecutar desde línea de comandos: php app/utils/generar_hash_password.php
 * 
 * Uso: php generar_hash_password.php [password]
 * Si no se proporciona password, se usará 'admin123' por defecto
 */

$password = $argv[1] ?? 'admin123';

echo "========================================\n";
echo "Generador de Hash de Contraseña\n";
echo "========================================\n\n";
echo "Contraseña: " . $password . "\n";
echo "Hash generado: " . password_hash($password, PASSWORD_DEFAULT) . "\n\n";
echo "========================================\n";
echo "Para actualizar el admin en la BD, ejecuta:\n";
echo "UPDATE usuarios SET password = '[hash_generado]' WHERE email = 'admin@eventhub.com';\n";
echo "========================================\n";
?>

