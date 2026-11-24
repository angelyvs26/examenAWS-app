<?php
// App de prueba: conecta EC2 (Docker/Apache) con RDS MySQL usando variables de entorno
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'admin';
$pass = getenv('DB_PASS') ?: '';
$name = getenv('DB_NAME') ?: 'examendb';

$estado = '';
$productos = [];

try {
    $db = new mysqli($host, $user, $pass, $name, 3306);
    $db->set_charset('utf8mb4');
    $estado = "Conectado a RDS: $host";

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['nombre'])) {
        $stmt = $db->prepare('INSERT INTO productos (nombre, precio) VALUES (?, ?)');
        $precio = (float)($_POST['precio'] ?? 0);
        $stmt->bind_param('sd', $_POST['nombre'], $precio);
        $stmt->execute();
        header('Location: /');
        exit;
    }

    $res = $db->query('SELECT id, nombre, precio, creado FROM productos ORDER BY id DESC');
    $productos = $res->fetch_all(MYSQLI_ASSOC);
} catch (mysqli_sql_exception $e) {
    $estado = 'ERROR de conexión: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Examen AWS - EC2 + RDS</title>
  <style>
    body{font-family:Arial,sans-serif;max-width:760px;margin:40px auto;padding:0 16px}
    .ok{color:#0a7d32}.err{color:#b00020}
    table{border-collapse:collapse;width:100%;margin-top:16px}
    th,td{border:1px solid #ccc;padding:6px 10px;text-align:left}
  </style>
</head>
<body>
  <h1>Examen práctico AWS</h1>
  <p>Servidor: <b><?= htmlspecialchars(gethostname()) ?></b></p>
  <p class="<?= str_starts_with($estado, 'ERROR') ? 'err' : 'ok' ?>"><?= htmlspecialchars($estado) ?></p>

  <form method="post">
    <input name="nombre" placeholder="Producto" required>
    <input name="precio" type="number" step="0.01" placeholder="Precio">
    <button>Agregar</button>
  </form>

  <table>
    <tr><th>ID</th><th>Producto</th><th>Precio</th><th>Creado</th></tr>
    <?php foreach ($productos as $p): ?>
    <tr>
      <td><?= $p['id'] ?></td>
      <td><?= htmlspecialchars($p['nombre']) ?></td>
      <td><?= number_format($p['precio'], 2) ?></td>
      <td><?= $p['creado'] ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
</body>
</html>
