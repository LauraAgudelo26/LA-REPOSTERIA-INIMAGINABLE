<?php
/**
 * Script para generar hashes de contraseñas
 * generar_hashes.php
 */

echo "<h2>Generador de Hashes de Contraseñas</h2>";

// Generar hashes para las contraseñas de prueba
$passwords = [
    'admin123' => password_hash('admin123', PASSWORD_DEFAULT),
    'cliente123' => password_hash('cliente123', PASSWORD_DEFAULT)
];

echo "<h3>Hashes generados:</h3>";
foreach ($passwords as $password => $hash) {
    echo "<p><strong>$password:</strong> $hash</p>";
}

echo "<h3>SQL para actualizar contraseñas:</h3>";
echo "<pre>";
echo "UPDATE cliente SET password = '{$passwords['admin123']}' WHERE email = 'admin@placeresocultos.com';\n";
echo "UPDATE cliente SET password = '{$passwords['cliente123']}' WHERE email = 'cliente@test.com';\n";
echo "</pre>";
?>
