<?php
require_once __DIR__ . '/includes/db_connect.php';
$pdo = getDBConnection();

try {
    $stmt = $pdo->query("DESCRIBE cars");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Columns in 'cars' table:\n";
    print_r($columns);
    
    if (!in_array('image', $columns)) {
        echo "\nERROR: 'image' column is MISSING!\n";
    } else {
        echo "\n'image' column exists.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
