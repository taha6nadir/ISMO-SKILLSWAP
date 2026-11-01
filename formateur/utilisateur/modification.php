<?php
$base = '../../';
require_once __DIR__ . '/../../partials/bootstrap.php';
require_role(['formateur']);

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$role = $_GET['role'] ?? '';
$rolesAutorises = ['stagiaire', 'mentor'];

if ($id <= 0) {
    header("Location: index.php?error=missing_id");
    exit();
}
if (!in_array($role, $rolesAutorises, true)) {
    header("Location: index.php?error=invalid_role");
    exit();
}

try {
    $req = $db->prepare("UPDATE utilisateurs SET role = ? WHERE id_user = ? AND role IN ('stagiaire', 'mentor')");
    $req->execute([$role, $id]);
    if ($req->rowCount() > 0) {
        header("Location: index.php?success=role_updated");
    } else {
        header("Location: index.php?error=update_failed");
    }
    exit();
} catch (PDOException $e) {
    header("Location: index.php?error=" . urlencode($e->getMessage()));
    exit();
}
?>
