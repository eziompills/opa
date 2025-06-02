<?php
require_once '../inc/header.php';
include 'sidebar.php';
echo '<div class="content-wrap">';
require_role(['owner']);
$salon_id=(int)($_GET['salon_id']??0);
$salon=$pdo->prepare("SELECT * FROM salons WHERE id=? AND owner_id=?");
$salon->execute([$salon_id,user()['id']]);
$salon=$salon->fetch();
if(!$salon){ http_response_code(404); echo 'Salon non trouvé'; require '../inc/footer.php'; exit; }

if($_SERVER['REQUEST_METHOD']==='POST'){
  if(isset($_POST['add'])){
    $pdo->prepare("INSERT INTO services (salon_id,name,duration,price_cents) VALUES (?,?,?,?)")
        ->execute([$salon_id,$_POST['name'],$_POST['duration'],$_POST['price']*100]);
  }elseif(isset($_POST['delete'])){
    $pdo->prepare("DELETE FROM services WHERE id=? AND salon_id=?")->execute([$_POST['service_id'],$salon_id]);
  }
  header("Location: services.php?salon_id=$salon_id"); exit;
}
$services=$pdo->prepare("SELECT * FROM services WHERE salon_id=?"); $services->execute([$salon_id]);
?>
<h1 class="section-title">Services – <?= htmlspecialchars($salon['name']) ?></h1>
<form method="post" class="row g-2 mb-4">
  <input type="hidden" name="add" value="1">
  <div class="col-md-4"><label for="serviceName" class="visually-hidden">Nom du service</label><input name="name" id="serviceName" class="form-control" placeholder="Nom" required></div>
  <div class="col-md-3"><label for="serviceDuration" class="visually-hidden">Durée en minutes</label><input name="duration" id="serviceDuration" class="form-control" placeholder="Durée (min)" type="number" required></div>
  <div class="col-md-3"><label for="servicePrice" class="visually-hidden">Prix en euros</label><input type="number" name="price" id="servicePrice" class="form-control" placeholder="Prix €" step="0.01" required></div>
  <div class="col-md-2"><button class="btn btn-success w-100"><i class="bi-plus"></i></button></div>
</form>
<table class="table">
<thead><tr><th>Service</th><th>Durée</th><th>Prix</th><th></th></tr></thead>
<tbody>
<?php foreach($services as $s): ?>
  <tr>
    <td><?= htmlspecialchars($s['name']) ?></td>
    <td><?= $s['duration'] ?> min</td>
    <td><?= number_format($s['price_cents']/100,2) ?> €</td>
    <td>
      <form method="post" style="display:inline">
        <input type="hidden" name="delete" value="1">
        <input type="hidden" name="service_id" value="<?= $s['id'] ?>">
        <button class="btn btn-sm btn-outline-danger"><i class="bi-trash"></i></button>
      </form>
    </td>
  </tr>
<?php endforeach; ?>
</tbody></table>
</div><?php require_once '../inc/footer.php'; ?>
