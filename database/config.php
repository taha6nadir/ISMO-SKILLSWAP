<?php
if (!isset($db) || !($db instanceof PDO)) {
    try {
        $db = new PDO("mysql:host=127.0.0.1;dbname=skillswaps", "root", "");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "erreur de connexion:" . $e->getMessage();
    }
}
?>
