<?php
$base = '../../';
require_once __DIR__ . '/../../partials/bootstrap.php';
require_role(['formateur']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: badge.php");
    exit();
}

$idUser = isset($_POST['id_user']) ? (int) $_POST['id_user'] : 0;
$idBadge = isset($_POST['id_badge']) ? (int) $_POST['id_badge'] : 0;

if ($idUser <= 0 || $idBadge <= 0) {
    header("Location: badge.php?error=missing_params");
    exit();
}

try {
    $stmt = $db->prepare("SELECT 1 FROM obtention_badges WHERE id_user = ? AND id_badge = ?");
    $stmt->execute([$idUser, $idBadge]);
    if ($stmt->fetchColumn()) {
        header("Location: badge.php?error=" . urlencode("Le stagiaire a déjà ce badge."));
        exit();
    }

    $stmt = $db->prepare("INSERT INTO obtention_badges (id_user, id_badge, date_obtention) VALUES (?, ?, NOW())");
    $stmt->execute([$idUser, $idBadge]);

    $stmt = $db->prepare("UPDATE utilisateurs SET score = score + 100 WHERE id_user = ?");
    $stmt->execute([$idUser]);

    header("Location: badge.php?msg=" . urlencode("Badge attribué avec succès."));
    exit();
} catch (PDOException $e) {
    header("Location: badge.php?error=" . urlencode($e->getMessage()));
    exit();
}
?>
