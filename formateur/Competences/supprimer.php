<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'formateur') {
    header('Location: ../../pagelogin/connexion/index.php');
    exit();
}
include("../../database/config.php");
if(isset($_GET["id"])){
    try{
        $req=$db->prepare("DELETE FROM competences WHERE id_competence=?");
        $r=$req->execute([$_GET["id"]]);
        if($r==false){
            header("Location: compe.php?error=Suppression a echoue");
        }else{
            header("Location: compe.php?msg=Suppression a reussi");
        }
    }catch(PDOException $e){
        echo "Erreur: ".$e->getMessage();
    }
}
?>