<?php
$base = '../../';
require_once __DIR__ . '/../../partials/bootstrap.php';
require_login();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header("Location: marketplace.php?error=missing_id");
    exit();
}

$redir = match ($role) {
    'admin' => '../../admin/Marketplace/market.php',
    'mentor', 'stagiaire' => '../../stagiaire/marketplace/index.php',
    default => 'marketplace.php',
};

try {
    $req = $db->prepare("UPDATE aide SET `signal` = `signal` + 1 WHERE id_demande = ?");
    $req->execute([$id]);
    header("Location: $redir?msg=signal_success");
    exit();
} catch (PDOException $e) {
    header("Location: $redir?error=" . urlencode($e->getMessage()));
    exit();
}
?>
