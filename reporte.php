<?php
/*
 * reporte.php
 * Muestra un listado de todos los inscriptos en forma de tabla.
 * También muestra los temas seleccionados gracias a GROUP_CONCAT.
 */

require_once __DIR__ . '/config/Database.php'; // Conexión

$db = new Database();
$conn = $db->getConnection(); // Conexión lista

// Seleccionamos todos los datos del inscripto
// También extraemos el nombre del país y los temas seleccionados
$sql = "
SELECT 
    i.*,                                     
    p.nombre AS pais,                         
    GROUP_CONCAT(t.nombre SEPARATOR ', ') AS temas  
FROM inscriptos i
JOIN paises p ON p.id_pais = i.id_pais       
LEFT JOIN inscripto_tema it ON it.id_inscripto = i.id_inscripto
LEFT JOIN temas_tecnologicos t ON t.id_tema = it.id_tema
GROUP BY i.id_inscripto                       
ORDER BY i.fecha_formulario DESC             
";

$stmt = $conn->query($sql);                  // Ejecutamos consulta
$filas = $stmt->fetchAll(PDO::FETCH_ASSOC);  // Convertimos en arreglo
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Inscriptos</title>

    <!-- Enlazamos CSS -->
    <link rel="stylesheet" href="css/estilos.css">
</head>

<body>

<header>
    <!-- Título del reporte -->
    <h1>Reporte de Inscriptos</h1>
</header>

<!-- Contenedor con estilo -->
<main class="reporte-contenedor">

    <!-- Volver al formulario -->
    <a href="index.php">&larr; Volver al formulario</a>

    <h2>Listado de registros guardados</h2>

    <!-- Tabla de datos -->
    <table border="1">

        <!-- Cabeceras de la tabla -->
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Edad</th>
            <th>Sexo</th>
            <th>País</th>
            <th>Nacionalidad</th>
            <th>Temas seleccionados</th>
            <th>Correo</th>
            <th>Celular</th>
            <th>Observaciones</th>
            <th>Fecha</th>
        </tr>

        <!-- Imprimir cada registro -->
        <?php foreach ($filas as $fila): ?>
        <tr>
            <td><?= $fila['id_inscripto'] ?></td>
            <td><?= $fila['nombre'] ?></td>
            <td><?= $fila['apellido'] ?></td>
            <td><?= $fila['edad'] ?></td>
            <td><?= $fila['sexo'] ?></td>
            <td><?= $fila['pais'] ?></td>
            <td><?= $fila['nacionalidad'] ?></td>
            <td><?= $fila['temas'] ?></td>
            <td><?= $fila['correo'] ?></td>
            <td><?= $fila['celular'] ?></td>
            <td><?= $fila['observaciones'] ?></td>
            <td><?= $fila['fecha_formulario'] ?></td>
        </tr>
        <?php endforeach; ?>

    </table>

</main>

<footer>
    &copy; <?= date('Y') ?> iTECH. All rights reserved.
</footer>

</body>
</html>
