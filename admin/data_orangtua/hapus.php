<?php

session_start();
require_once('../../config.php');

$id = $_GET['id'];

$result = mysqli_query($connection, "DELETE FROM orang_tua WHERE id=$id");

$_SESSION['berhasil']="Data berhasil dihapus";
header("Location: orangtua.php");
exit;

include('../layout/footer.php');
