<?php

$conn=mysqli_connect("localhost","root","1234","db_todo");
if(!$conn){
    die("Koneksi Gagal: ".mysqli_connect_error());
}

?>