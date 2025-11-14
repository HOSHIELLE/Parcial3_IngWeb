<?php
/*
 * guardar.php
 * -----------
 * Este archivo recibe los datos enviados desde index.php.
 * Aquí se realizan TODAS las VALIDACIONES del lado del servidor (PHP)
 * y se guardan los datos del formulario en la base de datos.
 */

require_once __DIR__ . '/config/Database.php'; // Importamos la clase de conexión

$db = new Database();              // Creamos objeto de conexión
$conn = $db->getConnection();      // Obtenemos la conexión a MySQL

$errores = [];                     // Aquí almacenaremos los mensajes de error

// ================================================================
// 1. RECIBIR LOS DATOS DEL FORMULARIO
// ================================================================

// Usamos trim() para eliminar espacios antes/después del texto
$nombre = trim($_POST['nombre'] ?? "");
$apellido = trim($_POST['apellido'] ?? "");

// Convertimos edad a número entero
$edad = intval($_POST['edad'] ?? 0);

// Sexo seleccionado (M, F, O)
$sexo = $_POST['sexo'] ?? "";

// País seleccionado
$id_pais = intval($_POST['id_pais'] ?? 0);

// Nacionalidad ingresada
$nacionalidad = trim($_POST['nacionalidad'] ?? "");

// Correo y celular
$correo = trim($_POST['correo'] ?? "");
$celular = trim($_POST['celular'] ?? "");

// Observaciones (opcional)
$observaciones = trim($_POST['observaciones'] ?? "");

// Temas tecnológicos seleccionados (checkbox)
$temasSeleccionados = $_POST['temas'] ?? [];  // Si no hay nada, será un arreglo vacío

// ================================================================
// 2. VALIDACIONES DEL LADO DEL SERVIDOR (PHP)
// ================================================================

// Validar nombre vacío
if ($nombre === "") {
    $errores[] = "El nombre es obligatorio.";
}

// Validar apellido vacío
if ($apellido === "") {
    $errores[] = "El apellido es obligatorio.";
}

// Validar rango válido de edad
if ($edad < 1 || $edad > 120) {
    $errores[] = "Edad inválida, debe estar entre 1 y 120.";
}

// Validar sexo válido
if (!in_array($sexo, ['M','F','O'])) {
    $errores[] = "Seleccione un sexo válido.";
}

// Validar país seleccionado
if ($id_pais <= 0) {
    $errores[] = "Seleccione un país.";
}

// Validar nacionalidad no vacía
if ($nacionalidad === "") {
    $errores[] = "La nacionalidad es obligatoria.";
}

// Validar formato del correo
if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    $errores[] = "El formato del correo es inválido.";
}

// Validar celular no vacío
if ($celular === "") {
    $errores[] = "Debe ingresar un número celular.";
}

// Validar que haya seleccionado al menos un tema (checkbox)
if (empty($temasSeleccionados)) {
    $errores[] = "Seleccione al menos un tema tecnológico.";
}

// ================================================================
// 3. SI HAY ERRORES → REGRESAMOS AL FORMULARIO CON EL MENSAJE
// ================================================================
if (!empty($errores)) {

    // Convertimos arreglo de errores en una sola cadena de texto
    $msg = implode(" ", $errores);

    // Redireccionamos a index.php enviando mensaje y tipo error
    header("Location: index.php?msg=" . urlencode($msg) . "&type=error");
    exit; // Importante para detener ejecución de este archivo
}

// ================================================================
// 4. FORMATEAR NOMBRE Y APELLIDO (Mayúscula inicial)
// ================================================================
$nombre = ucwords(strtolower($nombre));
$apellido = ucwords(strtolower($apellido));

// ================================================================
// 5. GUARDAR LOS DATOS EN LA BASE DE DATOS
// ================================================================

try {

    // Iniciamos transacción porque haremos inserción en 2 tablas
    $conn->beginTransaction();

    // Preparamos INSERT para guardar la información principal
    $sql = "INSERT INTO inscriptos 
            (nombre, apellido, edad, sexo, id_pais, nacionalidad, correo, celular, observaciones, fecha_formulario)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

    $stmt = $conn->prepare($sql); // Preparamos la sentencia

    // Ejecutamos el insert con los valores del formulario
    $stmt->execute([
        $nombre,
        $apellido,
        $edad,
        $sexo,
        $id_pais,
        $nacionalidad,
        $correo,
        $celular,
        $observaciones
    ]);

    // Obtenemos el ID del registro recién insertado
    $id_inscripto = $conn->lastInsertId();

    // ============================================
    // Guardar los temas seleccionados (checkbox)
    // ============================================

    // Preparamos el insert de la tabla puente
    $sqlTema = $conn->prepare("INSERT INTO inscripto_tema (id_inscripto, id_tema) VALUES (?, ?)");

    // Recorremos cada checkbox seleccionado
    foreach ($temasSeleccionados as $tema) {

        // Insertamos un registro relación inscripto ↔ tema
        $sqlTema->execute([$id_inscripto, $tema]);
    }

    // Confirmamos los cambios en la BD
    $conn->commit();

    // Redirigimos con mensaje de éxito
    header("Location: index.php?msg=" . urlencode("Inscripción guardada correctamente.") . "&type=ok");

} catch (Exception $e) {

    // Si algo falla, revertimos todo
    $conn->rollBack();

    // Mandamos mensaje de error
    header("Location: index.php?msg=Error al guardar datos&type=error");
}