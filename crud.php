<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$conn = new mysqli('127.0.0.1', 'root', '1234', 'examen', 3306);
if ($conn->connect_error) die("Conexión fallida: " . $conn->connect_error);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['agregar'])) {
        $usuario = $_POST['usuario'];
        $password = $_POST['password'];
        $nombre = $_POST['nombre'];
        $email = $_POST['email'];
        $stmt = $conn->prepare("INSERT INTO usuarios (usuario, password, nombre, email) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $usuario, $password, $nombre, $email);
        $stmt->execute();
    } elseif (isset($_POST['editar'])) {
        $id = $_POST['id'];
        $usuario = $_POST['usuario'];
        $password = $_POST['password'];
        $nombre = $_POST['nombre'];
        $email = $_POST['email'];
        $stmt = $conn->prepare("UPDATE usuarios SET usuario=?, password=?, nombre=?, email=? WHERE id=?");
        $stmt->bind_param("ssssi", $usuario, $password, $nombre, $email, $id);
        $stmt->execute();
    }
    header("Location: crud.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: crud.php");
    exit();
}

$result = $conn->query("SELECT * FROM usuarios ORDER BY id");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CRUD Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
    <h2>CRUD de Usuarios</h2>
    <p>Bienvenido, <?= htmlspecialchars($_SESSION['nombre']) ?> (<a href="logout.php">Cerrar sesión</a>)</p>

    <div class="card mb-4">
        <div class="card-header">Agregar nuevo usuario</div>
        <div class="card-body">
            <form method="post">
                <div class="row">
                    <div class="col-md-3"><input type="text" name="usuario" class="form-control" placeholder="Usuario" required></div>
                    <div class="col-md-3"><input type="password" name="password" class="form-control" placeholder="Contraseña" required></div>
                    <div class="col-md-3"><input type="text" name="nombre" class="form-control" placeholder="Nombre"></div>
                    <div class="col-md-3"><input type="email" name="email" class="form-control" placeholder="Email"></div>
                </div>
                <button type="submit" name="agregar" class="btn btn-primary mt-2">Agregar</button>
            </form>
        </div>
    </div>

    <table class="table table-bordered table-striped">
        <thead>
            <tr><th>ID</th><th>Usuario</th><th>Contraseña</th><th>Nombre</th><th>Email</th><th>Acciones</th></tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <form method="post" style="display: inline-block;">
                <tr>
                    <input type="hidden" name="id" value="<?= $row['id'] ?>"><?= $row['id'] ?>
                </td>
                <td><input type="text" name="usuario" value="<?= htmlspecialchars($row['usuario']) ?>" class="form-control form-control-sm"></td>
                <td><input type="text" name="password" value="<?= htmlspecialchars($row['password']) ?>" class="form-control form-control-sm"></td>
                <td><input type="text" name="nombre" value="<?= htmlspecialchars($row['nombre']) ?>" class="form-control form-control-sm"></td>
                <td><input type="email" name="email" value="<?= htmlspecialchars($row['email']) ?>" class="form-control form-control-sm"></td>
                <td>
                    <button type="submit" name="editar" class="btn btn-sm btn-warning">Editar</button>
                    <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar?')">Eliminar</a>
                </td>
                </form>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>