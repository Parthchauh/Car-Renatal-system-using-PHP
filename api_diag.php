<?php
/**
 * Diagnostic tool to check Cars API health
 */
require_once __DIR__ . '/includes/db_connect.php';
require_once __DIR__ . '/includes/functions.php';

echo "<h1>🔍 Cars API Diagnostic</h1>";

try {
    $pdo = getDBConnection();
    echo "<p style='color:green;'>✅ Database Connected.</p>";
    
    // Check table structure
    $stmt = $pdo->query("DESCRIBE cars");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<h3>Table Structure: 'cars'</h3><ul>";
    $hasImage = false;
    foreach ($columns as $col) {
        $name = $col['Field'];
        if ($name === 'image') $hasImage = true;
        echo "<li>$name ({$col['Type']})</li>";
    }
    echo "</ul>";
    
    if (!$hasImage) {
        echo "<p style='color:red;'>❌ ERROR: 'image' column is missing from 'cars' table!</p>";
    } else {
        echo "<p style='color:green;'>✅ 'image' column exists.</p>";
    }

    // Check data
    $stmt = $pdo->query("SELECT COUNT(*) FROM cars");
    $count = $stmt->fetchColumn();
    echo "<p>Total cars in database: <strong>$count</strong></p>";

    // Test API Logic
    echo "<h3>Testing API Select Logic...</h3>";
    $sql = "SELECT c.*, b.name AS brand_name 
            FROM cars c 
            JOIN brands b ON c.brand_id = b.id 
            LIMIT 1";
    $stmt = $pdo->query($sql);
    $result = $stmt->fetch();
    
    if ($result) {
        echo "<p style='color:green;'>✅ Query successful. Example Car: {$result['model']}</p>";
        echo "<pre>" . print_r($result, true) . "</pre>";
    } else {
        echo "<p style='color:orange;'>⚠️ Query successful but returned 0 results. Is the table seeded?</p>";
    }

} catch (Exception $e) {
    echo "<p style='color:red;'>❌ FATAL ERROR: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
