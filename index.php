<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    $conn = new mysqli('127.0.0.1', 'root', '1234', 'examen', 3306);
    if ($conn->connect_error) die("Conexión fallida: " . $conn->connect_error);

    $stmt = $conn->prepare("SELECT id, usuario, nombre, password FROM usuarios WHERE usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        if ($password == $row['password']) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['usuario'] = $row['usuario'];
            $_SESSION['nombre'] = $row['nombre'];
            header("Location: crud.php");
            exit();
        } else {
            $error = "Contraseña incorrecta";
        }
    } else {
        $error = "Usuario no existe";
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Examen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5" style="max-width: 400px;">
        <div class="card">
            <div class="card-header">Iniciar Sesión</div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                <form method="post">
                    <div class="mb-3">
                        <label>Usuario</label>
                        <input type="text" name="usuario" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Contraseña</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Ingresar</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>