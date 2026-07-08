<?php

$conn=mysqli_connect("localhost","root","","db_todo");
if(!$conn){
    die("Koneksi Gagal: ".mysqli_connect_error());
}

?>