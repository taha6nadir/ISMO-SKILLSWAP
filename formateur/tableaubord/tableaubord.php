<?php
$base = '../../';
require_once __DIR__ . '/../../partials/bootstrap.php';
require_role(['formateur']);

include("../../database/config.php");
try {
    $stagiairesActifs = $db->query("SELECT COUNT(*) FROM utilisateurs WHERE (role='stagiaire' OR role='mentor') AND statut='actif'")->fetchColumn();
    $competencesEnAttente = $db->query("SELECT COUNT(*) FROM validation_competence WHERE status='en_attente'")->fetchColumn();
    $aidesEchangees = $db->query("SELECT COUNT(*) FROM aide_effectuee")->fetchColumn();
    $aidesSemaine = $db->query("SELECT COUNT(*) FROM aide_effectuee WHERE date_intervention >= DATE_SUB(CURRENT_DATE(), INTERVAL 7 DAY)")->fetchColumn();

    $competencesAValider = $db->query("SELECT vc.*, u.nom, u.prenom, c.nom AS nom_competence, vc.niveau FROM validation_competence vc JOIN utilisateurs u ON vc.id_user = u.id_user JOIN competences c ON vc.id_competence = c.id_competence WHERE vc.status='en_attente' ORDER BY vc.date_demande DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

    $topStagiaires = $db->query("SELECT nom, prenom, score, filiere FROM utilisateurs WHERE statut='actif' AND (role='stagiaire' OR role='mentor') ORDER BY score DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

    $competencesDemandees = $db->query("SELECT c.nom, COUNT(vc.id_competence) AS total FROM competences c JOIN validation_competence vc ON vc.id_competence = c.id_competence GROUP BY c.id_competence ORDER BY total DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    $maxDemande = $competencesDemandees ? max(array_column($competencesDemandees, 'total')) : 1;
} catch (PDOException $e) {
    echo "Erreur: " . $e->getMessage();
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Sora:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../../1-css/style.css">
    <link rel="stylesheet" href="tableaubord.css">
    <title>Document</title>

</head>

<body>
    <div class="layout">
        <?php
$base = '../../';
$activeNav = 'dashboard';
?>
<?php include __DIR__ . '/../../partials/sidebar.php'; ?>
<?php include __DIR__ . '/../../partials/header.php'; ?>

        <main class="main">
            <div class="page-title">
                <h1 class="texte-title">Tableau de bord Formateur</h1>
                <p class="eyebrow">Vue d'ensemble de l'activité des stagiaires</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Stagiaires actifs</h3>
                    <div class="val"><?php echo $stagiairesActifs; ?></div>
                    <svg class="card-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                    </svg>
                </div>
                <div class="stat-card">
                    <h3>Compétences en attente</h3>
                    <div class="val"><?php echo $competencesEnAttente; ?></div>
                    <svg class="card-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z" />
                    </svg>
                </div>
                <div class="stat-card">
                    <h3>Aides échangées</h3>
                    <div class="val"><?php echo $aidesEchangees; ?> <span class="trend">+<?php echo $aidesSemaine; ?>
                            cette semaine</span></div>
                    <svg class="card-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <polyline points="23 6 13.5 15.5 8.5 10.5 1 18" />
                    </svg>
                </div>
            </div>

            <div class="panels-grid">
                <div class="panel">
                    <div class="panel-h">
                        <h2>Compétences à valider</h2><a href="../validecompetence/validecompetence.php"
                            style="color:var(--accent); text-decoration:none; font-size:13px; font-weight:600;">Voir
                            tout</a>
                    </div>
                    <?php if (count($competencesAValider) == 0): ?>
                        <div style="text-align:center;padding:24px;color:#94a3b8;">Aucune compétence en attente</div>
                    <?php else: ?>
                        <?php foreach ($competencesAValider as $c): ?>
                            <div class="item-box">
                                <div class="item-top">
                                    <span style="font-weight:600;"><?php echo htmlspecialchars($c['nom_competence']); ?></span>
                                    <span class="tag"><?php echo htmlspecialchars($c['niveau'] ?? 'N/A'); ?></span>
                                </div>
                                <span
                                    style="color:#94a3b8; font-size:13px;"><?php echo htmlspecialchars($c['prenom'] . ' ' . $c['nom']); ?></span>
                                <div class="btn-group">
                                    <a href="../validecompetence/valider.php?iduser=<?php echo $c['id_user']; ?>&idcompetence=<?php echo $c['id_competence']; ?>&id_validateur=<?php echo $_SESSION['id_user']; ?>"
                                        class="btn-action"
                                        style="background:#22c55e;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;">✓</a>
                                    <a href="../validecompetence/refuser.php?iduser=<?php echo $c['id_user']; ?>&idcompetence=<?php echo $c['id_competence']; ?>&id_validateur=<?php echo $_SESSION['id_user']; ?>"
                                        class="btn-action"
                                        style="background:#ef4444;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;">✕</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="panel">
                    <div class="panel-h">
                        <h2>Top Stagiaires</h2>
                    </div>
                    <div style="display:flex; flex-direction:column; gap:16px;">
                        <?php if (count($topStagiaires) == 0): ?>
                            <div style="text-align:center;padding:24px;color:#94a3b8;">Aucun stagiaire</div>
                        <?php else: ?>
                            <?php foreach ($topStagiaires as $s): ?>
                                <div style="display:flex; justify-content:space-between; align-items:center;">
                                    <div style="display:flex; align-items:center; gap:12px;">
                                        <div
                                            style="width:34px; height:34px; border-radius:50%; background:var(--accent); color:white; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700;">
                                            <?php echo substr($s['prenom'], 0, 1) . substr($s['nom'], 0, 1); ?>
                                        </div>
                                        <div>
                                            <div style="font-size:14px; font-weight:600;">
                                                <?php echo htmlspecialchars($s['prenom'] . ' ' . $s['nom']); ?>
                                            </div>
                                            <div style="font-size:11px; color:#94a3b8;">
                                                <?php echo htmlspecialchars($s['filiere'] ?? 'N/A'); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <span
                                        style="color:var(--accent); font-weight:700;"><?php echo (int) $s['score']; ?>pts</span>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="panel">
                    <div class="panel-h">
                        <h2>Compétences demandées</h2>
                    </div>
                    <div style="display:flex; flex-direction:column; gap:10px;">
                        <?php if (count($competencesDemandees) == 0): ?>
                            <div style="text-align:center;padding:24px;color:#94a3b8;">Aucune donnée</div>
                        <?php else: ?>
                            <?php foreach ($competencesDemandees as $c): ?>
                                <div class="item-box" style="display:flex; justify-content:space-between; align-items:center;">
                                    <span style="font-weight:600;font-size:14px;"><?php echo htmlspecialchars($c['nom']); ?></span>
                                    <span class="tag" style="margin:0;"><?php echo (int)$c['total']; ?> demande<?php echo (int)$c['total'] > 1 ? 's' : ''; ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="../../2-script/profile-menu.js"></script>
    
    <script src="tableaubord.js"></script>
</body>

</html>

</html>