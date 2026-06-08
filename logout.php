<?php
session_start();
session_destroy();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cerrando sesión...</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .logout-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            animation: fadeIn 0.5s ease;
        }
        .logout-card i {
            font-size: 4rem;
            color: #28a745;
            margin-bottom: 1rem;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
    </style>
    <meta http-equiv="refresh" content="2;url=index.php">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="logout-card">
                    <i class="fas fa-check-circle"></i>
                    <h3>¡Sesión cerrada exitosamente!</h3>
                    <p>Serás redirigido al login en unos segundos...</p>
                    <a href="index.php" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Ir ahora
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>