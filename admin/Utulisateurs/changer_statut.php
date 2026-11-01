<?php
$base = '../../';
require_once __DIR__ . '/../../partials/bootstrap.php';
require_role(['admin']);

$statutsAutorises = ['actif', 'en_attente', 'suspendu'];
$statut = $_GET['statut'] ?? '';
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0 || !in_array($statut, $statutsAutorises, true)) {
    header("Location: utili.php?merr=Paramètres invalides");
    exit();
}

try {
    $req = $db->prepare('UPDATE utilisateurs SET statut = ? WHERE id_user = ?');
    $req->execute([$statut, $id]);
    header("Location: utili.php?msucc=Statut modifié avec succès");
    exit();
} catch (PDOException $e) {
    header("Location: utili.php?merr=" . urlencode($e->getMessage()));
    exit();
}
?>
