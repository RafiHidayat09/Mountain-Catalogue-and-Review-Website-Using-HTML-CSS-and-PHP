<?php

//[1]informasi database
$db_server="localhost";
$db_username="root";
$db_password="";
$db_name="userprofile";//ubah nama data base sesuai



$conn=mysqli_connect($db_server, $db_username,$db_password,$db_name);

if(!$conn){
    echo"Koneksi gagal".mysqli_connect_error();
} else{
    echo"Koneksi berhasil";
}
?>