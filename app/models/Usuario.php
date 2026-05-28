<?php

require_once __DIR__ . '/../../config/database.php';

class Usuario
{
    private PDO $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function obtenerTodos(): array
    {
        $sql = "SELECT 
                    id,
                    username,
                    email,
                    role,
                    active,
                    created_at
                FROM users
                ORDER BY username ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return $usuarios;
    }

    public function obtenerPorId(int $id): ?array
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return $usuario ?: null;
    }

    public function obtenerPorUsername(string $username): ?array
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->bindValue(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return $usuario ?: null;
    }

    public function crear(string $username, string $password, string $email, string $role, int $active = 1): bool
    {
        // Verificar si el usuario ya existe
        if ($this->obtenerPorUsername($username)) {
            return false;
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $this->conn->prepare(
            "INSERT INTO users (username, password_hash, email, role, active) 
             VALUES (:username, :password_hash, :email, :role, :active)"
        );

        $stmt->bindValue(':username', $username, PDO::PARAM_STR);
        $stmt->bindValue(':password_hash', $passwordHash, PDO::PARAM_STR);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->bindValue(':role', $role, PDO::PARAM_STR);
        $stmt->bindValue(':active', $active, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function actualizar(int $id, string $email, int $active = 1): bool
    {
        $stmt = $this->conn->prepare(
            "UPDATE users SET email = :email, active = :active WHERE id = :id"
        );

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->bindValue(':active', $active, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function cambiarPassword(int $id, string $newPassword): bool
    {
        $passwordHash = password_hash($newPassword, PASSWORD_BCRYPT);

        $stmt = $this->conn->prepare(
            "UPDATE users SET password_hash = :password_hash WHERE id = :id"
        );

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':password_hash', $passwordHash, PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function eliminar(int $id): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function desactivar(int $id): bool
    {
        $stmt = $this->conn->prepare("UPDATE users SET active = 0 WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function activar(int $id): bool
    {
        $stmt = $this->conn->prepare("UPDATE users SET active = 1 WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
