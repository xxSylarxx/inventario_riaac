<?php

/**
 * Modelo Producto
 * Gestión de productos e inventario
 */

class Producto
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Obtener todos los productos con relaciones
     */
    public function obtenerTodos()
    {
        $stmt = $this->pdo->query("
SELECT p.*, c.nombre as categoria_nombre, pr.nombre as proveedor_nombre
FROM productos p
LEFT JOIN categorias_productos c ON p.categoria_id = c.id
LEFT JOIN proveedores pr ON p.proveedor_id = pr.id
ORDER BY p.nombre ASC
");
        return $stmt->fetchAll();
    }

    /**
     * Obtener producto por ID
     */
    public function obtenerPorId($id)
    {
        $stmt = $this->pdo->prepare("
SELECT p.*, c.nombre as categoria_nombre, pr.nombre as proveedor_nombre
FROM productos p
LEFT JOIN categorias_productos c ON p.categoria_id = c.id
LEFT JOIN proveedores pr ON p.proveedor_id = pr.id
WHERE p.id = ?
");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Obtener productos activos
     */
    public function obtenerActivos()
    {
        $stmt = $this->pdo->query("
SELECT * FROM productos WHERE estado = 'activo' ORDER BY nombre ASC
");
        return $stmt->fetchAll();
    }

    /**
     * Crear producto
     */
    public function crear($datos)
    {
        $stmt = $this->pdo->prepare("
INSERT INTO productos
(nombre, codigo, categoria_id, proveedor_id, stock, precio_compra, fecha_compra, descripcion, estado)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
");

        return $stmt->execute([
            $datos['nombre'],
            $datos['codigo'] ?? null,
            $datos['categoria_id'] ?? null,
            $datos['proveedor_id'] ?? null,
            $datos['stock'] ?? 0,
            $datos['precio_compra'] ?? null,
            $datos['fecha_compra'] ?? null,
            $datos['descripcion'] ?? null,
            $datos['estado'] ?? 'activo'
        ]);
    }

    /**
     * Actualizar producto
     */
    public function actualizar($id, $datos)
    {
        $stmt = $this->pdo->prepare("
UPDATE productos
SET nombre = ?, codigo = ?, categoria_id = ?, proveedor_id = ?,
stock = ?, precio_compra = ?, fecha_compra = ?, descripcion = ?, estado = ?
WHERE id = ?
");

        return $stmt->execute([
            $datos['nombre'],
            $datos['codigo'] ?? null,
            $datos['categoria_id'] ?? null,
            $datos['proveedor_id'] ?? null,
            $datos['stock'],
            $datos['precio_compra'] ?? null,
            $datos['fecha_compra'] ?? null,
            $datos['descripcion'] ?? null,
            $datos['estado'],
            $id
        ]);
    }

    /**
     * Eliminar producto
     */
    public function eliminar($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM productos WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Obtener categorías
     */
    public function obtenerCategorias()
    {
        $stmt = $this->pdo->query("SELECT * FROM categorias_productos ORDER BY nombre ASC");
        return $stmt->fetchAll();
    }

    /**
     * Obtener proveedores
     */
    public function obtenerProveedores()
    {
        $stmt = $this->pdo->query("SELECT * FROM proveedores WHERE estado = 'activo' ORDER BY nombre ASC");
        return $stmt->fetchAll();
    }

    /**
     * Crear categoría
     */
    public function crearCategoria($nombre, $descripcion = null)
    {
        $stmt = $this->pdo->prepare("INSERT INTO categorias_productos (nombre, descripcion) VALUES (?, ?)");
        return $stmt->execute([$nombre, $descripcion]);
    }

    /**
     * Crear proveedor
     */
    public function crearProveedor($datos)
    {
        $stmt = $this->pdo->prepare("
INSERT INTO proveedores (nombre, contacto, whatsapp, email, direccion, estado)
VALUES (?, ?, ?, ?, ?, ?)
");
        return $stmt->execute([
            $datos['nombre'],
            $datos['contacto'] ?? null,
            $datos['whatsapp'] ?? null,
            $datos['email'] ?? null,
            $datos['direccion'] ?? null,
            $datos['estado'] ?? 'activo'
        ]);
    }
}
