<?php 
session_start();
$servername = "127.0.0.1";
$username = "uz123_SuperAdmin";
$password = "Bankir2721";
$dbname = "uz123_kokand";
$conn = new mysqli($servername,$username,$password,$dbname);
if ($conn->connect_error) {
die("connection failed:".$conn->connect_error); }
date_default_timezone_set("UTC");
date_default_timezone_set("Asia/Tashkent");
?> 