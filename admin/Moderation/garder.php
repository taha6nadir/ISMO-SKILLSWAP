<?php
$base = '../../';
require_once __DIR__ . '/../../partials/bootstrap.php';
require_role(['admin']);

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header("Location: mode.php?error=missing_id");
    exit();
}

try {
    $req = $db->prepare("UPDATE aide SET `signal` = 0 WHERE id_demande = ?");
    $req->execute([$id]);
    header("Location: mode.php?msg=keep_success");
    exit();
} catch (PDOException $e) {
    header("Location: mode.php?error=" . urlencode($e->getMessage()));
    exit();
}
?>
