<?php
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_id']) && ($_SESSION['is_admin'] ?? false) === true;
}

function isGroupCreator($pdo, $group_id, $user_id) {
    $stmt = $pdo->prepare("SELECT creator_id FROM study_groups WHERE id = ?");
    $stmt->execute([$group_id]);
    $group = $stmt->fetch();
    return $group && $group['creator_id'] == $user_id;
}

function isGroupMember($pdo, $group_id, $user_id) {
    $stmt = $pdo->prepare("SELECT id FROM group_members WHERE group_id = ? AND user_id = ?");
    $stmt->execute([$group_id, $user_id]);
    return $stmt->fetch() !== false;
}

function redirectTo($page) {
    header("Location: index.php?page=" . $page);
    exit();
}

function showAlert($type, $message) {
    $_SESSION[$type] = $message;
}
?>
