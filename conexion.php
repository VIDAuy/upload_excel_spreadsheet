<?php

$conexion = new mysqli("localhost", "root", "", "excel");

if ($conexion->connect_errno) {
    echo "Fallo la conexión " . $conexion->connect_errno;
    die();
}
