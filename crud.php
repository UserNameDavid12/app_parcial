<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Conexión a la base de datos
$conn = new mysqli('127.0.0.1', 'root', '1234', 'examen', 3306);
if ($conn->connect_error) die("Conexión fallida: " . $conn->connect_error);

// Procesar acciones del formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['agregar'])) {
        $usuario = $_POST['usuario'];
        $password = $_POST['password'];
        $nombre = $_POST['nombre'];
        $email = $_POST['email'];
        $stmt = $conn->prepare("INSERT INTO usuarios (usuario, password, nombre, email) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $usuario, $password, $nombre, $email);
        if ($stmt->execute()) {
            $_SESSION['mensaje'] = "Usuario agregado correctamente.";
            $_SESSION['tipo_mensaje'] = "success";
        } else {
            $_SESSION['mensaje'] = "Error al agregar usuario: " . $stmt->error;
            $_SESSION['tipo_mensaje'] = "danger";
        }
        $stmt->close();
        header("Location: crud.php");
        exit();
    } 
    elseif (isset($_POST['editar'])) {
        $id = $_POST['id'];
        $usuario = $_POST['usuario'];
        $password = $_POST['password'];
        $nombre = $_POST['nombre'];
        $email = $_POST['email'];
        $stmt = $conn->prepare("UPDATE usuarios SET usuario=?, password=?, nombre=?, email=? WHERE id=?");
        $stmt->bind_param("ssssi", $usuario, $password, $nombre, $email, $id);
        if ($stmt->execute()) {
            $_SESSION['mensaje'] = "Usuario actualizado correctamente.";
            $_SESSION['tipo_mensaje'] = "success";
        } else {
            $_SESSION['mensaje'] = "Error al actualizar usuario: " . $stmt->error;
            $_SESSION['tipo_mensaje'] = "danger";
        }
        $stmt->close();
        header("Location: crud.php");
        exit();
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id=?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Usuario eliminado correctamente.";
        $_SESSION['tipo_mensaje'] = "success";
    } else {
        $_SESSION['mensaje'] = "Error al eliminar usuario: " . $stmt->error;
        $_SESSION['tipo_mensaje'] = "danger";
    }
    $stmt->close();
    header("Location: crud.php");
    exit();
}

$result = $conn->query("SELECT * FROM usuarios ORDER BY id");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD - Panel de Administración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background: #f4f7fc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .navbar-custom .navbar-brand, .navbar-custom .nav-link {
            color: white;
        }
        .card-shadow {
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            background: white;
            border: none;
        }
        .card-shadow:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }
        .table-custom {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        .table-custom thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .table-custom th, .table-custom td {
            vertical-align: middle;
            text-align: center;
            padding: 12px;
        }
        .btn-action {
            border-radius: 20px;
            padding: 5px 15px;
            margin: 0 3px;
            transition: all 0.2s;
        }
        .btn-action:hover {
            transform: scale(1.05);
        }
        .form-control-sm {
            border-radius: 20px;
        }
        .welcome-badge {
            background: rgba(255,255,255,0.2);
            border-radius: 30px;
            padding: 5px 15px;
        }
        footer {
            text-align: center;
            margin-top: 2rem;
            color: #6c757d;
        }
        .btn-add {
            background: linear-gradient(135deg, #28a745, #20c997);
            border: none;
            border-radius: 30px;
            padding: 8px 20px;
            font-weight: bold;
            color: white;
        }
        .btn-add:hover {
            transform: scale(1.02);
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        .alert-fixed {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            animation: fadeOut 3s ease-in-out forwards;
        }
        @keyframes fadeOut {
            0% { opacity: 1; }
            70% { opacity: 1; }
            100% { opacity: 0; visibility: hidden; }
        }
    </style>
</head>
<body>

<!-- Mostrar mensaje flash si existe -->
<?php if (isset($_SESSION['mensaje'])): ?>
<div class="alert alert-<?= $_SESSION['tipo_mensaje'] ?> alert-fixed alert-dismissible fade show" role="alert">
    <i class="fas <?= $_SESSION['tipo_mensaje'] == 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle' ?>"></i>
    <?= $_SESSION['mensaje'] ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']); ?>
<?php endif; ?>

<nav class="navbar navbar-custom navbar-expand-lg mb-4">
    <div class="container">
        <a class="navbar-brand" href="#">
            <i class="fas fa-database"></i> Sistema de Gestión de Usuarios
        </a>
        <div class="ms-auto">
            <span class="welcome-badge">
                <i class="fas fa-user-circle"></i> <?= htmlspecialchars($_SESSION['nombre']) ?>
            </span>
            <a href="logout.php" class="btn btn-outline-light btn-sm ms-2">
                <i class="fas fa-sign-out-alt"></i> Cerrar sesión
            </a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="card card-shadow mb-4">
        <div class="card-body">
            <h4 class="card-title"><i class="fas fa-user-plus"></i> Agregar nuevo usuario</h4>
            <form method="post">
                <div class="row g-3">
                    <div class="col-md-3">
                        <input type="text" name="usuario" class="form-control" placeholder="Usuario" required>
                    </div>
                    <div class="col-md-3">
                        <input type="password" name="password" class="form-control" placeholder="Contraseña" required>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="nombre" class="form-control" placeholder="Nombre completo">
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <input type="email" name="email" class="form-control" placeholder="Correo electrónico">
                            <button type="submit" name="agregar" class="btn btn-add">
                                <i class="fas fa-save"></i> Agregar
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card card-shadow">
        <div class="card-body">
            <h4 class="card-title"><i class="fas fa-users"></i> Listado de usuarios</h4>
            <div class="table-responsive">
                <table class="table table-custom table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Contraseña</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <form method="post" style="margin:0; padding:0;">
                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                <td><?= $row['id'] ?></td>
                                <td><input type="text" name="usuario" value="<?= htmlspecialchars($row['usuario']) ?>" class="form-control form-control-sm" required></td>
                                <td><input type="text" name="password" value="<?= htmlspecialchars($row['password']) ?>" class="form-control form-control-sm" required></td>
                                <td><input type="text" name="nombre" value="<?= htmlspecialchars($row['nombre']) ?>" class="form-control form-control-sm"></td>
                                <td><input type="email" name="email" value="<?= htmlspecialchars($row['email']) ?>" class="form-control form-control-sm"></td>
                                <td class="text-nowrap">
                                    <button type="submit" name="editar" class="btn btn-action btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Editar
                                    </button>
                                    <a href="?delete=<?= $row['id'] ?>" class="btn btn-action btn-danger btn-sm" onclick="return confirm('¿Eliminar este usuario?')">
                                        <i class="fas fa-trash-alt"></i> Eliminar
                                    </a>
                                </td>
                            </form>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <footer>
        <p><i class="fas fa-code"></i> Desarrollo de Soluciones Cloud - Examen Parcial</p>
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>