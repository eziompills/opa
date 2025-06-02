<?php
require_once 'inc/header.php';
require_role(['customer','owner','staff','admin']);
$booking_id=(int)($_GET['booking_id']??0);
$stmt=$pdo->prepare("SELECT b.*, s.name as salon_name FROM bookings b JOIN salons s ON s.id=b.salon_id WHERE b.id=? AND b.customer_id=?");
$stmt->execute([$booking_id,user()['id']]);
$booking=$stmt->fetch();
if(!$booking){ die('Réservation invalide'); }

// Handle form
if($_SERVER['REQUEST_METHOD']==='POST'){
  $rating=(int)($_POST['rating']??0);
  $comment=$_POST['comment']??'';
  $pdo->prepare("INSERT INTO reviews (booking_id,rating,comment) VALUES (?,?,?)")->execute([$booking_id,$rating,$comment]);
  header('Location: /dashboard.php?msg=Merci+pour+votre+avis');
  exit;
}
?>
<h1>Laisser un avis pour <?= htmlspecialchars($booking['salon_name']) ?></h1>
<form method="post">
  <div class="mb-3">
    <label class="form-label">Note (1 à 5)</label>
    <select name="rating" class="form-select" required>
      <?php for($i=5;$i>=1;$i--): ?><option value="<?= $i ?>"><?= $i ?></option><?php endfor; ?>
    </select>
  </div>
  <div class="mb-3">
    <label class="form-label">Commentaire</label>
    <textarea name="comment" class="form-control"></textarea>
  </div>
  <button class="btn btn-success">Envoyer</button>
</form>
<?php require_once 'inc/footer.php'; ?>
