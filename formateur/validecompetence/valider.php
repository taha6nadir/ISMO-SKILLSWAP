<?php
$base = '../../';
require_once __DIR__ . '/../../partials/bootstrap.php';
require_role(['formateur']);

$idUser = isset($_GET['iduser']) ? (int) $_GET['iduser'] : 0;
$idCompetence = isset($_GET['idcompetence']) ? (int) $_GET['idcompetence'] : 0;

if ($idUser <= 0 || $idCompetence <= 0) {
    header("Location: validecompetence.php?error=missing_params");
    exit();
}

try {
    $stmt = $db->prepare("UPDATE validation_competence SET status = 'validee', date_validation = NOW(), id_validateur = ? WHERE id_user = ? AND id_competence = ?");
    $stmt->execute([$id_user, $idUser, $idCompetence]);

    if ($stmt->rowCount() > 0) {
        $stmt = $db->prepare("UPDATE utilisateurs SET score = score + 100 WHERE id_user = ?");
        $stmt->execute([$idUser]);
        header("Location: validecompetence.php?msg=valide_success");
    } else {
        header("Location: validecompetence.php?error=update_failed");
    }
    exit();
} catch (PDOException $e) {
    header("Location: validecompetence.php?error=" . urlencode($e->getMessage()));
    exit();
}
?>
