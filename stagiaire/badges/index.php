<?php
$base = '../../';
require_once __DIR__ . '/../../partials/bootstrap.php';
require_role(['stagiaire', 'mentor']);
try {
  include('../../database/config.php');
  $badges = $db->prepare("SELECT * FROM badges");
  $badges->execute();
  $badges = $badges->fetchAll(PDO::FETCH_ASSOC);
  #recupere les badges obtenus et join le nom et les icone des badges 
  $badges_obtenus = $db->prepare("SELECT * FROM obtention_badges bo JOIN badges b ON b.id_badge = bo.id_badge WHERE bo.id_user = ?");
  $badges_obtenus->execute([$id_user]);
  $badges_obtenus = $badges_obtenus->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
  echo "Erreur : " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISMO-SkillSwap – Mes badges</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="../../1-css/style.css">
  <link rel="stylesheet" href="style.css" />
</head>

<body>
  <div class="layout">
    <?php
$base = '../../';
$activeNav = 'badges';
?>
<?php include __DIR__ . '/../../partials/sidebar.php'; ?>
<?php include __DIR__ . '/../../partials/header.php'; ?>
    <main class="main">
      <div class="page-title">Mes badges</div>
      <div class="page-sub">Vos badges de reconnaissance obtenus grâce à vos contributions</div>
      <div class="badge-stats">
        <div class="stat-card">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="2">
            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
          </svg>
          <div>
            <div class="stat-number"><?php echo count($badges_obtenus); ?></div>
            <div class="stat-label">Badges obtenus</div>
          </div>
        </div>
        <div class="stat-card">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2">
            <polyline points="9 11 12 14 22 4" />
            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" />
          </svg>
          <div>
            <div class="stat-number"><?php echo count($badges); ?></div>
            <div class="stat-label">Badges disponibles</div>
          </div>
        </div>
      </div>
      <section class="badges-section">
        <h2><?= icon('award') ?> Mes badges obtenus</h2>
        <div class="badges-grid">
          <?php if (count($badges_obtenus) === 0): ?>
            <p class="no-badges">Vous n'avez pas encore obtenu de badges. Participez pour gagner des récompenses !</p>
          <?php else: ?>
            <?php foreach ($badges_obtenus as $badge_obtenu): ?>
              <article class="badge-item">
                <div class="badge-icon"><?php echo $badge_obtenu['icone']; ?></div>
                <div class="badge-content">
                  <h3><?php echo $badge_obtenu['nom']; ?></h3>
                  <?php
                  $date = new DateTime($badge_obtenu['date_obtention']);
                  $date_formatee = $date->format('d/m/Y');
                  ?>
                  <span class="badge-date">Obtenu le <?php echo htmlspecialchars($date_formatee); ?></span>
                </div>
              </article>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </section>
      <section class="badges-section">
        <h2><?= icon('lock') ?> Badges disponibles</h2>
        <div class="badges-grid">
          <?php foreach ($badges as $badge): ?>
            <article class="badge-item locked">
              <div class="badge-icon"><?php echo $badge['icone']; ?></div>
              <div class="badge-content">
                <h3><?php echo $badge['nom']; ?></h3>
                <p>Points requis : <?php echo $badge['points_requis']; ?></p>
              </div>
              <?php
              echo '<!-- score='.($_SESSION['score'] ?? 'N/A').' pts_requis='.$badge['points_requis'].' obtenus='.json_encode(array_column($badges_obtenus, 'id_badge')).' -->';
              $sc = $_SESSION['score'] ?? 0;
              if (in_array($badge['id_badge'], array_column($badges_obtenus, 'id_badge'))) {
                echo '<p class="btn-claim">badge obtenu</p>';
              } elseif ($badge['points_requis'] <= $sc) {
                echo '<a href="reclamation.php?id_badge=' . $badge['id_badge'] . '" class="btn-claim">Réclamer</a>';
              } else {
                echo '<p class="btn-claim disabled">Points insuffisants</p>';
              } 
              ?>
            </article>
          <?php endforeach; ?>
        </div>
      </section>
    </main>
  </div>
  <script src="../../2-script/profile-menu.js"></script>
  
</body>

</html>