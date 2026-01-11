<?php
// Diagnose users table structure on Railway
echo "=== DIAGNOSING USERS TABLE ===\n\n";

// Get database connection directly from environment variables
$host = getenv('DB_HOST') ?: 'mysql.railway.internal';
$dbname = getenv('DB_NAME') ?: 'railway';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';
$port = getenv('DB_PORT') ?: '3306';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Users table has " . count($columns) . " columns:\n\n";
    
    $has_middle_name = false;
    foreach ($columns as $col) {
        $marker = ($col['Field'] === 'middle_name') ? ' ← THIS ONE!' : '';
        echo "  {$col['Field']} ({$col['Type']}) {$col['Null']} {$col['Key']} {$col['Default']}{$marker}\n";
        if ($col['Field'] === 'middle_name') {
            $has_middle_name = true;
        }
    }
    
    echo "\n";
    if ($has_middle_name) {
        echo "✓ middle_name column EXISTS\n";
    } else {
        echo "✗ middle_name column MISSING - WILL ADD IT NOW!\n\n";
        $pdo->exec("ALTER TABLE users ADD COLUMN middle_name VARCHAR(255) NULL AFTER first_name");
        echo "✓ middle_name column ADDED!\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== DIAGNOSIS COMPLETE ===\n";
exit(0);
?>
