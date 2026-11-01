<?php
$base = '../../';
require_once __DIR__ . '/../../partials/bootstrap.php';
require_role(['formateur']);

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header("Location: compe.php?error=missing_id");
    exit();
}

try {
    $req = $db->prepare("DELETE FROM competences WHERE id_competence = ?");
    $req->execute([$id]);
    header("Location: compe.php?msg=Suppression réussie");
    exit();
} catch (PDOException $e) {
    header("Location: compe.php?error=" . urlencode($e->getMessage()));
    exit();
}
?>
