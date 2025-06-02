<?php require_once 'inc/header.php'; ?>
<?php
$citiesList=$pdo->query("SELECT DISTINCT city FROM salons ORDER BY city")->fetchAll(PDO::FETCH_COLUMN);
$servicesList=$pdo->query("SELECT DISTINCT name FROM services ORDER BY name")->fetchAll(PDO::FETCH_COLUMN);
$q = $_GET['q'] ?? '';
$city = $_GET['city'] ?? '';
$service = $_GET['service'] ?? '';
$category = $_GET['category'] ?? '';
$sort = $_GET['sort'] ?? '';
$sql = "SELECT DISTINCT s.id,s.name,s.city,s.lat,s.lng,s.slug,(SELECT AVG(r.rating) FROM reviews r JOIN bookings b ON b.id=r.booking_id WHERE b.salon_id=s.id) as rating, (SELECT MIN(sv.price_cents) FROM services sv WHERE sv.salon_id=s.id) as min_price
        FROM salons s LEFT JOIN services sv ON sv.salon_id=s.id WHERE 1";
$params=[];
if($q){ $sql.=" AND (s.name LIKE ? OR s.city LIKE ?)"; $params[]="%$q%"; $params[]="%$q%"; }
if($city){ $sql.=" AND s.city=?"; $params[]=$city; }
if($service){ $sql.=" AND sv.name=?"; $params[]=$service; }
if($category){ $sql.=" AND s.category=?"; $params[]=$category; }
if($sort=='price'){ $sql.=" GROUP BY s.id ORDER BY min_price ASC"; }
elseif($sort=='rating'){ $sql.=" GROUP BY s.id ORDER BY rating DESC"; }
else{ $sql.=" GROUP BY s.id ORDER BY s.name"; }
$stmt=$pdo->prepare($sql);
$stmt->execute($params);
$salons=$stmt->fetchAll();
?>
<div class="container py-4">
  <h1 class="section-title">Rechercher un salon</h1>
  <form class="row g-3 mb-4">
    <div class="col-md-3">
      <input list="cities" name="city" class="form-control" placeholder="Ville" value="<?= htmlspecialchars($city) ?>">
      <datalist id="cities"><?php foreach($citiesList as $c): ?><option value="<?= htmlspecialchars($c) ?>"><?php endforeach; ?></datalist>
    </div>
    <div class="col-md-3">
      <input list="servicesList" name="service" class="form-control" placeholder="Service" value="<?= htmlspecialchars($service) ?>">
      <datalist id="servicesList"><?php foreach($servicesList as $s): ?><option value="<?= htmlspecialchars($s) ?>"><?php endforeach; ?></datalist>
    </div>
    <div class="col-md-2">
      <select class="form-select" name="category">
        <option value="">Catégorie</option>
        <?php foreach(['barbershop'=>'Barbier','bio'=>'Bio','kids'=>'Kids','mixte'=>'Mixte','spa'=>'Spa'] as $k=>$v): ?>
          <option value="<?= $k ?>" <?= ($category==$k)?'selected':'' ?>><?= $v ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-2">
      <select class="form-select" name="sort">
        <option value="">Tri</option>
        <option value="price" <?= ($sort=='price')?'selected':'' ?>>Prix</option>
        <option value="rating" <?= ($sort=='rating')?'selected':'' ?>>Note</option>
      </select>
    </div>
    <div class="col-md-2"><button class="btn btn-primary w-100">Filtrer</button></div>
  </form>
  <div id="map" style="height:400px; border-radius:1rem; box-shadow:0 4px 12px rgba(0,0,0,.1);"></div>
  <div class="row mt-4">
    <?php foreach($salons as $salon): ?>
      <div class="col-md-4 mb-4">
        <div class="card h-100">
          <div class="card-body">
            <h5 class="card-title"><?= htmlspecialchars($salon['name']) ?></h5>
            <p class="card-text"><?= htmlspecialchars($salon['city']) ?></p>
            <p class="card-text">€<?= number_format($salon['min_price']/100,2) ?> | ★<?= round($salon['rating'],1) ?></p>
            <a href="salon.php?slug=<?= urlencode($salon['slug']) ?>" class="btn btn-primary">Voir</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>
<script>
var map = L.map('map').setView([46.5,2.5],6);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:18}).addTo(map);
L.Control.geocoder({defaultMarkGeocode:false})
  .on('markgeocode', function(e){ map.fitBounds(e.geocode.bbox); })
  .addTo(map);

var markers = L.markerClusterGroup();
<?php foreach($salons as $salon): if($salon['lat'] && $salon['lng']): ?>
  L.marker([<?= $salon['lat'] ?>, <?= $salon['lng'] ?>])
   .bindPopup('<strong><?= addslashes($salon['name']) ?></strong><br>€<?= number_format($salon['min_price']/100,2) ?><br><a href="salon.php?slug=<?= addslashes($salon['slug']) ?>">Voir</a>')
   .addTo(markers);
<?php endif; endforeach; ?>
map.addLayer(markers);
</script>
<?php require_once 'inc/footer.php'; ?>