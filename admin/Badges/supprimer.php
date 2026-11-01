<?php
$base = '../../';
require_once __DIR__ . '/../../partials/bootstrap.php';
require_role(['admin']);

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header("Location: badges.php?error=missing_id");
    exit();
}

try {
    $req = $db->prepare("DELETE FROM badges WHERE id_badge = ?");
    $req->execute([$id]);
    header("Location: badges.php?msg=Suppression réussie");
    exit();
} catch (PDOException $e) {
    header("Location: badges.php?error=" . urlencode($e->getMessage()));
    exit();
}
?>
