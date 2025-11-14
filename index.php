<?php
/*
 * index.php
 * Archivo que muestra el formulario de inscripción.
 * No guarda datos, solo los envía a guardar.php.
 */

require_once __DIR__ . '/config/Database.php'; // Importamos conexión

$db = new Database();             // Creamos objeto Database
$conn = $db->getConnection();     // Conexión lista

// ===============================
// Cargar países desde la BD
// ===============================
$stmtP = $conn->query("SELECT * FROM paises ORDER BY nombre");
$paises = $stmtP->fetchAll(PDO::FETCH_ASSOC); // Convertimos en arreglo

// ===============================
// Cargar temas tecnológicos para checkbox
// ===============================
$stmtT = $conn->query("SELECT * FROM temas_tecnologicos");
$temas = $stmtT->fetchAll(PDO::FETCH_ASSOC);

// Mensajes que vienen desde guardar.php
$mensaje = $_GET['msg'] ?? "";
$tipo = $_GET['type'] ?? "";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formulario de Inscripción</title>

    <!-- Enlazamos los estilos CSS -->
    <link rel="stylesheet" href="css/estilos.css">
</head>

<body>

<header>
    <!-- Título visible arriba -->
    <h1>Formulario de Inscripción</h1>
</header>

<!-- Formulario que enviará datos a guardar.php -->
<form action="guardar.php" method="post">

    <!-- Mostrar mensaje si lo hay -->
    <?php if ($mensaje): ?>
        <p class="<?= htmlspecialchars($tipo) ?>">
            <?= htmlspecialchars($mensaje) ?>
        </p>
    <?php endif; ?>

    <!-- Campo nombre -->
    <label>Nombre:</label>
    <input type="text" name="nombre" required>

    <!-- Campo apellido -->
    <label>Apellido:</label>
    <input type="text" name="apellido" required>

    <!-- Edad -->
    <label>Edad:</label>
    <input type="number" name="edad" min="1" max="120" required>

    <!-- Sexo -->
    <label>Sexo:</label>
    <select name="sexo" required>
        <option value="">Seleccione...</option>
        <option value="M">Masculino</option>
        <option value="F">Femenino</option>
        <option value="O">Otro</option>
    </select>

    <!-- País -->
    <label>País de Residencia:</label>
    <select name="id_pais" required>
        <option value="">Seleccione...</option>

        <!-- Imprimimos todos los países -->
        <?php foreach ($paises as $p): ?>
            <option value="<?= $p['id_pais'] ?>">
                <?= htmlspecialchars($p['nombre']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <!-- Nacionalidad -->
    <label>Nacionalidad:</label>
    <input type="text" name="nacionalidad" required>

    <!-- Correo -->
    <label>Correo:</label>
    <input type="email" name="correo" required>

    <!-- Celular -->
    <label>Celular:</label>
    <input type="text" name="celular" required>

    <!-- Checkbox: Temas tecnológicos -->
    <label>Tema Tecnológico que le gustaría aprender:</label>

    <!-- Recorremos todos los temas -->
    <?php foreach ($temas as $t): ?>
        <label>
            <!-- Un checkbox por cada tema -->
            <input type="checkbox" name="temas[]" value="<?= $t['id_tema'] ?>">
            <?= htmlspecialchars($t['nombre']) ?>
        </label>
    <?php endforeach; ?>

    <!-- Observaciones -->
    <label>Observaciones:</label>
    <textarea name="observaciones"></textarea>

    <!-- Botones -->
    <div class="botones">
        <button type="reset">Limpiar</button>
        <button type="submit">Enviar</button>
    </div>

</form>

<footer>
    <!-- Footer obligatorio -->
    &copy; <?= date('Y') ?> iTECH. All rights reserved.
    <br>
    <a href="reporte.php" style="color:#ffeb3b;">Ver reporte de inscriptos</a>
</footer>

</body>
</html>
