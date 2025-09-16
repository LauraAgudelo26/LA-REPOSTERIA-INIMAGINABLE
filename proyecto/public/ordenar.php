<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $producto = $_POST['producto'];
    $precio = $_POST['precio'];

    // Aquí puedes guardar en la base de datos
    // Ejemplo simple:
    echo "Orden de $producto por $$precio recibida correctamente.";
}
?>
