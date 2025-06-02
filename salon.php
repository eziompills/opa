<?php
require_once 'inc/header.php';
$slug=$_GET['slug']??'';
$salonStmt=$pdo->prepare("SELECT * FROM salons WHERE slug=?");$salonStmt->execute([$slug]);
$salon=$salonStmt->fetch();
if(!$salon){http_response_code(404);require '404.php';exit;}

$services=$pdo->prepare("SELECT * FROM services WHERE salon_id=?");$services->execute([$salon['id']]);

$avg=$pdo->prepare("SELECT AVG(rating) avg, COUNT(*) c FROM reviews r JOIN bookings b ON b.id=r.booking_id WHERE b.salon_id=?");
$avg->execute([$salon['id']]);$rating=$avg->fetch();
$stars= str_repeat('<i class="bi-star-fill rating"></i>',floor($rating['avg'])) ?? '';

$gallery_dir='uploads/s'.$salon['id'];
$pics=is_dir($gallery_dir)?glob($gallery_dir.'/*'):[];
$cover = $pics[0] ?? 'assets/img/placeholder.jpg';
?>
<style>
.hero-salon{height:320px;position:relative;border-radius:1rem;overflow:hidden;margin-bottom:1rem}
.hero-salon .overlay{background:linear-gradient(to top,rgba(0,0,0,.7),rgba(0,0,0,0));color:#fff;padding:2rem;height:100%;position:absolute;bottom:0;left:0;right:0}
</style>
<div class="hero-salon" style="background:url('/<?= $cover ?>') center/cover">
  <div class="overlay">
    <h1><?= htmlspecialchars($salon['name']) ?></h1>
    <span class="badge bg-primary"><?= ucfirst($salon['category']) ?></span>
    <div><?= $stars ?> (<?= $rating['c'] ?>)</div>
    <p><i class="bi-geo-alt"></i> <?= htmlspecialchars($salon['address']) ?> – <?= htmlspecialchars($salon['city']) ?></p>
  </div>
</div>

<!-- Tabs -->
<ul class="nav nav-tabs" id="salonTab" role="tablist">
  <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#desc" type="button">Présentation</button></li>
  <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#photos" type="button">Photos</button></li>
  <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#avis" type="button">Avis</button></li>
</ul>
<div class="tab-content p-4">
  <div class="tab-pane fade show active" id="desc">
    <?= nl2br(htmlspecialchars($salon['description'])) ?>
    <h4 id="reserver" class="mt-4">Réserver</h4>
    <form method="post" action="choose_slot.php" class="row g-2">
      <input type="hidden" name="salon_id" value="<?= $salon['id'] ?>">
      <div class="col-md-6">
        <select name="service_id" class="form-select" required>
          <?php foreach($services as $srv): ?>
            <option value="<?= $srv['id']?>"><?= htmlspecialchars($srv['name']) ?> – <?= number_format($srv['price_cents']/100,2) ?>€</option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3"><button class="btn btn-primary w-100"><i class="bi-calendar-plus"></i> Choisir créneau</button></div>
    </form>
  </div>
  <div class="tab-pane fade" id="photos">
    <?php if($pics): ?>
    <div class="swiper">
      <div class="swiper-wrapper">
        <?php foreach($pics as $p): ?><div class="swiper-slide"><img src="/<?= $p ?>" class="img-fluid rounded"></div><?php endforeach;?>
      </div><div class="swiper-pagination"></div>
    </div>
    <script>new Swiper('.swiper',{loop:true,pagination:{el:'.swiper-pagination'}});</script>
    <?php else: ?><p>Pas encore de photos.</p><?php endif; ?>
  </div>
  <div class="tab-pane fade" id="avis">
    <?php
    $reviews=$pdo->prepare("SELECT r.*,u.name FROM reviews r JOIN bookings b ON b.id=r.booking_id JOIN users u ON u.id=b.customer_id WHERE b.salon_id=? ORDER BY r.created_at DESC");
    $reviews->execute([$salon['id']]);
    foreach($reviews as $r): ?>
      <div class="mb-3 border-bottom pb-2">
        <strong><?= htmlspecialchars($r['name']) ?></strong>
        <div class="rating"><?= str_repeat('★',$r['rating']) ?></div>
        <p><?= nl2br(htmlspecialchars($r['comment'])) ?></p>
        <?php if($r['owner_reply']): ?><div class="alert alert-info p-2"><small>Réponse : <?= htmlspecialchars($r['owner_reply']) ?></small></div><?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<a href="#reserver" class="btn btn-primary d-md-none w-100 fixed-bottom rounded-0 py-3 text-center shadow"><i class="bi-calendar-plus"></i> Réserver</a>
<?php require_once 'inc/footer.php'; ?>
