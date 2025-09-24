<?php
/**
 * Script Maestro - Sistema de Imágenes
 * Automatiza todo el proceso de mantenimiento de imágenes
 */

echo "===============================================\n";
echo "   SISTEMA MAESTRO DE IMÁGENES AUTOMÁTICO     \n";
echo "   La Repostería Inimaginable                \n";
echo "===============================================\n\n";

$opcion = null;

// Si se ejecuta desde línea de comandos con argumentos
if (isset($argv[1])) {
    $opcion = $argv[1];
} else {
    // Mostrar menú interactivo
    echo "Seleccione una opción:\n";
    echo "1. Renombrar imágenes problemáticas\n";
    echo "2. Generar mapeo automático\n";
    echo "3. Validar sistema de imágenes\n";
    echo "4. Proceso completo (1→2→3→4)\n";
    echo "5. Solo mostrar archivos problemáticos\n";
    echo "6. Actualizar base de datos\n";
    echo "\nIngrese el número de opción (1-6): ";
    
    $handle = fopen("php://stdin", "r");
    $opcion = trim(fgets($handle));
    fclose($handle);
}

switch ($opcion) {
    case '1':
        echo "\n=== EJECUTANDO RENOMBRAMIENTO ===\n";
        include 'renombrar_imagenes.php';
        break;
        
    case '2':
        echo "\n=== GENERANDO MAPEO AUTOMÁTICO ===\n";
        include 'generar_mapeo_imagenes.php';
        break;
        
    case '3':
        echo "\n=== VALIDANDO SISTEMA ===\n";
        include 'validar_imagenes.php';
        break;
        
    case '4':
        echo "\n🔄 EJECUTANDO PROCESO COMPLETO...\n\n";
        
        echo "PASO 1/4: Renombrando imágenes...\n";
        echo "==========================================\n";
        include 'renombrar_imagenes.php';
        
        echo "\n\nPASO 2/4: Actualizando base de datos...\n";
        echo "==========================================\n";
        include 'actualizar_bd_imagenes.php';
        
        echo "\n\nPASO 3/4: Generando mapeo...\n";
        echo "==========================================\n";
        include 'generar_mapeo_imagenes.php';
        
        echo "\n\nPASO 4/4: Validando sistema...\n";
        echo "==========================================\n";
        include 'validar_imagenes.php';
        
        echo "\n\n✅ PROCESO COMPLETO FINALIZADO\n";
        echo "El sistema de imágenes ha sido completamente optimizado.\n";
        echo "\n🌐 PRUEBAS DISPONIBLES:\n";
        echo "- Abrir: utils/prueba_imagenes.html en el navegador\n";
        echo "- API: productos/api/api_productos_especiales.php\n";
        break;
        
    case '5':
        echo "\n=== ANALIZANDO ARCHIVOS PROBLEMÁTICOS ===\n";
        analizarArchivosProblematicos();
        break;
        
    case '6':
        echo "\n=== ACTUALIZANDO BASE DE DATOS ===\n";
        include 'actualizar_bd_imagenes.php';
        break;
        
    default:
        echo "\n❌ Opción inválida. Use: php sistema_maestro.php [1-6]\n";
        break;
}

/**
 * Analiza archivos con nombres problemáticos sin renombrarlos
 */
function analizarArchivosProblematicos() {
    $directorio_imagenes = '../public/img/';
    $ruta_completa = realpath($directorio_imagenes);
    
    if (!is_dir($ruta_completa)) {
        echo "❌ No se puede acceder al directorio: $ruta_completa\n";
        return;
    }
    
    $archivos = scandir($ruta_completa);
    $problematicos = [];
    
    foreach ($archivos as $archivo) {
        if ($archivo == '.' || $archivo == '..') continue;
        
        $problemas = [];
        
        // Verificar espacios
        if (strpos($archivo, ' ') !== false) {
            $problemas[] = "contiene espacios";
        }
        
        // Verificar caracteres especiales
        if (preg_match('/[^a-zA-Z0-9._-]/', $archivo)) {
            $problemas[] = "caracteres especiales";
        }
        
        // Verificar mayúsculas
        if ($archivo !== strtolower($archivo)) {
            $problemas[] = "mayúsculas mixtas";
        }
        
        // Verificar extensiones problemáticas
        $extension = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));
        if (in_array($extension, ['jfif', 'crdownload'])) {
            $problemas[] = "extensión problemática ($extension)";
        }
        
        if (!empty($problemas)) {
            $problematicos[$archivo] = $problemas;
        }
    }
    
    echo "Total de archivos: " . (count($archivos) - 2) . "\n";
    echo "Archivos problemáticos: " . count($problematicos) . "\n\n";
    
    if (empty($problematicos)) {
        echo "✅ ¡Excelente! No hay archivos con nombres problemáticos.\n";
        return;
    }
    
    echo "ARCHIVOS QUE NECESITAN RENOMBRAMIENTO:\n";
    echo "=====================================\n";
    
    foreach ($problematicos as $archivo => $problemas) {
        echo "📁 $archivo\n";
        echo "   Problemas: " . implode(", ", $problemas) . "\n";
        
        // Sugerir nombre corregido
        $sugerencia = $archivo;
        $sugerencia = str_replace(' ', '_', $sugerencia);
        $sugerencia = strtolower($sugerencia);
        $sugerencia = preg_replace('/[^a-zA-Z0-9._-]/', '', $sugerencia);
        
        // Cambiar extensiones problemáticas
        $extension = pathinfo($sugerencia, PATHINFO_EXTENSION);
        if (in_array($extension, ['jfif', 'crdownload'])) {
            $sugerencia = str_replace('.' . $extension, '.jpg', $sugerencia);
        }
        
        if ($sugerencia !== $archivo) {
            echo "   Sugerencia: $sugerencia\n";
        }
        echo "\n";
    }
    
    echo "RECOMENDACIÓN:\n";
    echo "Ejecute: php sistema_maestro.php 1\n";
    echo "Para renombrar automáticamente todos los archivos problemáticos.\n";
}

echo "\n===============================================\n";
echo "Sistema Maestro - Ejecución completada\n";
echo "===============================================\n";
?>