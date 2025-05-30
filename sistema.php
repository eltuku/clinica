<?php
session_start();


if (!isset($_SESSION['usuario'])) {
 header("Location: index.php");
    exit();
}

echo "<p>Bienvenido</p>";
echo "<p><a href='logout.php'>Cerrar sesión</a></p>";


?>
<?php
include "con_db.php"; // Incluye el archivo de conexión a la base de datos

// AGREGAR
if (isset($_POST['agregar'])) { // Si se presionó el botón "Agregar"
    $nombre = $_POST['nombre']; // Captura el nombre del paciente
    $fecha = $_POST['fecha'];   // Captura la fecha del turno
    $hora = $_POST['hora'];     // Captura la hora del turno
    $especialidad = $_POST['especialidad']; // Captura la especialidad elegida
    // Inserta el turno en la base de datos
    $conex->query("INSERT INTO turnos (nombre, fecha, hora, especialidad) VALUES ('$nombre', '$fecha', '$hora', '$especialidad')");
    header("Location: sistema.php"); // Redirige a la página principal
    exit;
}

// ELIMINAR
if (isset($_GET['eliminar'])) { // Si se presionó el botón "Eliminar"
    $nombre = $_GET['nombre'];
    $fecha = $_GET['fecha'];
    $hora = $_GET['hora'];
    $especialidad = $_GET['especialidad'];
    // Elimina el turno que coincida exactamente con los datos
    $conex->query("DELETE FROM turnos WHERE nombre='$nombre' AND fecha='$fecha' AND hora='$hora' AND especialidad='$especialidad'");
    header("Location: sistema.php"); // Redirige a la página principal
    exit;
}

// EDITAR - carga datos
$editando = null;
if (isset($_GET['editar'])) { // Si se quiere editar un turno
    $nombre = $_GET['nombre'];
    $fecha = $_GET['fecha'];
    $hora = $_GET['hora'];
    $especialidad = $_GET['especialidad'];
    // Busca el turno a editar
    $res = $conex->query("SELECT * FROM turnos WHERE nombre='$nombre' AND fecha='$fecha' AND hora='$hora' AND especialidad='$especialidad'");
    $editando = $res->fetch_assoc(); // Almacena los datos para precargar en el formulario
}

