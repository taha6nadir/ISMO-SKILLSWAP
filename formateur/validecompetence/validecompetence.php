<?php
$base = '../../';
require_once __DIR__ . '/../../partials/bootstrap.php';
require_role(['formateur']);

$nbdemande = 0;
$validecemois = 0;
$refusecemois = 0;
$validations = [];
try {
    include("../../database/config.php");
    $nbdemande = $db->query("SELECT COUNT(*) FROM validation_competence WHERE status = 'en_attente'")->fetchColumn();
    $validecemois = $db->query("SELECT COUNT(*) FROM validation_competence WHERE status = 'validee' AND MONTH(date_validation) = MONTH(CURRENT_DATE()) AND YEAR(date_validation) = YEAR(CURRENT_DATE())")->fetchColumn();
    $refusecemois = $db->query("SELECT COUNT(*) FROM validation_competence WHERE status = 'refusee' AND MONTH(date_validation) = MONTH(CURRENT_DATE()) AND YEAR(date_validation) = YEAR(CURRENT_DATE())")->fetchColumn();
} catch (PDOException $e) {
    echo "Erreur: " . $e->getMessage();
}
include("../../database/config.php");
try {
    #on récupère les compétences en attente de validation avec les informations de l'utilisateur le nom prenom et la photo
    #et aussi le nom de la compétence de la table competences
    $stmt = $db->query("SELECT vc.*, u.nom, u.prenom, u.photo, c.nom AS nom_competence,u.filiere  AS filiere FROM validation_competence vc JOIN utilisateurs u ON vc.id_user = u.id_user JOIN competences c ON vc.id_competence = c.id_competence WHERE vc.status = 'en_attente'");
    $stmt->execute();
    $validations = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Erreur: " . $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="../../1-css/style.css">
    <link rel="stylesheet" href="validecompetence.css">
    <title>Validation des compétences | ISMO-SkillSwap</title>
</head>

<body>
    <div class="layout">
        <?php
$base = '../../';
$activeNav = 'validate';
?>
<?php include __DIR__ . '/../../partials/sidebar.php'; ?>
<?php include __DIR__ . '/../../partials/header.php'; ?>

        <main class="main">
            <div class="page-title">
                <h1>Validation des compétences</h1>
                <p class="eyebrow">Validez ou refusez les déclarations de compétences des stagiaires</p>
            </div>

                <div class="search-section">
                    <div class="search-wrapper">
                        <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8" />
                            <path d="M21 21l-4.35-4.35" />
                        </svg>
                        <input type="text" id="searchInput" placeholder="Rechercher par stagiaire ou compétence...">
                    </div>
                    <select class="filiere-select" id="filiereFilter">
                        <option value="">Toutes les filières</option>
                        <?php
                        $filieres = $db->query("SELECT DISTINCT filiere FROM utilisateurs WHERE filiere IS NOT NULL AND filiere != ''");
                        foreach ($filieres as $f) {
                            echo '<option value="' . htmlspecialchars($f['filiere']) . '">' . htmlspecialchars($f['filiere']) . '</option>';
                        }
                        ?>
                    </select>
                </div>

            <div class="stats-row">
                <div class="stat-card-v">
                    <div class="stat-v-num"><?php echo $nbdemande; ?></div>
                    <div class="stat-v-label">En attente de validation</div>
                </div>
                <div class="stat-card-v">
                    <div class="stat-v-num"><?php echo $validecemois; ?></div>
                    <div class="stat-v-label">Validées ce mois</div>
                </div>
                <div class="stat-card-v red-stat">
                    <div class="stat-v-num"><?php echo $refusecemois; ?></div>
                    <div class="stat-v-label">Refusées ce mois</div>
                </div>
            </div>

            <div class="validation-container">

                <?php if (count($validations) == 0): ?>
                    <div class="empty-state"><?= icon('clipboard') ?> Aucune compétence en attente de validation.</div>
                <?php else: ?>
                    <?php foreach ($validations as $validation): ?>
                        <div class="v-card-item" data-nom="<?php echo htmlspecialchars(strtolower($validation['prenom'] . ' ' . $validation['nom'])); ?>" data-competence="<?php echo htmlspecialchars(strtolower($validation['nom_competence'])); ?>" data-filiere="<?php echo htmlspecialchars($validation['filiere']); ?>">
                            <div class="v-card-header">
                                <?php
                                if ($validation['photo']) {
                                    echo '<img class="v-avatar" src="../../pagelogin/photo/' . htmlspecialchars($validation['photo']) . '" alt="Photo de profil" loading="lazy" decoding="async" width="45" height="45">';
                                } else {
                                    echo '<div class="v-avatar">' . substr($validation['nom'], 0, 1) . substr($validation['prenom'], 0, 1) . '</div>';
                                }
                                ?>
                                <div class="v-user-details">
                                    <div class="v-skill-line">
                                        <span
                                            class="v-skill-name"><?php echo htmlspecialchars($validation['nom_competence']); ?></span>
                                    </div>
                                    <div class="v-student-name">
                                        <?php echo htmlspecialchars($validation['prenom'] . ' ' . $validation['nom']); ?> •
                                        <?php echo htmlspecialchars($validation['filiere']); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="v-justification">
                                <strong>Justification:</strong> <?php echo htmlspecialchars($validation['justification']); ?>
                            </div>
                            <div class="v-card-footer">
                                <span class="v-date">Déclaré le
                                    <?php echo htmlspecialchars($validation['date_demande']); ?></span>
                                <div class="v-buttons">
                                    <a href="refuser.php?iduser=<?php echo $validation['id_user']; ?>&idcompetence=<?php echo $validation['id_competence']; ?>&id_validateur=<?php echo $_SESSION['id_user']; ?>"
                                        class="v-btn-refuse"><?= icon('x') ?> Refuser</a>
                                    <a href="valider.php?iduser=<?php echo $validation['id_user']; ?>&idcompetence=<?php echo $validation['id_competence']; ?>&id_validateur=<?php echo $_SESSION['id_user']; ?>"
                                        class="v-btn-validate"><?= icon('check') ?> Valider</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>
    <script>
        function filterCards() {
            const search = document.getElementById('searchInput').value.toLowerCase();
            const filiere = document.getElementById('filiereFilter').value;
            document.querySelectorAll('.v-card-item').forEach(card => {
                const nom = card.dataset.nom;
                const competence = card.dataset.competence;
                const cardFiliere = card.dataset.filiere;
                const matchSearch = !search || nom.includes(search) || competence.includes(search);
                const matchFiliere = !filiere || cardFiliere === filiere;
                card.style.display = matchSearch && matchFiliere ? '' : 'none';
            });
        }
        document.getElementById('searchInput').addEventListener('input', filterCards);
        document.getElementById('filiereFilter').addEventListener('change', filterCards);
    </script>
    <script src="../../2-script/profile-menu.js"></script>
    
    <script src="validecompetence.js"></script>
</body>

</html>