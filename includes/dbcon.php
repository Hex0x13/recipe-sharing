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

    public function userExists($userId) {
        $sql = "SELECT COUNT(*) FROM users WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
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

    public function updateUser($id, $name, $password, $email, $role, $profile) {
        $sql = "UPDATE users SET ";
        $params = [];
        
        if (isset($name)) {
            $sql .= "name = :name, ";
            $params[':name'] = $name;
        }
        if (isset($password)) {
            $sql .= "password = :password, ";
            $params[':password'] = $password;
        }
        if (isset($email)) {
            $sql .= "email = :email, ";
            $params[':email'] = $email;
        }
        if (isset($role)) {
            $sql .= "role = :role, ";
            $params[':role'] = $role;
        }

        if (isset($profile)) {
            $sql .= "profile_img = :profile";
            $params[':profile'] = $profile;
        }       
        // Remove trailing comma and space
        $sql = rtrim($sql, ', ');
        
        $sql .= " WHERE id = :id";
        $params[':id'] = $id;
        
        $stmt = $this->conn->prepare($sql);
        
        // Bind parameters dynamically
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        return $stmt->execute();
    }


    public function deleteUser($id) {
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Recipes CRUD
// Recipes CRUD
// Recipes CRUD
    public function addRecipe($title, $description, $ingredients, $instructions, $cookingTime, $servingSize, $userId, $categoryName, $imageUrls) {
        // First, check if the category exists
        $categoryId = $this->getCategoryIdByName($categoryName);
        
        // If the category doesn't exist, create it
        if ($categoryId === null) {
            $categoryId = $this->createCategory($categoryName);
        }

        // Insert the recipe with the retrieved or created category ID
        $sql = "INSERT INTO recipes (title, description, ingredients, instructions, cooking_time, serving_size, user_id) 
                VALUES (:title, :description, :ingredients, :instructions, :cookingTime, :servingSize, :userId)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':ingredients', $ingredients);
        $stmt->bindParam(':instructions', $instructions);
        $stmt->bindParam(':cookingTime', $cookingTime);
        $stmt->bindParam(':servingSize', $servingSize);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();

        // Retrieve the ID of the newly inserted recipe
        $recipeId = $this->conn->lastInsertId();

        // Associate the recipe with the category
        $this->associateRecipeWithCategory($recipeId, $categoryId);

        // Insert images for the recipe
        foreach ($imageUrls as $imageUrl) {
            $this->addRecipeImage($imageUrl, $recipeId);
        }

        return true;
    }

    private function associateRecipeWithCategory($recipeId, $categoryId) {
        // Insert a new entry in the recipe_categories table to associate the recipe with the category
        $sql = "INSERT INTO recipe_categories (recipe_id, category_id) VALUES (:recipeId, :categoryId)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':recipeId', $recipeId);
        $stmt->bindParam(':categoryId', $categoryId);
        $stmt->execute();
    }

    private function createCategory($categoryName) {
        // Insert a new category
        $sql = "INSERT INTO categories (name) VALUES (:categoryName)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':categoryName', $categoryName);
        if ($stmt->execute()) {
            // Return the ID of the newly created category
            return $this->conn->lastInsertId();
        } else {
            // Return null if failed to create the category
            return null;
        }
    }

    // Helper method to retrieve category ID by name
    private function getCategoryIdByName($categoryName) {
        $sql = "SELECT id FROM categories WHERE name = :name";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':name', $categoryName);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['id'] : null;
    }

    public function getRecipes() {
        $sql = "SELECT recipes.*, categories.name AS category_name
                FROM recipes
                JOIN recipe_categories ON recipes.id = recipe_categories.recipe_id
                JOIN categories ON recipe_categories.category_id = categories.id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRecipeById($id) {
        $sql = "SELECT recipes.*, categories.name AS category FROM recipes 
                JOIN recipe_categories ON recipes.id = recipe_categories.recipe_id
                JOIN categories ON recipe_categories.category_id = categories.id 
                WHERE recipes.id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getRecipeList() {
        $sql = "
        SELECT 
            DISTINCT r.id AS recipe_id,
            r.title,
            r.description ,
            r.ingredients,
            r.instructions,
            r.cooking_time,
            r.serving_size,
            r.special_instructions,
            c.name AS category_name,
            ri.image_url AS image_url
        FROM 
            recipes r
        INNER JOIN 
            users u ON r.user_id = u.id
        INNER JOIN 
            recipe_categories rc ON r.id = rc.recipe_id
        INNER JOIN 
            categories c ON rc.category_id = c.id
        LEFT JOIN 
            recipe_image ri ON r.id = ri.recipe_id;
        ";
        
        // Prepare and execute statement
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        // Fetch all rows
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getRecipeByIdWIthImg($id) {
        $sql = "
        SELECT 
            r.id AS recipe_id,
            r.title,
            r.description ,
            r.ingredients,
            r.instructions,
            r.cooking_time,
            r.serving_size,
            r.special_instructions,
            c.name AS category_name,
            ri.image_url AS image_url
        FROM 
            recipes r
        INNER JOIN 
            users u ON r.user_id = u.id
        INNER JOIN 
            recipe_categories rc ON r.id = rc.recipe_id
        INNER JOIN 
            categories c ON rc.category_id = c.id
        LEFT JOIN 
            recipe_image ri ON r.id = ri.recipe_id
        WHERE r.id = ? LIMIT 1
        ";
        
        // Prepare and execute statement
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        // Fetch all rows
        $result = $stmt->fetch();
        return $result;
    }

    public function getRecipesByUser($userId) {
        $sql = "SELECT * FROM recipes WHERE user_id = :userId";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function updateRecipe($id, $title, $description, $ingredients, $instructions, $cookingTime, $servingSize, $categoryName) {
        // Prepare the SQL statement for updating the recipe fields
        $sql = "UPDATE recipes SET ";
        $params = [];

        if (!empty($title)) {
            $sql .= "title = :title, ";
            $params[':title'] = $title;
        }
        if (!empty($description)) {
            $sql .= "description = :description, ";
            $params[':description'] = $description;
        }
        if (!empty($ingredients)) {
            $sql .= "ingredients = :ingredients, ";
            $params[':ingredients'] = $ingredients;
        }
        if (!empty($instructions)) {
            $sql .= "instructions = :instructions, ";
            $params[':instructions'] = $instructions;
        }
        if (!empty($cookingTime)) {
            $sql .= "cooking_time = :cookingTime, ";
            $params[':cookingTime'] = $cookingTime;
        }
        if (!empty($servingSize)) {
            $sql .= "serving_size = :servingSize, ";
            $params[':servingSize'] = $servingSize;
        }

        // Remove trailing comma and space
        $sql = rtrim($sql, ', ');

        // Add WHERE clause to update only the specified recipe
        $sql .= " WHERE id = :id";
        $params[':id'] = $id;

        // Prepare and execute the SQL statement to update recipe fields
        $stmt = $this->conn->prepare($sql);
        
        // Bind parameters dynamically
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $success = $stmt->execute();

        // Update the category if provided
        if (!empty($categoryName)) {
            $categoryId = $this->getCategoryIdByName($categoryName);
            if ($categoryId !== null) {
                $updateCategorySql = "UPDATE recipe_categories SET category_id = :categoryId WHERE recipe_id = :recipeId";
                $updateCategoryStmt = $this->conn->prepare($updateCategorySql);
                $updateCategoryStmt->bindParam(':categoryId', $categoryId);
                $updateCategoryStmt->bindParam(':recipeId', $id);
                $updateCategoryStmt->execute();
            }
        }

        return $success;
    }


    public function deleteRecipe($id) {
        $sql = "DELETE FROM recipes WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function getNumberOfUsers() {
        $sql = "SELECT COUNT(*) AS user_count FROM users";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['user_count'];
    }

    // Function to get the number of recipes
    public function getNumberOfRecipes() {
        $sql = "SELECT COUNT(*) AS recipe_count FROM recipes";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['recipe_count'];
    }
    public function addRecipeImage($imageUrl, $recipeId) {
        $sql = "INSERT INTO recipe_image (image_url, recipe_id) VALUES (:imageUrl, :recipeId)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':imageUrl', $imageUrl);
        $stmt->bindParam(':recipeId', $recipeId);
        return $stmt->execute();
    }

}