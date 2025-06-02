<?php
require_once 'inc/config.php';
require_once 'inc/mailer.php';

require_once 'inc/auth.php';
if(!is_logged()){ header('Location:/login.php'); exit; }

$salon_id=(int)($_POST['salon_id']??0);
$service_id=(int)($_POST['service_id']??0);
$start=$_POST['start']??'';
$end=$_POST['end']??'';
if(!$salon_id||!$service_id||!$start||!$end){ die('param manquants'); }

$pdo->beginTransaction();
// choose first available staff
$staff=$pdo->prepare("SELECT u.id FROM staff s JOIN users u ON u.id=s.user_id WHERE s.salon_id=? LIMIT 1");
$staff->execute([$salon_id]);
$staff_id=$staff->fetchColumn();

$pdo->prepare("INSERT INTO bookings (salon_id,service_id,customer_id,staff_id,starts_at,ends_at) VALUES (?,?,?,?,?,?)")
    ->execute([$salon_id,$service_id,user()['id'],$staff_id,$start,$end]);
$pdo->commit();


// Notifications e‑mail
$customer_email = user()['email'];
$staff_email = $pdo->prepare("SELECT email FROM users WHERE id=?"); $staff_email->execute([$staff_id]); $staff_email=$staff_email->fetchColumn();
$salon_name = $pdo->query("SELECT name FROM salons WHERE id={$salon_id}")->fetchColumn();
$service_name = $pdo->query("SELECT name FROM services WHERE id={$service_id}")->fetchColumn();
$subj = "Confirmation de réservation - $salon_name";
$icsLink = "https://".$_SERVER['HTTP_HOST']."/generate_ics.php?booking_id=".$pdo->lastInsertId();
$body = "<p>Bonjour ".htmlspecialchars(user()['name']).",</p><p>Votre rendez-vous pour <strong>$service_name</strong> est confirmé le {$start}.</p><p><a href='$icsLink'>Ajouter à mon agenda</a></p>";
send_email($customer_email, $subj, $body);
if($staff_email) send_email($staff_email, "Nouveau rendez-vous - $salon_name", "<p>Vous avez un nouveau rendez-vous pour <strong>$service_name</strong> le {$start}.</p>");

header('Location:/my_bookings.php?msg=Réservation effectuée');
?>
