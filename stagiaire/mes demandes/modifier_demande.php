<?php
$base = '../../';
require_once __DIR__ . '/../../partials/bootstrap.php';
require_role(['stagiaire', 'mentor']);
try {
    include('../../database/config.php');
    if (isset($_POST['modifier'])) {
        $id_demande = $_POST['id_demande'] ?? '';
        $titre = $_POST['titre'] ?? '';
        $description = $_POST['description'] ?? '';
        $tags = $_POST['tags'] ?? '';
            $stmt = $db->prepare("UPDATE aide SET titre = ?, description = ?, tags = ? WHERE id_demande = ? AND id_user = ?");
            $stmt->execute([$titre, $description, $tags, $id_demande, $id_user]);
            header('Location: index.php?message=Demande modifiée avec succès');
            exit();
        } else {
            header('Location: index.php?error=Aucune modification effectuée');
            exit();
        }
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        exit();
    }
?>
