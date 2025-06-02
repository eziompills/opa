<?php
require_once 'inc/header.php';
require_role(['customer','owner','staff','admin']);
$bookings=$pdo->prepare("SELECT b.*, s.name as salon, sv.name as service, (SELECT id FROM reviews r WHERE r.booking_id=b.id LIMIT 1) as reviewed
  FROM bookings b
  JOIN salons s ON s.id=b.salon_id
  JOIN services sv ON sv.id=b.service_id
  WHERE b.customer_id=?
  ORDER BY b.starts_at DESC");
$bookings->execute([user()['id']]);
$bookings=$bookings->fetchAll();
?>
<h1>Mes réservations</h1>
<table class="table">
<thead><tr><th>Date</th><th>Salon</th><th>Service</th><th>Statut</th><th>Action</th></tr></thead>
<tbody>
<?php foreach($bookings as $b): ?>
<tr>
<td><?= $b['starts_at']?></td>
<td><?= htmlspecialchars($b['salon'])?></td>
<td><?= htmlspecialchars($b['service'])?></td>
<td><?= $b['status']?></td>
<?php
$canCancel = ($b['status']=='confirmed') && (strtotime($b['starts_at']) - time() > 86400);
?>
<td>
<?php if($canCancel): ?><a href="cancel_booking.php?id=<?= $b['id']?>" class="btn btn-sm btn-outline-danger me-1">Annuler</a><?php endif; ?>
<?php if(!$b['reviewed']): ?>
<a href="customer_review.php?booking_id=<?= $b['id']?>" class="btn btn-sm btn-outline-primary">Noter</a>
<?php else: ?>✓ Merci<?php endif; ?>
</td>
</tr>
<?php endforeach;?>
</tbody>
</table>
<?php require_once 'inc/footer.php'; ?>
