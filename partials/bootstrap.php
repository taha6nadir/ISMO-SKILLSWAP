<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../database/config.php';
require_once __DIR__ . '/icons.php';

$viewer = null;
if (!empty($_SESSION['id_user']) && isset($db)) {
    try {
        $stmtViewer = $db->prepare("SELECT id_user, nom, prenom, email, role, score, photo, filiere, statut, note_moyenne, date_inscription FROM utilisateurs WHERE id_user = ?");
        $stmtViewer->execute([$_SESSION['id_user']]);
        $viewer = $stmtViewer->fetch(PDO::FETCH_ASSOC) ?: null;
    } catch (PDOException $e) {
        $viewer = null;
    }
}

if ($viewer) {
    foreach (['id_user', 'nom', 'prenom', 'email', 'role', 'score', 'photo', 'filiere', 'statut', 'note_moyenne', 'date_inscription'] as $field) {
        ${$field} = $viewer[$field];
    }
    $_SESSION['role'] = $viewer['role'];
    $_SESSION['nom'] = $viewer['nom'];
    $_SESSION['prenom'] = $viewer['prenom'];
    $_SESSION['email'] = $viewer['email'];
    $_SESSION['photo'] = $viewer['photo'];
    $_SESSION['filiere'] = $viewer['filiere'];
    $_SESSION['statut'] = $viewer['statut'];
    $_SESSION['note_moyenne'] = $viewer['note_moyenne'];
    $_SESSION['date_inscription'] = $viewer['date_inscription'];
} else {
    $id_user = $_SESSION['id_user'] ?? null;
    $nom = $_SESSION['nom'] ?? '';
    $prenom = $_SESSION['prenom'] ?? '';
    $email = $_SESSION['email'] ?? '';
    $role = $_SESSION['role'] ?? null;
    $score = $_SESSION['score'] ?? 0;
    $photo = $_SESSION['photo'] ?? null;
    $filiere = $_SESSION['filiere'] ?? '';
    $statut = $_SESSION['statut'] ?? null;
    $note_moyenne = $_SESSION['note_moyenne'] ?? 0;
    $date_inscription = $_SESSION['date_inscription'] ?? null;
}

$viewerRole = $role ?? null;

if (!function_exists('e')) {
    function e($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('require_login')) {
    function require_login()
    {
        global $base;
        if (empty($_SESSION['id_user'])) {
            header('Location: ' . ($base ?? '') . 'pagelogin/connexion/index.php');
            exit();
        }
    }
}

if (!function_exists('require_role')) {
    function require_role($roles)
    {
        global $base, $viewerRole;
        require_login();
        if (!in_array($viewerRole, (array) $roles, true)) {
            header('Location: ' . ($base ?? '') . 'pagelogin/connexion/index.php');
            exit();
        }
    }
}
