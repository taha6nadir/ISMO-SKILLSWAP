<?php
$base = '../../';
require_once __DIR__ . '/../../partials/bootstrap.php';
require_role(['mentor']);

if (!isset($_GET['id_demande'])) {
    header("Location: index.php?error=ID de demande manquant.");
    exit();
}

$id_demande = (int) $_GET['id_demande'];
$id_mentor = $id_user;

try {
    #verifier que la demande existe et est encore ouverte
    $stmt = $db->prepare("SELECT id_demande, id_user, status FROM aide WHERE id_demande = ?");
    $stmt->execute([$id_demande]);
    $demande = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$demande) {
        header("Location: index.php?error=Demande introuvable.");
        exit();
    }
    if ($demande['status'] !== 'ouvert') {
        header("Location: index.php?error=Cette demande n'est plus ouverte.");
        exit();
    }
    if ((int) $demande['id_user'] === (int) $id_mentor) {
        header("Location: index.php?error=Vous ne pouvez pas proposer votre aide sur votre propre demande.");
        exit();
    }

    #verifier si le mentor a deja proposé son aide
    $stmt = $db->prepare("SELECT 1 FROM propositions_aide WHERE id_user = ? AND id_demande = ?");
    $stmt->execute([$id_mentor, $id_demande]);
    if ($stmt->fetchColumn()) {
        header("Location: index.php?error=Vous avez déjà proposé votre aide pour cette demande.");
        exit();
    }

    #proposer l'aide
    $stmt = $db->prepare("INSERT INTO propositions_aide ( id_user,`status`, id_demande, date_prop) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$id_mentor, "en_attente", $id_demande]);

    header("Location: index.php?msg=Votre aide a été proposée avec succès.");
    exit();
} catch (PDOException $e) {
    echo "Erreur: " . $e->getMessage();
    exit();
}
?>
