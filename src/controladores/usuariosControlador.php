<?php

use src\modelos\usuariosModelo;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["accion"]) && isset($_SESSION['cedula'])) {

    $accion = $_POST["accion"];
    $cedula = $_POST['cedula_usuario'] ?? "";
    $nombre = $_POST['nombre_usuario'] ?? "";
    $apellido = $_POST['apellido_usuario'] ?? "";
    $correo = $_POST['correo_usuario'] ?? "";
    $telefono = $_POST['telefono_usuario'] ?? "";
    $rol = $_POST['id_rol'] ?? "";
    $usuario = $_POST['usuario_usuario'] ?? "";
    $contrasena1 = $_POST['contrasena1_usuario'] ?? "";
    $contrasena2 = $_POST['contrasena2_usuario'] ?? "";
    $foto = $_FILES['foto_usuario'] ?? "";

    $objeto = new usuariosModelo();
    ob_clean();
    switch ($accion) {
        case "listar":
            $resultado = $objeto->seleccionarUsuarios();
            echo json_encode($resultado);
            exit();
        case "registrar":
            $resultado = $objeto->registrarUsuarios(
                $cedula,
                $rol,
                $nombre,
                $apellido,
                $telefono,
                $correo,
                $usuario,
                $contrasena1,
                $contrasena2
            );
            echo json_encode($resultado);
            exit();
        case "eliminar":
            $resultado = $objeto->eliminarUsuarios($cedula);
            echo json_encode($resultado);
            exit();
        case "seleccionarUno":
            $resultado = $objeto->seleccionarUsuarios($cedula);
            echo json_encode($resultado);
            exit();
        case "actualizar":
            $resultado = $objeto->actualizarUsuarios(
                $cedula,
                $nombre,
                $apellido,
                $correo,
                $telefono,
                $rol,
                $usuario,
                $contrasena1,
                $contrasena2
            );
            echo json_encode($resultado);
            exit();
        case "cerrarSesion":
            $resultado = $objeto->cerrarSesionUsuarios();
            echo json_encode($resultado);
            exit();
        default:
            echo json_encode(["error" => "Acción no reconocida"]);
            exit();
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "GET" && isset($url2) && $url2 != "") {
    if (isset($url2) && $url2 != "") {
        if (is_file("src/vistas/usuarios/".$url2.".php")) {
            if ($url2 == "dashboard" && isset($_SESSION['rol'])) {
                require_once "src/config/inc/header.php";
                require_once "src/config/inc/sidebar.php";
                require_once "src/vistas/usuarios/".$url2.".php";
            }else {
                require_once "src/vistas/usuarios/" . $url2 . ".php";
            }
            $_SESSION['vistaActual'] = $url2;
        } else {
            require_once "src/vistas/others/404.php";
            $_SESSION['vistaActual'] = '';
        }
    } else {
        require_once "src/vistas/others/404.php";
        $_SESSION['vistaActual'] = '';
    }
} elseif (isset($_SESSION['rol'])) {
    //Cuando el usuario ya inicio sesión y va al modulo de usuarios
    require_once "src/config/inc/header.php";
    require_once "src/config/inc/sidebar.php";
    require_once "src/vistas/usuarios/usuarios.php";
    $_SESSION['vistaActual'] = 'usuarios';
} else {
    
    $accion = $_POST['accion'];
    $cedula = $_POST['cedula_usuario'] ?? "";
    $nombre = $_POST['nombre_usuario'] ?? "";
    $apellido = $_POST['apellido_usuario'] ?? "";
    $correo = $_POST['correo_usuario'] ?? "";
    $telefono = $_POST['telefono_usuario'] ?? "";
    $rol = $_POST['id_rol'] ?? 2;
    $usuario = $_POST['usuario_usuario'] ?? "";
    $contrasena1 = $_POST['contrasena1_usuario'] ?? "";
    $contrasena2 = $_POST['contrasena2_usuario'] ?? "";

    $objeto = new usuariosModelo();
    if (isset($_POST['accion'])) {
        
        ob_clean();
        switch ($accion) {
            case 'iniciarSesion':
                $resultado = $objeto->iniciarSesionUsuarios($usuario, $contrasena1);
                echo json_encode($resultado);
                exit();
            case 'registrar':
                $resultado = $objeto->registrarUsuarios(
                    $cedula,
                    $rol,
                    $nombre,
                    $apellido,
                    $telefono,
                    $correo,
                    $usuario,
                    $contrasena1,
                    $contrasena2
                );
                if ($resultado['icono'] == 'success') {
                    $resultado['tipo'] = 'alertarYredireccionar';
                    $resultado['url'] = APP_URL;
                }
                echo json_encode($resultado);
                exit();
            default:
                header("Location:" . APP_URL);
                exit();
        }
    } else {
        header("Location:" . APP_URL);
        exit();
    }
}
