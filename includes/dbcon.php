<?php
class Dbcon {
    private $conn;
    private $host = '127.0.0.1';
    private $dbname = 'recipe_sharing_app';
    private $user = 'root';
    private $pass = '';

    public function __construct() {
        $dsn = "mysql:host={$this->host};dbname={$this->dbname}";
        try {
            $this->conn = new PDO($dsn, $this->user, $this->pass);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die('Connection failed: ' . $e->getMessage());
        }
    }

    // Users CRUD
    public function addUser($name, $password, $email, $role, $profile) {
        $sql = "INSERT INTO users (name, password, email, role, profile_img) VALUES (:name, :password, :email, :role, :profile_img)";
        $password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':profile_img', $profile);
        return $stmt->execute();
    }

    public function getUsers() {
        $sql = "SELECT * FROM users";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserByAuth($email, $password) {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (password_verify($password, $user['password'])) {
            unset($user['password']);
        } else {
            $user = null;
        }
        return $user;
    }

    public function getUserById($id) {
        $sql = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateUser($id, $name, $password, $email, $role) {
        $sql = "UPDATE users SET name = :name, password = :password, email = :email, role = :role WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':role', $role);
        return $stmt->execute();
    }

    public function deleteUser($id) {
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }


    // Recipes CRUD
    public function addRecipe($title, $description, $ingredients, $instructions, $cookingTime, $servingSize, $specialInstructions, $userId) {
        $sql = "INSERT INTO recipes (title, description, ingredients, instructions, cooking_time, serving_size, special_instructions, user_id) 
                VALUES (:title, :description, :ingredients, :instructions, :cookingTime, :servingSize, :specialInstructions, :userId)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':ingredients', $ingredients);
        $stmt->bindParam(':instructions', $instructions);
        $stmt->bindParam(':cookingTime', $cookingTime);
        $stmt->bindParam(':servingSize', $servingSize);
        $stmt->bindParam(':specialInstructions', $specialInstructions);
        $stmt->bindParam(':userId', $userId);
        return $stmt->execute();
    }

    public function getRecipesByUser($userId) {
        $sql = "SELECT * FROM recipes WHERE user_id = :userId";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRecipeById($id) {
        $sql = "SELECT * FROM recipes WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateRecipe($id, $title, $description, $ingredients, $instructions, $cookingTime, $servingSize, $specialInstructions) {
        $sql = "UPDATE recipes SET 
                title = :title, 
                description = :description, 
                ingredients = :ingredients, 
                instructions = :instructions, 
                cooking_time = :cookingTime, 
                serving_size = :servingSize, 
                special_instructions = :specialInstructions 
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':ingredients', $ingredients);
        $stmt->bindParam(':instructions', $instructions);
        $stmt->bindParam(':cookingTime', $cookingTime);
        $stmt->bindParam(':servingSize', $servingSize);
        $stmt->bindParam(':specialInstructions', $specialInstructions);
        return $stmt->execute();
    }

    public function deleteRecipe($id) {
        $sql = "DELETE FROM recipes WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}