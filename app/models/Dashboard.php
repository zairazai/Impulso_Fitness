<?php

require_once __DIR__ . '/../../config/database.php';

class Dashboard
{
    private PDO $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function contarSociosActivos(): int
    {
        $stmt = $this->conn->prepare(
            'SELECT COUNT(*) AS total FROM socios WHERE estado = :estado'
        );
        $estado = 'activo';
        $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return (int)($data['total'] ?? 0);
    }

    public function contarVentasDelDia(): int
    {
        $stmt = $this->conn->prepare(
            'SELECT COUNT(*) AS total FROM ventas WHERE DATE(fecha) = CURRENT_DATE()'
        );
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return (int)($data['total'] ?? 0);
    }

    public function contarProductosActivos(): int
    {
        $stmt = $this->conn->prepare(
            'SELECT COUNT(*) AS total FROM productos WHERE activo = 1'
        );
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return (int)($data['total'] ?? 0);
    }
}
