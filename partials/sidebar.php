<?php
$viewerRole = $viewerRole ?? ($_SESSION['role'] ?? null);
$base = $base ?? '';
$activeNav = $activeNav ?? '';

$menus = [
    'admin' => [
        'dashboard' => ['Tableau de bord', 'admin/tableaubord/table.php', '🏠'],
        'users' => ['Utilisateurs', 'admin/Utulisateurs/utili.php', '👥'],
        'badges' => ['Badges', 'admin/Badges/badges.php', '🎖️'],
        'moderation' => ['Modération', 'admin/Moderation/mode.php', '🛡️'],
        'stats' => ['Statistiques', 'admin/Statistiques/stat.php', '📊'],
        'marketplace' => ['Marketplace', 'admin/Marketplace/market.php', '🛒'],
    ],
    'formateur' => [
        'dashboard' => ['Tableau de bord', 'formateur/tableaubord/tableaubord.php', '🏠'],
        'validate' => ['Valider compétences', 'formateur/validecompetence/validecompetence.php', '📋'],
        'skills' => ['Compétences', 'formateur/Competences/compe.php', '🏷️'],
        'users' => ['Utilisateurs', 'formateur/utilisateur/index.php', '👥'],
        'badges' => ['Badges', 'formateur/badges/badge.php', '🎖️'],
        'marketplace' => ['Marketplace', 'formateur/marketplace/marketplace.php', '🛒'],
    ],
    'stagiaire' => [
        'dashboard' => ['Tableau de bord', 'stagiaire/tableaubord/index.php', '🏠'],
        'requests' => ['Mes demandes', 'stagiaire/mes demandes/index.php', '📋'],
        'skills' => ['Compétences', 'stagiaire/competences/index.php', '🏷️'],
        'badges' => ['Badges', 'stagiaire/badges/index.php', '🎖️'],
        'passport' => ['Passeport', 'stagiaire/passeport/index.php', '👤'],
        'marketplace' => ['Marketplace', 'stagiaire/marketplace/index.php', '🛒'],
    ],
];
$menus['mentor'] = $menus['stagiaire'];
$menus['mentor'] = array_slice($menus['mentor'], 0, 3, true)
    + ['propositions' => ['Mes propositions', 'stagiaire/mes propositions/index.php', '🤝']]
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
        $items['dashboard'] = ['Tableau de bord', $dashboard[$viewerRole], '🏠'];
    }
    $items['search'] = ['Recherche', 'search.php', '🔍'];
    $items['profiles'] = ['Profils publics', 'public_profile/public_profile.php', '👤'];
} else {
    $items = $menus[$viewerRole] ?? [];
}
?>
<aside class="sidebar">
    <div class="sidebar-logo"><span>ISMO-SkillSwap</span></div>
    <nav class="sidebar-nav">
        <?php foreach ($items as $key => $item): ?>
            <div class="nav-item<?= $key === $activeNav ? ' active' : '' ?>" onclick="location.href='<?= e($base . $item[1]) ?>'">
                <span class="nav-icon"><?= $item[2] ?></span>
                <span><?= e($item[0]) ?></span>
            </div>
        <?php endforeach; ?>
    </nav>
</aside>
