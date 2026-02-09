<?php
// Test de la API de documentos
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== TEST API DOCUMENTOS ===\n\n";

// Test 1: Verificar archivos
echo "1. Verificando archivos requeridos...\n";
$files = [
    '../../config/config.php',
    '../../includes/autoload.php',
    '../../includes/helpers.php',
    '../../includes/auth.php',
    '../../models/DocumentoServicio.php'
];

foreach ($files as $file) {
    $exists = file_exists($file);
    echo "   - $file: " . ($exists ? "✓ existe" : "✗ NO EXISTE") . "\n";
}

// Test 2: Cargar archivos
echo "\n2. Cargando configuración...\n";
try {
    require_once '../../config/config.php';
    echo "   ✓ config.php cargado\n";

    require_once '../../includes/autoload.php';
    echo "   ✓ autoload.php cargado\n";

    require_once '../../includes/helpers.php';
    echo "   ✓ helpers.php cargado\n";

    require_once '../../includes/auth.php';
    echo "   ✓ auth.php cargado\n";
} catch (Exception $e) {
    echo "   ✗ ERROR: " . $e->getMessage() . "\n";
    exit;
}

// Test 3: Verificar clase
echo "\n3. Verificando clase DocumentoServicio...\n";
if (class_exists('DocumentoServicio')) {
    echo "   ✓ Clase DocumentoServicio existe\n";

    // Test 4: Instanciar
    echo "\n4. Intentando instanciar...\n";
    try {
        $doc = new DocumentoServicio($pdo);
        echo "   ✓ Instancia creada exitosamente\n";

        // Test 5: Llamar método
        echo "\n5. Probando método obtenerPorServicio...\n";
        $result = $doc->obtenerPorServicio('hosting', 2);
        echo "   ✓ Método ejecutado\n";
        echo "   Documentos encontrados: " . count($result) . "\n";

        if (count($result) > 0) {
            echo "\n   Primer documento:\n";
            print_r($result[0]);
        }
    } catch (Exception $e) {
        echo "   ✗ ERROR: " . $e->getMessage() . "\n";
        echo "   Trace: " . $e->getTraceAsString() . "\n";
    }
} else {
    echo "   ✗ Clase DocumentoServicio NO existe\n";
    echo "   Clases cargadas: " . implode(', ', get_declared_classes()) . "\n";
}

echo "\n=== FIN TEST ===\n";
