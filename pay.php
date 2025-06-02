<?php
require_once 'inc/config.php';
require_once 'inc/stripe.php';
$booking_id=(int)($_GET['booking_id']??0);
$booking=$pdo->prepare("SELECT b.*, sv.name as service, sv.price_cents FROM bookings b JOIN services sv ON sv.id=b.service_id WHERE b.id=?");
$booking->execute([$booking_id]);
$b=$booking->fetch();
if(!$b || $b['customer_id']!=user()['id']) die('Invalide');

if($b['payment_status']=='paid'){header('Location: my_bookings.php');exit;}

$session=\Stripe\Checkout\Session::create([
 'customer_email'=>user()['email'],
 'line_items' => [[
   'price_data'=>[
     'currency'=>'eur',
     'unit_amount'=>$b['price_cents'],
     'product_data'=>['name'=>$b['service']]
   ],
   'quantity'=>1
 ]],
 'mode'=>'payment',
 'success_url'=>'https://'.$_SERVER['HTTP_HOST'].'/pay_success.php?sid={CHECKOUT_SESSION_ID}',
 'cancel_url'=>'https://'.$_SERVER['HTTP_HOST'].'/my_bookings.php'
]);

$pdo->prepare("UPDATE bookings SET stripe_session=?, payment_status='unpaid' WHERE id=?")
    ->execute([$session->id,$booking_id]);

header('Location: '.$session->url);
exit;
?>