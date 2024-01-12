<?php
require './vendor/autoload.php';
require './conexion.php';

/** Cargamos la librería **/

use PhpOffice\PhpSpreadsheet\IOFactory;

$nombre_archivo = './usuarios.xlsx'; //ruta del archivo
$documento = IOFactory::load($nombre_archivo); //Cargamos el archivo excel
$total_hojas = $documento->getSheetCount(); //Obtenemos la cantidad de hojas


$error_registro = 0;

/** Recorremos las hojas **/
for ($indice_hoja = 0; $indice_hoja < $total_hojas; $indice_hoja++) {
    //Establecemos la hoja que va a analizar
    $hoja_actual = $documento->getSheet($indice_hoja);

    //Obtenemos la cantidad de filas que contienen información
    $numero_filas = $hoja_actual->getHighestDataRow();

    //Obtenemos la cantidad de columnas que contienen información
    $letra = $hoja_actual->getHighestColumn();

    for ($indice_fila = 2; $indice_fila <= $numero_filas; $indice_fila++) {
        $cedula           = $hoja_actual->getCell("A" . $indice_fila);
        $nombre           = $hoja_actual->getCell("B" . $indice_fila);
        $apellido         = $hoja_actual->getCell("C" . $indice_fila);
        $fecha_nacimiento = $hoja_actual->getCell("D" . $indice_fila)->getCalculatedValue();
        $fecha_nacimiento = ($fecha_nacimiento - 25569) * 86400;
        $fecha_nacimiento = date("Y-m-d", $fecha_nacimiento);
        $departamento     = $hoja_actual->getCell("E" . $indice_fila);
        $sexo             = $hoja_actual->getCell("F" . $indice_fila);
        $edad             = $hoja_actual->getCell("G" . $indice_fila);
        $email            = $hoja_actual->getCell("H" . $indice_fila);


        if ($cedula != "" && $nombre != "" && $apellido != "" && $departamento != "" && $sexo != "" && $edad != "" && $email != "") {

            $usuario_existe = verificar_usuario($cedula);

            if ($usuario_existe == false) {
                $insert = registrar_usuario($cedula, utf8_decode($nombre), utf8_decode($apellido), $fecha_nacimiento, utf8_decode($departamento), $sexo, $edad, $email);

                if (!$insert) {
                    $error_registro++;
                }
            }
        }
    }
}



if ($error_registro != 0) {
    die(json_encode("Ocurrieron errores al registrar $error_registro usuarios"));
} else {
    die(json_encode("Se registrarón los usuarios con éxito"));
}




function verificar_usuario($cedula)
{
    global $conexion;
    $tabla = "usuarios";

    $sql = "SELECT id FROM {$tabla} WHERE cedula = '$cedula'";
    $consulta = mysqli_query($conexion, $sql);
    $resultado = mysqli_num_rows($consulta);

    return $resultado == 0 ? false : true;
}

function registrar_usuario($cedula, $nombre, $apellido, $fecha_nacimiento, $departamento, $sexo, $edad, $email)
{
    global $conexion;
    $tabla = "usuarios";

    $sql = "INSERT INTO {$tabla} (cedula, nombre, apellido, fecha_nacimiento, departamento, sexo, edad, email) VALUES ('$cedula', '$nombre', '$apellido', '$fecha_nacimiento', '$departamento', '$sexo', '$edad', '$email')";

    $consulta = mysqli_query($conexion, $sql);

    return $consulta;
}
