<?php
function sendMail($to,$subject,$msg){
    mail($to,$subject,$msg);
}
?>