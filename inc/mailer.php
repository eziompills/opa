<?php
function send_email($to, $subject, $html){
    // simple wrapper around PHP mail()
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
    $headers .= "From: oplani <no-reply@oplani.fr>\r\n";
    return mail($to, $subject, $html, $headers);
}
?>
