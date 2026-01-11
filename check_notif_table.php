#!/usr/bin/env php
<?php
require 'config/database.php';

echo "=== Notifications Table Structure ===\n\n";
$stmt = $pdo->query('DESCRIBE notifications');
while($row = $stmt->fetch()) {
    echo str_pad($row['Field'], 20) . " " . $row['Type'] . "\n";
}
