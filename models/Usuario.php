<?php

/**
 * Modelo Usuario
 * Gestión de usuarios y autenticación
 */

class Usuario
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Autenticar usuario
     */
    public function autenticar($usuario, $password)
    {
        $stmt = $this->pdo->prepare("
SELECT id, usuario, password, nombre, email, rol
FROM usuarios
WHERE usuario = ? AND estado = 'activo'
");
        $stmt->execute([$usuario]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Crear sesión
            $_SESSION['usuario_id'] = $user['id'];
            $_SESSION['usuario'] = $user['usuario'];
            $_SESSION['nombre'] = $user['nombre'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['rol'] = $user['rol'];
            return true;
        }

        return false;
    }

    /**
     * Cerrar sesión
     */
    public function cerrarSesion()
    {
        session_unset();
        session_destroy();
    }

    /**
     * Obtener todos los usuarios
     */
    public function obtenerTodos()
    {
        $stmt = $this->pdo->query("
SELECT id, usuario, nombre, email, rol, estado, fecha_creacion
FROM usuarios
ORDER BY fecha_creacion DESC
");
        return $stmt->fetchAll();
    }

    /**
     * Obtener usuario por ID
     */
    public function obtenerPorId($id)
    {
        $stmt = $this->pdo->prepare("
SELECT id, usuario, nombre, email, rol, estado, fecha_creacion
FROM usuarios
WHERE id = ?
");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Crear usuario
     */
    public function crear($datos)
    {
        $stmt = $this->pdo->prepare("
INSERT INTO usuarios (usuario, password, nombre, email, rol, estado)
VALUES (?, ?, ?, ?, ?, ?)
");

        $password_hash = password_hash($datos['password'], PASSWORD_DEFAULT);

        return $stmt->execute([
            $datos['usuario'],
            $password_hash,
            $datos['nombre'],
            $datos['email'],
            $datos['rol'],
            $datos['estado'] ?? 'activo'
        ]);
    }

    /**
     * Actualizar usuario
     */
    public function actualizar($id, $datos)
    {
        if (isset($datos['password']) && !empty($datos['password'])) {
            $stmt = $this->pdo->prepare("
UPDATE usuarios
SET usuario = ?, password = ?, nombre = ?, email = ?, rol = ?, estado = ?
WHERE id = ?
");
            $password_hash = password_hash($datos['password'], PASSWORD_DEFAULT);
            return $stmt->execute([
                $datos['usuario'],
                $password_hash,
                $datos['nombre'],
                $datos['email'],
                $datos['rol'],
                $datos['estado'],
                $id
            ]);
        } else {
            $stmt = $this->pdo->prepare("
UPDATE usuarios
SET usuario = ?, nombre = ?, email = ?, rol = ?, estado = ?
WHERE id = ?
");
            return $stmt->execute([
                $datos['usuario'],
                $datos['nombre'],
                $datos['email'],
                $datos['rol'],
                $datos['estado'],
                $id
            ]);
        }
    }

    /**
     * Eliminar usuario
     */
    public function eliminar($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM usuarios WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