// EDITAR - guardar cambios
if (isset($_POST['editar'])) { // Si se presionó "Actualizar Turno"
    // Datos originales para identificar el turno
    $original_nombre = $_POST['original_nombre'];
    $original_fecha = $_POST['original_fecha'];
    $original_hora = $_POST['original_hora'];
    $original_especialidad = $_POST['original_especialidad'];

    // Nuevos datos
    $nombre = $_POST['nombre'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $especialidad = $_POST['especialidad'];

    // Elimina el turno original
    $conex->query("DELETE FROM turnos WHERE nombre='$original_nombre' AND fecha='$original_fecha' AND hora='$original_hora' AND especialidad='$original_especialidad'");
    // Inserta el turno actualizado
    $conex->query("INSERT INTO turnos (nombre, fecha, hora, especialidad) VALUES ('$nombre', '$fecha', '$hora', '$especialidad')");
    header("Location: sistema.php"); // Redirige
    exit;
}

// FILTRO
$filtro = $_GET['filtro'] ?? ''; // Toma el filtro si existe
$sql = "SELECT * FROM turnos"; // Consulta base
if ($filtro != '') {
    $sql .= " WHERE especialidad = '$filtro'"; // Filtra por especialidad
}
$sql .= " ORDER BY fecha, hora"; // Ordena por fecha y hora
$turnos = $conex->query($sql); // Ejecuta la consulta

// Lista de especialidades posibles
$opciones = ['Clínico', 'Pediatría', 'Cardiología', 'Dermatología'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestión de Turnos Médicos</title>
  <script src="https://cdn.tailwindcss.com"></script> <!-- Estilos con Tailwind CSS -->
</head>
<body class="bg-gray-100 min-h-screen p-8">
  <div class="max-w-5xl mx-auto">
    <h1 class="text-3xl font-bold text-blue-700 mb-6">Gestión de Turnos Médicos</h1>

    <!-- FORMULARIO -->
    <form method="POST" class="bg-white p-6 rounded-xl shadow grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
      <!-- Campos ocultos para saber qué turno se está editando -->
      <input type="hidden" name="original_nombre" value="<?= $editando['nombre'] ?? '' ?>">
      <input type="hidden" name="original_fecha" value="<?= $editando['fecha'] ?? '' ?>">
      <input type="hidden" name="original_hora" value="<?= $editando['hora'] ?? '' ?>">
      <input type="hidden" name="original_especialidad" value="<?= $editando['especialidad'] ?? '' ?>">

      <!-- Campo paciente -->
      <div>
        <label class="block font-semibold">Paciente</label>
        <input type="text" name="nombre" required class="w-full p-2 border rounded" value="<?= $editando['nombre'] ?? '' ?>">
      </div>

      <!-- Campo fecha con restricción: no se puede elegir una fecha anterior a hoy -->
      <div>
        <label class="block font-semibold">Fecha</label>
        <input 
          type="date" 
          name="fecha" 
          required 
          class="w-full p-2 border rounded" 
          value="<?= $editando['fecha'] ?? '' ?>"
          min="<?= date('Y-m-d') ?>" <!-- Aquí se bloquean fechas anteriores a hoy -->
        
      </div>

      <!-- Campo hora -->
      <div>
        <label class="block font-semibold">Hora</label>
        <input type="time" name="hora" required class="w-full p-2 border rounded" value="<?= $editando['hora'] ?? '' ?>">
      </div>

      <!-- Campo especialidad -->
      <div>
        <label class="block font-semibold">Especialidad</label>
        <select name="especialidad" required class="w-full p-2 border rounded">
          <?php foreach ($opciones as $esp): ?>
            <option value="<?= $esp ?>" <?= ($editando && $editando['especialidad'] == $esp) ? 'selected' : '' ?>>
              <?= $esp ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Botón de agregar o actualizar -->
      <div class="md:col-span-2">
        <button type="submit" name="<?= $editando ? 'editar' : 'agregar' ?>" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded">
          <?= $editando ? 'Actualizar Turno' : 'Agregar Turno' ?>
        </button>
      </div>
    </form>

    <!-- FILTRO -->
    <form method="GET" class="mb-4 flex items-center gap-4">
      <label class="font-semibold">Filtrar por especialidad:</label>
      <select name="filtro" class="p-2 border rounded">
        <option value="">-- Todas --</option>
        <?php foreach ($opciones as $esp): ?>
          <option value="<?= $esp ?>" <?= $filtro == $esp ? 'selected' : '' ?>><?= $esp ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="bg-blue-500 text-white px-4 py-1 rounded">Filtrar</button>
      <a href="sistema.php" class="text-sm text-gray-500 underline">Quitar filtro</a>
    </form>

    <!-- TABLA -->
    <table class="w-full bg-white rounded-xl shadow overflow-hidden text-sm">
      <thead class="bg-blue-600 text-white">
        <tr>
          <th class="p-3 text-left">Paciente</th>
          <th class="p-3 text-left">Fecha</th>
          <th class="p-3 text-left">Hora</th>
          <th class="p-3 text-left">Especialidad</th>
          <th class="p-3 text-left">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($turno = $turnos->fetch_assoc()): ?>
          <tr class="border-t hover:bg-gray-50">
            <td class="p-3"><?= $turno['nombre'] ?></td>
            <td class="p-3"><?= $turno['fecha'] ?></td>
            <td class="p-3"><?= $turno['hora'] ?></td>
            <td class="p-3"><?= $turno['especialidad'] ?></td>
            <td class="p-3 flex gap-2">
              <!-- Enlace para editar con todos los datos del turno -->
              <a href="?editar=1&nombre=<?= urlencode($turno['nombre']) ?>&fecha=<?= $turno['fecha'] ?>&hora=<?= $turno['hora'] ?>&especialidad=<?= urlencode($turno['especialidad']) ?>" class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded">Editar</a>
              <!-- Enlace para eliminar con confirmación -->
              <a href="?eliminar=1&nombre=<?= urlencode($turno['nombre']) ?>&fecha=<?= $turno['fecha'] ?>&hora=<?= $turno['hora'] ?>&especialidad=<?= urlencode($turno['especialidad']) ?>" onclick="return confirm('¿Eliminar este turno?')" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">Eliminar</a>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</body>
</html>