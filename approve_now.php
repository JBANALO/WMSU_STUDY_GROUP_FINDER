<?php
require_once 'config/database.php';

echo "<h2>Admin Approval Page</h2>";

try {
    // Approve the user first
    $stmt = $pdo->prepare("UPDATE users SET status = 'approved' WHERE email = ?");
    $stmt->execute(['hz202305178@wmsu.edu.ph']);
    echo "<p style='color: green;'><strong>✓ User josiebanalo has been approved!</strong></p>";
    
    // Approve the study group
    $stmt = $pdo->prepare("UPDATE study_groups SET status = 'approved' WHERE id = ?");
    $stmt->execute([1]);
    echo "<p style='color: green;'><strong>✓ Study group IT312 has been approved!</strong></p>";
    
    echo "<p><a href='index.php?page=dashboard' style='color: #8B0000; text-decoration: none;'>&larr; Go to Dashboard</a></p>";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
