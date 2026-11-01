<?php
$base = '../../';
require_once __DIR__ . '/../../partials/bootstrap.php';
require_role(['stagiaire', 'mentor']);

$id_badge = isset($_GET['id_badge']) ? (int) $_GET['id_badge'] : 0;
if ($id_badge <= 0) {
    header('Location: index.php?error=Badge introuvable.');
    exit();
}

try {
    include('../../database/config.php');

    $stmt = $db->prepare("SELECT id_badge, points_requis FROM badges WHERE id_badge = ?");
    $stmt->execute([$id_badge]);
    $badge = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$badge) {
        header('Location: index.php?error=Badge introuvable.');
        exit();
    }

    $stmt = $db->prepare("SELECT 1 FROM obtention_badges WHERE id_user = ? AND id_badge = ?");
    $stmt->execute([$id_user, $id_badge]);
    if ($stmt->fetchColumn()) {
        header('Location: index.php?error=Vous possédez déjà ce badge.');
        exit();
    }

    $stmt = $db->prepare("SELECT score FROM utilisateurs WHERE id_user = ?");
    $stmt->execute([$id_user]);
    $score = (int) $stmt->fetchColumn();
    if ($score < (int) $badge['points_requis']) {
        header('Location: index.php?error=Points insuffisants pour réclamer ce badge.');
        exit();
    }

    $stmt = $db->prepare("INSERT INTO obtention_badges (id_user, id_badge, date_obtention) VALUES (?, ?, NOW())");
    $stmt->execute([$id_user, $id_badge]);

    #ajouter 50 points quand l'utilisateur obtient un nouveau badge
    $stmt = $db->prepare("UPDATE utilisateurs SET score = score + 50 WHERE id_user = ?");
    $stmt->execute([$id_user]);

    header("Location: index.php?msg=Badge réclamé avec succès ! Vous avez gagné 50 points.");
    exit();
} catch (Exception $e) {
    header("Location: index.php?error=" . urlencode("Erreur : " . $e->getMessage()));
    exit();
}
?>
