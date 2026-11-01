<?php
$base = '../';
require_once __DIR__ . '/../partials/bootstrap.php';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
  header('Location: ../search.php');
  exit;
}

include '../database/config.php';

$stmt = $db->prepare("SELECT id_user, nom, prenom, email, role, filiere, photo, score, note_moyenne FROM utilisateurs WHERE id_user = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
  echo "Utilisateur introuvable.";
  exit;
}

$nom = $user['nom'];
$prenom = $user['prenom'];
$role = $user['role'];
$filiere = $user['filiere'];
$photo = $user['photo'];
$score = $user['score'];
$note_moyenne = $user['note_moyenne'];
$initials = strtoupper(substr($nom, 0, 1) . substr($prenom, 0, 1));

$competences = [];
$badges = [];
$aide_count = 0;

if ($role === 'stagiaire' || $role === 'mentor') {
  $stmt = $db->prepare("SELECT c.nom, vc.niveau, vc.date_validation FROM validation_competence vc JOIN competences c ON vc.id_competence = c.id_competence WHERE vc.id_user = ? AND vc.status = 'validee' ORDER BY vc.date_validation DESC");
  $stmt->execute([$id]);
  $competences = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $stmt = $db->prepare("SELECT b.nom, b.icone, ob.date_obtention FROM obtention_badges ob JOIN badges b ON ob.id_badge = b.id_badge WHERE ob.id_user = ? ORDER BY ob.date_obtention DESC");
  $stmt->execute([$id]);
  $badges = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if ($role === 'mentor') {
  $stmt = $db->prepare("SELECT COUNT(*) FROM aide_effectuee WHERE id_mentor = ?");
  $stmt->execute([$id]);
  $aide_count = intval($stmt->fetchColumn());
}

$confirmed_competences = [];
$attributed_badges = [];
if ($role === 'formateur') {
  $stmt = $db->prepare("SELECT c.nom, vc.niveau, vc.date_validation, u.nom AS unom, u.prenom AS uprenom FROM validation_competence vc JOIN competences c ON vc.id_competence = c.id_competence JOIN utilisateurs u ON vc.id_user = u.id_user WHERE vc.id_validateur = ? AND vc.status = 'validee' ORDER BY vc.date_validation DESC");
  $stmt->execute([$id]);
  $confirmed_competences = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $stmt = $db->prepare("SELECT b.nom, b.icone, ob.date_obtention, u.nom AS unom, u.prenom AS uprenom FROM obtention_badges ob JOIN badges b ON ob.id_badge = b.id_badge JOIN utilisateurs u ON ob.id_user = u.id_user WHERE ob.confirmed_by = ? ORDER BY ob.date_obtention DESC");
  $stmt->execute([$id]);
  $attributed_badges = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$comp_count = count($competences);
$badge_count = count($badges);
$confirmed_count = count($confirmed_competences);
$attributed_count = count($attributed_badges);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISMO-SkillSwap – Profil de <?= htmlspecialchars($nom . ' ' . $prenom) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="../1-css/style.css" />
  <link rel="stylesheet" href="public_profile.css" />
</head>
<body>
  <div class="layout">
    <?php
$base = '../';
$activeNav = 'profiles';
$publicNav = true;
$showBack = true;
?>
<?php include __DIR__ . '/../partials/sidebar.php'; ?>
<?php include __DIR__ . '/../partials/header.php'; ?>

    <main class="main">
      <div class="pub-card">
        <div class="pub-avatar">
          <?php if ($photo): ?>
            <img src="../pagelogin/photo/<?= htmlspecialchars($photo) ?>" alt="" loading="lazy" decoding="async" width="80" height="80" style="width: 80px; height: 80px; object-fit:cover; border-radius:50%;">
          <?php else: ?>
            <?= $initials ?>
          <?php endif; ?>
        </div>
        <div class="pub-info">
          <h1><?= htmlspecialchars($nom . ' ' . $prenom) ?></h1>
          <div class="pub-role"><?= htmlspecialchars(ucfirst($role) . ($filiere ? ' — ' . $filiere : '')) ?></div>
          <?php if ($role === 'mentor'): ?>
          <div class="pub-rating">
            <span class="stars"><?php if ($note_moyenne > 0): ?><?= str_repeat('★', min(5, intval($note_moyenne))) . str_repeat('☆', 5 - min(5, intval($note_moyenne))) ?><?php else: ?>☆☆☆☆☆<?php endif; ?></span>
            <span class="score"><?= $note_moyenne > 0 ? number_format($note_moyenne, 1) : '—' ?></span>
          </div>
          <?php endif; ?>
        </div>
      </div>

      <div class="pub-stats">
        <?php if ($role === 'stagiaire' || $role === 'mentor'): ?>
          <div class="pub-stat">
            <div class="val"><?= $comp_count ?></div>
            <div class="lbl">Compétences validées</div>
          </div>
          <div class="pub-stat">
            <div class="val"><?= $badge_count ?></div>
            <div class="lbl">Badges obtenus</div>
          </div>
          <div class="pub-stat">
            <div class="val"><?= $role === 'mentor' ? $aide_count : $score ?></div>
            <div class="lbl"><?= $role === 'mentor' ? 'Aides effectuées' : 'Score' ?></div>
          </div>
        <?php elseif ($role === 'formateur'): ?>
          <div class="pub-stat">
            <div class="val"><?= $confirmed_count ?></div>
            <div class="lbl">Compétences confirmées</div>
          </div>
          <div class="pub-stat">
            <div class="val"><?= $attributed_count ?></div>
            <div class="lbl">Badges attribués</div>
          </div>
          <div class="pub-stat">
            <div class="val"><?= $score ?></div>
            <div class="lbl">Score</div>
          </div>
        <?php elseif ($role === 'admin'): ?>
          <div class="pub-stat">
            <div class="val"><?= $score ?></div>
            <div class="lbl">Score</div>
          </div>
          <div class="pub-stat">
            <div class="val">Admin</div>
            <div class="lbl">Rôle</div>
          </div>
          <div class="pub-stat">
            <div class="val">—</div>
            <div class="lbl">Plateforme</div>
          </div>
        <?php endif; ?>
      </div>

      <?php if ($role === 'stagiaire' || $role === 'mentor'): ?>
      <div class="pub-grid">
        <div class="pub-card-section">
          <div class="pub-card-title">Compétences validées</div>
          <?php if (count($competences) > 0): ?>
          <div class="comp-list">
            <?php foreach ($competences as $c): ?>
              <div class="comp-item">
                <span><?= htmlspecialchars($c['nom']) ?></span>
                <span class="comp-level"><?= htmlspecialchars(ucfirst($c['niveau'])) ?></span>
              </div>
            <?php endforeach; ?>
          </div>
          <?php else: ?>
          <p style="color:#a5a3bf;font-size:0.9rem;">Aucune compétence validée pour le moment.</p>
          <?php endif; ?>
        </div>
        <div class="pub-card-section">
          <div class="pub-card-title">Badges obtenus</div>
          <?php if (count($badges) > 0): ?>
          <div class="badge-list">
            <?php foreach ($badges as $b): ?>
              <div class="badge-item">
                <div class="icon"><?= htmlspecialchars($b['icone'] ?? '🎖️') ?></div>
                <div>
                  <div class="name"><?= htmlspecialchars($b['nom']) ?></div>
                  <div class="date"><?= $b['date_obtention'] ? date('d/m/Y', strtotime($b['date_obtention'])) : '—' ?></div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
          <?php else: ?>
          <p style="color:#a5a3bf;font-size:0.9rem;">Aucun badge obtenu pour le moment.</p>
          <?php endif; ?>
        </div>
      </div>
      <?php endif; ?>

      <?php if ($role === 'mentor' && $aide_count > 0): ?>
      <div class="pub-card-section" style="margin-top:24px;">
        <div class="pub-card-title">Aides effectuées</div>
        <p style="color:#3e3b60;">Ce mentor a effectué <strong><?= $aide_count ?></strong> intervention<?= $aide_count > 1 ? 's' : '' ?> d'aide.</p>
      </div>
      <?php endif; ?>

      <?php if ($role === 'formateur'): ?>
      <div class="pub-grid">
        <div class="pub-card-section">
          <div class="pub-card-title">Compétences confirmées</div>
          <?php if (count($confirmed_competences) > 0): ?>
          <div class="comp-list">
            <?php foreach ($confirmed_competences as $c): ?>
              <div class="comp-item">
                <span><?= htmlspecialchars($c['nom']) ?> <span style="color:#a5a3bf;font-weight:400;font-size:0.85rem;">— <?= htmlspecialchars($c['unom'] . ' ' . $c['uprenom']) ?></span></span>
                <span class="comp-level"><?= htmlspecialchars(ucfirst($c['niveau'])) ?></span>
              </div>
            <?php endforeach; ?>
          </div>
          <?php else: ?>
          <p style="color:#a5a3bf;font-size:0.9rem;">Aucune compétence confirmée pour le moment.</p>
          <?php endif; ?>
        </div>
        <div class="pub-card-section">
          <div class="pub-card-title">Badges attribués</div>
          <?php if (count($attributed_badges) > 0): ?>
          <div class="badge-list">
            <?php foreach ($attributed_badges as $b): ?>
              <div class="badge-item">
                <div class="icon"><?= htmlspecialchars($b['icone'] ?? '🎖️') ?></div>
                <div>
                  <div class="name"><?= htmlspecialchars($b['nom']) ?></div>
                  <div class="date"><?= htmlspecialchars($b['unom'] . ' ' . $b['uprenom']) ?> — <?= $b['date_obtention'] ? date('d/m/Y', strtotime($b['date_obtention'])) : '—' ?></div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
          <?php else: ?>
          <p style="color:#a5a3bf;font-size:0.9rem;">Aucun badge attribué pour le moment.</p>
          <?php endif; ?>
        </div>
      </div>
      <?php endif; ?>

      <?php if ($role === 'admin'): ?>
      <div class="pub-grid">
        <div class="pub-card-section">
          <div class="pub-card-title">À propos</div>
          <p style="color:#3e3b60;">Administrateur de la plateforme ISMO-SkillSwap.</p>
        </div>
        <div class="pub-card-section">
          <div class="pub-card-title">Email</div>
          <p style="color:#3e3b60;"><?= htmlspecialchars($user['email']) ?></p>
        </div>
      </div>
      <?php endif; ?>
    </main>
  </div>
  <script src="../2-script/profile-menu.js"></script>
</body>
</html>
