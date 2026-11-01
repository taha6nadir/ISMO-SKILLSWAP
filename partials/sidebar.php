<?php
$viewerRole = $viewerRole ?? ($_SESSION['role'] ?? null);
$base = $base ?? '';
$activeNav = $activeNav ?? '';

$menus = [
    'admin' => [
        'dashboard' => ['Tableau de bord', 'admin/tableaubord/table.php', 'home'],
        'users' => ['Utilisateurs', 'admin/Utulisateurs/utili.php', 'users'],
        'badges' => ['Badges', 'admin/Badges/badges.php', 'award'],
        'moderation' => ['Modération', 'admin/Moderation/mode.php', 'shield'],
        'stats' => ['Statistiques', 'admin/Statistiques/stat.php', 'chart'],
        'marketplace' => ['Marketplace', 'admin/Marketplace/market.php', 'cart'],
    ],
    'formateur' => [
        'dashboard' => ['Tableau de bord', 'formateur/tableaubord/tableaubord.php', 'home'],
        'validate' => ['Valider compétences', 'formateur/validecompetence/validecompetence.php', 'clipboard'],
        'skills' => ['Compétences', 'formateur/Competences/compe.php', 'tag'],
        'users' => ['Utilisateurs', 'formateur/utilisateur/index.php', 'users'],
        'badges' => ['Badges', 'formateur/badges/badge.php', 'award'],
        'marketplace' => ['Marketplace', 'formateur/marketplace/marketplace.php', 'cart'],
    ],
    'stagiaire' => [
        'dashboard' => ['Tableau de bord', 'stagiaire/tableaubord/index.php', 'home'],
        'requests' => ['Mes demandes', 'stagiaire/mes demandes/index.php', 'clipboard'],
        'skills' => ['Compétences', 'stagiaire/competences/index.php', 'tag'],
        'badges' => ['Badges', 'stagiaire/badges/index.php', 'award'],
        'passport' => ['Passeport', 'stagiaire/passeport/index.php', 'user'],
        'marketplace' => ['Marketplace', 'stagiaire/marketplace/index.php', 'cart'],
    ],
];
$menus['mentor'] = $menus['stagiaire'];
$menus['mentor'] = array_slice($menus['mentor'], 0, 3, true)
    + ['propositions' => ['Mes propositions', 'stagiaire/mes propositions/index.php', 'handshake']]
    + array_slice($menus['mentor'], 3, null, true);

if (!empty($publicNav)) {
    $items = [];
    $dashboard = [
        'admin' => 'admin/tableaubord/table.php',
        'formateur' => 'formateur/tableaubord/tableaubord.php',
        'stagiaire' => 'stagiaire/tableaubord/index.php',
        'mentor' => 'stagiaire/tableaubord/index.php',
    ];
    if (isset($dashboard[$viewerRole])) {
        $items['dashboard'] = ['Tableau de bord', $dashboard[$viewerRole], 'home'];
    }
    $items['search'] = ['Recherche', 'search.php', 'search'];
    $items['profiles'] = ['Profils publics', 'public_profile/public_profile.php', 'user'];
} else {
    $items = $menus[$viewerRole] ?? [];
}
?>
<aside class="sidebar">
    <div class="sidebar-logo">
        <span class="brand-mark" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3Z"/>
            </svg>
        </span>
        <span class="brand-text">ISMO<span>SkillSwap</span></span>
    </div>
    <nav class="sidebar-nav">
        <?php foreach ($items as $key => $item): ?>
            <a class="nav-item<?= $key === $activeNav ? ' active' : '' ?>" href="<?= e($base . $item[1]) ?>"
                aria-label="<?= e($item[0]) ?>" <?= $key === $activeNav ? 'aria-current="page"' : '' ?>>
                <span class="nav-icon"><?= icon($item[2]) ?></span>
                <span><?= e($item[0]) ?></span>
            </a>
        <?php endforeach; ?>
    </nav>
    <div class="sidebar-footer">
        <span class="sidebar-footer-dot"></span>
        <span>ISMO SkillSwap</span>
    </div>
</aside>
