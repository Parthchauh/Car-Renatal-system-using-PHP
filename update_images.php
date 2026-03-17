<?php
/**
 * Update existing car images with high-quality URLs
 */
require_once __DIR__ . '/includes/db_connect.php';
$pdo = getDBConnection();


$images = [
    'Camry' => 'https://images.pexels.com/photos/170811/pexels-photo-170811.jpeg',
    'RAV4' => 'https://images.unsplash.com/photo-1459356979461-dae1b8dcb07b',
    '3 Series' => 'https://images.unsplash.com/photo-1461632830798-3adb3034e4c8',
    'X5' => 'https://images.pexels.com/photos/358070/pexels-photo-358070.jpeg',
    'C-Class' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9',
    'GLE' => 'https://images.pexels.com/photos/358489/pexels-photo-358489.jpeg',
    'Civic' => 'https://images.unsplash.com/photo-1502877338535-766e1452684a',
    'Model 3' => 'https://images.unsplash.com/photo-1503736334956-4c8f8e92946d',
    'Model Y' => 'https://images.unsplash.com/photo-1503376780353-7e6692767b70',
    'A4' => 'https://images.unsplash.com/photo-1511390835673-02e273e6b0e7',
    'Mustang' => 'https://images.unsplash.com/photo-1549921296-a0108b3a0664',
    'Tucson' => 'https://images.pexels.com/photos/1707826/pexels-photo-1707826.jpeg'
];

try {
    $pdo->beginTransaction();
    foreach ($images as $model => $url) {
        $stmt = $pdo->prepare("UPDATE cars SET image = ? WHERE model = ?");
        $stmt->execute([$url, $model]);
        echo "Updated $model with $url\n";
    }
    $pdo->commit();
    echo "\nAll images updated successfully!";
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Error: " . $e->getMessage();
}
