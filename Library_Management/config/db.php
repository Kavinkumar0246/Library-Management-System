<?php
$conn = new mysqli("localhost","root","","library_management");

if($conn->connect_error){
    die("Connection Failed");
}
?>