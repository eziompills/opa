<?php
require_once '../inc/header.php';
include 'sidebar.php';
echo '<div class="content-wrap">';
require_role(['admin']);
?>
<h1 class="section-title">Tableau de bord administrateur</h1>
<ul>
  <li><a href="stats.php">Graphiques</a></li>
  <li><a href="users.php">Utilisateurs</a></li>
  <li><a href="salons.php">Salons</a></li>
  <li><a href="bookings.php">Réservations</a></li>
  <li><a href="import_salons.php">Import salons CSV</a></li>
</ul>
</div><?php require_once '../inc/footer.php'; ?>
