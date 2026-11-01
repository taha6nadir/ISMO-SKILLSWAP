<?php
$base = '../../';
require_once __DIR__ . '/../../partials/bootstrap.php';
require_role(['formateur']);

include("../../database/config.php");

// filters
$searchFilter = isset($_GET['q']) ? trim($_GET['q']) : '';
$roleFilter = isset($_GET['role']) ? $_GET['role'] : '';
$statutFilter = isset($_GET['statut']) ? $_GET['statut'] : '';
$rolesAutorises = ['', 'stagiaire', 'mentor'];
$statutsAutorises = ['', 'actif', 'en_attente', 'suspendu'];
if (!in_array($roleFilter, $rolesAutorises)) $roleFilter = '';
if (!in_array($statutFilter, $statutsAutorises)) $statutFilter = '';

try {
  $conditions = ["(`statut`='actif' AND (role='stagiaire' OR role='mentor'))"];
  $params = [];
  if ($roleFilter) {
    $conditions[] = "role = ?";
    $params[] = $roleFilter;
  }
  if ($statutFilter) {
    $conditions[] = "statut = ?";
    $params[] = $statutFilter;
  }
  if ($searchFilter) {
    $conditions[] = "(CONCAT(nom, ' ', prenom) LIKE ? OR email LIKE ?)";
    $s = '%' . $searchFilter . '%';
    $params[] = $s;
    $params[] = $s;
  }
  $sql = "SELECT * FROM utilisateurs WHERE " . implode(' AND ', $conditions) . " ORDER BY date_inscription DESC";
  $utr = $db->prepare($sql);
  $utr->execute($params);
  $utilisateurs = $utr->fetchAll(PDO::FETCH_ASSOC);

  $totalUsers = $db->query("SELECT COUNT(*) FROM utilisateurs")->fetchColumn();
  $totalStagiaires = $db->query("SELECT COUNT(*) FROM utilisateurs WHERE role='stagiaire'")->fetchColumn();
  $totalMentors = $db->query("SELECT COUNT(*) FROM utilisateurs WHERE role='mentor'")->fetchColumn();
} catch (PDOException $e) {
  die("Erreur de base de données : " . $e->getMessage());
}


?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISMO-SkillSwap – Utilisateurs</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Outfit:wght@500;600;700;800&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="../../1-css/style.css" />
  <link rel="stylesheet" href="utilisateur.css" />
</head>

<body>
  <div class="layout">
    <?php
$base = '../../';
$activeNav = 'users';
?>
<?php include __DIR__ . '/../../partials/sidebar.php'; ?>
<?php include __DIR__ . '/../../partials/header.php'; ?>

    <main class="main">
      <section class="content-area">
        <div class="page-header">
          <div>
            <h1>Gérez les rôles des stagiaires</h1>
            <p class="eyebrow">Gestion des utilisateurs</p>
          </div>
        </div>

        <div class="stats-grid">
          <article class="stat-card">
            <span class="stat-label">Total utilisateurs</span>
            <strong><?= $totalUsers ?></strong>
          </article>
          <article class="stat-card">
            <span class="stat-label">Stagiaires</span>
            <strong><?= $totalStagiaires ?></strong>
          </article>
          <article class="stat-card">
            <span class="stat-label">Mentors</span>
            <strong><?= $totalMentors ?></strong>
          </article>
        </div>

        <section class="table-card">
          <form method="GET" action="index.php">
          <div class="table-header">
            <div class="search-wrapper">
              <span class="search-icon"><?= icon('search') ?></span>
              <input name="q" type="search" placeholder="Rechercher par nom, email..." aria-label="Recherche utilisateurs" value="<?= htmlspecialchars($searchFilter) ?>" />
            </div>
            <div class="filter-row">
              <label>
                <span class="visually-hidden">Filtrer par rôle</span>
                <select name="role" onchange="this.form.submit()">
                  <option value="">Tous les rôles</option>
                  <option value="stagiaire" <?= $roleFilter === 'stagiaire' ? 'selected' : '' ?>>Stagiaire</option>
                  <option value="mentor" <?= $roleFilter === 'mentor' ? 'selected' : '' ?>>Mentor</option>
                </select>
              </label>
              <label>
                <span class="visually-hidden">Filtrer par statut</span>
                <select name="statut" onchange="this.form.submit()">
                  <option value="">Tous les statuts</option>
                  <option value="actif" <?= $statutFilter === 'actif' ? 'selected' : '' ?>>Actif</option>
                  <option value="en_attente" <?= $statutFilter === 'en_attente' ? 'selected' : '' ?>>En attente</option>
                  <option value="suspendu" <?= $statutFilter === 'suspendu' ? 'selected' : '' ?>>Suspendu</option>
                </select>
              </label>
            </div>
          </div>
          </form>

          <div class="table-responsive">
            <table>
              <thead>
                <tr>
                  <th>Utilisateur</th>
                  <th>Email</th>
                  <th>Rôle</th>
                  <th>Filière</th>
              
                  <th>Actions</th>
                </tr>

              </thead>
              <tbody>
                <?php foreach ($utilisateurs as $user): ?>
           <?php
           if ($user['role'] == 'stagiaire') :
           ?>
                <tr>
                  <td class="user-cell">
                    <div class="user-cell-inner">
                      <span class="user-avatar"><?php echo htmlspecialchars(substr($user['nom'], 0, 1) . substr($user['prenom'], 0, 1)); ?></span>
                      <div>
                       <strong><?php echo htmlspecialchars($user['nom'] . ' ' . $user['prenom']); ?></strong>
                      </div>
                    </div>
                  </td>
                 <td><?php echo htmlspecialchars($user['email']); ?></td>
                  <td><span class="badge badge-stagiaire"><?php echo htmlspecialchars($user['role']); ?></span></td>
                 <td><?php echo htmlspecialchars($user['filiere']); ?></td>
               
                  <td class="actions-cell">
                    <div class="actions-cell-inner">
                      <a href="modification.php?id=<?php echo $user['id_user']; ?>&amp;role=mentor" class="btn-role" data-role="stagiaire">Passer Mentor</a>
                    </div>

                  </td>
                </tr>
                <?php endif; ?>
                <?php
                if ($user['role'] == 'mentor') :
                ?><tr>
                  <td class="user-cell">
                    <div class="user-cell-inner">
                      <span class="user-avatar"><?php echo htmlspecialchars(substr($user['nom'], 0, 1) . substr($user['prenom'], 0, 1)); ?></span>
                      <div>
                        <strong><?php echo htmlspecialchars($user['nom'] . ' ' . $user['prenom']); ?></strong>
                      </div>
                    </div>
                  </td>
                  <td><?php echo htmlspecialchars($user['email']); ?></td>
                  <td><span class="badge badge-mentor"><?php echo htmlspecialchars($user['role']); ?></span></td>
                  <td><?php echo htmlspecialchars($user['filiere']); ?></td>
                 
                  <td class="actions-cell">
                    <div class="actions-cell-inner">
                      <a href="modification.php?id=<?php echo $user['id_user']; ?>&amp;role=stagiaire" class="btn-role" data-role="mentor">Passer Stagiaire</a>
                    </div>
                  </td>
                </tr>
                <?php endif; ?>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </section>
      </section>
    </main>
  </div>
  <script src="../../2-script/profile-menu.js"></script>
  
</body>

</html>