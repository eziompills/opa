
<?php
require_once '../inc/header.php';
include 'sidebar.php';
echo '<div class="content-wrap">';
require_role(['owner']);
$salons=$pdo->prepare("SELECT * FROM salons WHERE owner_id=?");
$salons->execute([user()['id']]);
$salons=$salons->fetchAll();
?>
<h1 class="section-title">Mon espace professionnel</h1>
<a href="add_salon.php" class="btn btn-primary mb-3"><i class="bi-plus-circle"></i> Ajouter un salon</a>

<div class="row g-4">
<?php foreach($salons as $s): ?>
  <div class="col-md-6" data-aos="fade-up">
    <div class="card p-4">
      <h4><?= htmlspecialchars($s['name']) ?></h4>
      <p class="text-muted"><?= htmlspecialchars($s['city']) ?> – <?= htmlspecialchars($s['category'] ?? '') ?></p>
      <div class="btn-group flex-wrap">
        <a href="profile.php?salon_id=<?= htmlspecialchars((string)$s['id']) ?>" class="btn btn-outline-secondary btn-sm">Profil</a>
        <a href="gallery.php?salon_id=<?= htmlspecialchars((string)$s['id']) ?>" class="btn btn-outline-secondary btn-sm">Galerie</a>
        <a href="services.php?salon_id=<?= htmlspecialchars((string)$s['id']) ?>" class="btn btn-outline-secondary btn-sm">Services</a>
        <a href="staff.php?salon_id=<?= htmlspecialchars((string)$s['id']) ?>" class="btn btn-outline-secondary btn-sm">Personnel</a>
        <a href="salon_hours.php?salon_id=<?= htmlspecialchars((string)$s['id']) ?>" class="btn btn-outline-secondary btn-sm">Horaires</a>
        <a href="reviews.php?salon_id=<?= htmlspecialchars((string)$s['id']) ?>" class="btn btn-outline-secondary btn-sm">Avis</a>
        <a href="schedule.php?salon_id=<?= htmlspecialchars((string)$s['id']) ?>" class="btn btn-outline-secondary btn-sm">Planning</a>
        <a href="analytics.php?salon_id=<?= htmlspecialchars((string)$s['id']) ?>" class="btn btn-outline-primary btn-sm"><i class="bi-graph-up"></i> Stats</a>
      <a href="marketing.php" class="btn btn-outline-secondary btn-sm">Marketing</a>
</div>
    </div>
  </div>
<?php endforeach; ?>
</div>
</div><?php require_once '../inc/footer.php'; ?>
