<?php
$base = '../../';
require_once __DIR__ . '/../../partials/bootstrap.php';
require_role(['admin']);

$result = [];
$totalSignal = 0;
$totalPages = 1;
$page = max(1, (int) ($_GET['page'] ?? 1));
try{
    include('../../database/config.php');
    # Récupérer les publications signalées
    $perPage = 10;
    $totalSignal = (int) $db->query("SELECT COUNT(*) FROM aide WHERE `signal` > 0")->fetchColumn();
    $totalPages = max(1, (int) ceil($totalSignal / $perPage));
    $page = min($page, $totalPages);
    $offset = ($page - 1) * $perPage;
    $req = $db->prepare("SELECT p.*, u.nom, u.prenom FROM aide p JOIN utilisateurs u ON p.id_user = u.id_user WHERE p.`signal` > 0 ORDER BY p.date_pub DESC LIMIT $perPage OFFSET $offset");
    $req->execute();
    $result = $req->fetchAll(PDO::FETCH_ASSOC);

}catch(PDOException $e){
    echo "ERREUR DE CONNEXION À LA BASE DE DONNÉES: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Modération</title>
  <link rel="stylesheet" href="../../1-css/style.css?v=2" />
  <link rel="stylesheet" href="mode.css?v=2" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Outfit:wght@500;600;700;800&display=swap"
    rel="stylesheet" />
</head>

<body>
  <div class="layout">
    <?php
$base = '../../';
$activeNav = 'moderation';
?>
<?php include __DIR__ . '/../../partials/sidebar.php'; ?>
<?php include __DIR__ . '/../../partials/header.php'; ?>

    <main class="content-area">
      <div class="page-header">
        <div>
          <h1>Modération</h1>
          <p class="page-subtitle">Examinez les publications signalées par la communauté</p>
        </div>
      </div>

      <div class="stats-grid">
        <article class="stat-card">
          <span class="stat-label">Publications signalées</span>
          <strong><?php echo $totalSignal ?? 0; ?></strong>
        </article>
      </div>
      <div class="moderation-items">
        <?php if (count($result) == 0): ?>
          <div style="text-align:center;padding:24px;color:#a5a3bf;">Aucune publication signalée</div>
        <?php else: ?>
        <?php foreach ($result as $row): ?>
          <article class="moderation-card">
            <div class="card-header">
              <div class="warning-icon"><?= icon('alert') ?></div>
              <div class="publication-info">
                <h2><?php echo $row['titre']; ?></h2>
              </div>
              <div class="user-meta">
                <?php
                  $initial = strtoupper(substr($row['nom'] ?? '?', 0, 1) . substr($row['prenom'] ?? '?', 0, 1));
                ?>
                <div class="user-avatar"><?= $initial ?></div>
                <div class="user-details">
                  <span class="user-name card-user-name"><?= htmlspecialchars(($row['nom'] ?? 'Inconnu') . ' ' . ($row['prenom'] ?? '')) ?></span>
                  <span class="date"><?= $row['date_pub'] ?></span>
                </div>
              </div>
              <div class="report-count">
                <div class="count"><?php echo $row['signal']; ?></div>
                <div class="label">signalements</div>
              </div>
            </div>
          <div class="content-body">
            <p><?php echo htmlspecialchars($row['description']); ?></p>
          </div>

          <div class="card-actions">
            <a href="garder.php?id=<?php echo $row['id_demande']; ?>" class="btn btn-keep"><?= icon('check') ?> Garder</a>
            <a href="supprimer.php?id=<?php echo $row['id_demande']; ?>" class="btn btn-delete"><?= icon('trash') ?> Supprimer</a>
          </div>
        </article>
        <?php endforeach; ?>
        <?php endif; ?>
        
      </div>
      <?php
      $paginationParams = [];
      include __DIR__ . '/../../partials/pagination.php';
      ?>
    </main>
  </div>
  <script src="../../2-script/profile-menu.js"></script>
  
</body>

</html>