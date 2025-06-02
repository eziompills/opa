<?php
$menu=[['Dashboard','/owner/dashboard.php','bi-speedometer'],
       ['Galerie','/owner/gallery.php','bi-images'],
       ['Services','/owner/services.php','bi-scissors'],
       ['Personnel','/owner/staff.php','bi-people'],
       ['Planning','/owner/schedule.php','bi-calendar2-week'],
       ['Avis','/owner/reviews.php','bi-chat-square-quote'],
       ['Stats','/owner/analytics.php','bi-graph-up'],
       ['Marketing','/owner/marketing.php','bi-megaphone']];
?>
<nav class="sidebar d-none d-lg-block" id="sidebar">
  <h4 class="mb-4"><i class="bi-shop-window"></i> Espace Pro</h4>
  <?php foreach($menu as $m): ?>
    <a href="<?= $m[1] ?>" class="<?= strpos($_SERVER['PHP_SELF'],$m[1])!==false?'active':'' ?>"><i class="bi <?= $m[2]?> me-1"></i> <?= $m[0] ?></a>
  <?php endforeach;?>
</nav>
<?php display_sidebar_toggle('sidebar'); ?>
