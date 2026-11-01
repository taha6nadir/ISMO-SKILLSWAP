<?php
$base = $base ?? '';
$headerNom = $viewer['nom'] ?? '';
$headerPrenom = $viewer['prenom'] ?? '';
$headerPhoto = $viewer['photo'] ?? null;
$headerEmail = $viewer['email'] ?? '';
$headerRole = $viewerRole ?? 'Visiteur';
$viewerName = trim($headerNom . ' ' . $headerPrenom);
$viewerInitials = strtoupper(substr($headerNom, 0, 1) . substr($headerPrenom, 0, 1));
$searchValue = $_GET['q'] ?? '';
?>
<header class="header">
    <form class="header-search" action="<?= e($base) ?>search.php" method="GET">
        <svg class="header-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="7" />
            <line x1="21" y1="21" x2="16.65" y2="16.65" />
        </svg>
        <input name="q" class="header-search-input" type="search" placeholder="Rechercher..."
            value="<?= e($searchValue) ?>">
    </form>

    <div class="user-pill" role="button" tabindex="0" aria-label="Ouvrir le menu de profil"
        aria-expanded="false" aria-controls="profile-menu"
        data-email="<?= e($headerEmail) ?>" data-base="<?= e($base) ?>" data-user-id="<?= e($viewer['id_user'] ?? '') ?>">
        <div class="user-info">
            <div class="user-name"><?= e($viewerName !== '' ? $viewerName : 'Visiteur') ?></div>
            <div class="user-role"><?= e($headerRole) ?></div>
        </div>
        <?php if (!empty($headerPhoto)): ?>
            <img class="user-avatar" src="<?= e($base) ?>pagelogin/photo/<?= e($headerPhoto) ?>" alt="Photo de profil" loading="lazy" width="40" height="40">
        <?php elseif ($viewerName !== ''): ?>
            <div class="user-avatar"><?= e($viewerInitials) ?></div>
        <?php else: ?>
            <div class="user-avatar">?</div>
        <?php endif; ?>
    </div>
</header>
