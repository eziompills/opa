<nav class="sidebar d-none d-lg-block" id="sidebar">
  <h4 class="mb-4"><i class="bi-shield-lock"></i> Admin</h4>
  <a href="/admin/dashboard.php" class="<?= strpos($_SERVER['PHP_SELF'],'dashboard')!==false?'active':''?>"><i class="bi-speedometer"></i> Dashboard</a>
  <a href="/admin/salons.php" class="<?= strpos($_SERVER['PHP_SELF'],'salons')!==false?'active':''?>"><i class="bi-shop"></i> Salons</a>
  <a href="/admin/bookings.php" class="<?= strpos($_SERVER['PHP_SELF'],'bookings')!==false?'active':''?>"><i class="bi-journal-check"></i> Réservations</a>
  <a href="/admin/users.php" class="<?= strpos($_SERVER['PHP_SELF'],'users')!==false?'active':''?>"><i class="bi-people"></i> Utilisateurs</a>
</nav>
<button class="btn btn-outline-primary d-lg-none position-fixed" style="top:1rem;left:1rem;z-index:1050" onclick="document.getElementById('sidebar').classList.toggle('open')"><i class="bi-list"></i></button>
