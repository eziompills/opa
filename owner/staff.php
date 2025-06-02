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
    $email=$_POST['email']; $name=$_POST['name'];
    $user=$pdo->prepare("SELECT id FROM users WHERE email=?"); $user->execute([$email]); $uid=$user->fetchColumn();
    if(!$uid){
      $uid=null;
      // create account
      $pass=bin2hex(random_bytes(4));
      $pdo->prepare("INSERT INTO users (name,email,password_hash,role,verified) VALUES (?,?,?,?,1)")
          ->execute([$name,$email,password_hash($pass,PASSWORD_DEFAULT),'staff']);
      $uid=$pdo->lastInsertId();
      send_email($email,'Compte staff ôplani',"Bonjour $name, votre mot de passe temporaire est $pass");
    }
    // link staff
    $pdo->prepare("INSERT IGNORE INTO staff (salon_id,user_id) VALUES (?,?)")->execute([$salon_id,$uid]);
  }elseif(isset($_POST['remove'])){
    $pdo->prepare("DELETE FROM staff WHERE salon_id=? AND user_id=?")->execute([$salon_id,$_POST['uid']]);
  }
}
$staff=$pdo->prepare("SELECT u.* FROM staff s JOIN users u ON u.id=s.user_id WHERE s.salon_id=?");
$staff->execute([$salon_id]);
?>
<h1 class="section-title">Personnel – <?= htmlspecialchars($salon['name']) ?></h1>
<form method="post" class="row g-2 mb-4">
  <input type="hidden" name="add" value="1">
  <div class="col-md-4"><label for="staffName" class="visually-hidden">Nom du membre du personnel</label><input name="name" id="staffName" class="form-control" placeholder="Nom" required></div>
  <div class="col-md-5"><label for="staffEmail" class="visually-hidden">Email du membre du personnel</label><input type="email" name="email" id="staffEmail" class="form-control" placeholder="Email" required></div>
  <div class="col-md-3"><button class="btn btn-success w-100"><i class="bi-person-plus"></i> Ajouter</button></div>
</form>
<table class="table">
<thead><tr><th>Nom</th><th>Email</th><th></th></tr></thead>
<tbody>
<?php foreach($staff as $st): ?>
<tr>
  <td><?= htmlspecialchars($st['name']) ?></td>
  <td><?= htmlspecialchars($st['email']) ?></td>
  <td>
    <form method="post" style="display:inline">
      <input type="hidden" name="remove" value="1">
      <input type="hidden" name="uid" value="<?= $st['id'] ?>">
      <button class="btn btn-sm btn-outline-danger"><i class="bi-trash"></i></button>
    </form>
  </td>
</tr>
<?php endforeach; ?>
</tbody></table>
</div><?php require_once '../inc/footer.php'; ?>
