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
        
        if (!empty($name)) {
            $sql .= "name = :name, ";
            $params[':name'] = $name;
        }
        if (!empty($password)) {
            $sql .= "password = :password, ";
            $params[':password'] = $password;
        }
        if (!empty($email)) {
            $sql .= "email = :email, ";
            $params[':email'] = $email;
        }
        if (!empty($role)) {
            $sql .= "role = :role, ";
            $params[':role'] = $role;
        }

        if (!empty($profile)) {
            $sql .= "profile_img = :profile";
            $params[':profile'] = $profile;
            $this->deleteProfileImage($id);
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
        $this->deleteProfileImage($id);
        return $stmt->execute();
    }

    public function addRecipe($title, $description, $ingredients, $instructions, $specialInstructions, $cookingTime, $servingSize, $userId, $categoryName, $imageUrls) {
        // First, check if the category exists
        $categoryId = $this->getCategoryIdByName($categoryName);
        
        // If the category doesn't exist, create it
        if ($categoryId === null) {
            $categoryId = $this->createCategory($categoryName);
        }

        // Insert the recipe with the retrieved or created category ID
        $sql = "INSERT INTO recipes (title, description, ingredients, instructions, special_instructions, cooking_time, serving_size, user_id) 
                VALUES (:title, :description, :ingredients, :instructions, :special_instructions, :cookingTime, :servingSize, :userId)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':ingredients', $ingredients);
        $stmt->bindParam(':instructions', $instructions);
        $stmt->bindParam(':special_instructions', $specialInstructions);
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
        $sql = "SELECT 
                    DISTINCT r.id AS recipe_id,
                    r.title,
                    r.description ,
                    r.ingredients,
                    r.instructions,
                    r.cooking_time,
                    r.serving_size,
                    r.special_instructions,
                    c.name AS category_name,
                    COALESCE(ri.image_url, './assets/dist/img/no-image.png') AS image_url
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
        $sql = "SELECT 
                    r.id AS recipe_id,
                    r.title,
                    r.description ,
                    r.ingredients,
                    r.instructions,
                    r.cooking_time,
                    r.serving_size,
                    r.special_instructions,
                    c.name AS category_name,
                    COALESCE(ri.image_url, './assets/dist/img/no-image.png') AS image_url
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
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getRecipesByUser($userId) {
        $sql = "SELECT * FROM recipes WHERE user_id = :userId";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function updateRecipe($id, $title, $description, $ingredients, $instructions, $specialInstructions, $cookingTime, $servingSize, $categoryName, $imageUrls) {
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
        if (!empty($specialInstructions)) {
            $sql .= "special_instructions = :specialInstructions, ";
            $params[':specialInstructions'] = $specialInstructions;
        }

        // Remove trailing comma and space
        $sql = rtrim($sql, ', ');

        // Add WHERE clause to update only the specified recipe
        $sql .= " WHERE id = :id";
        $params[':id'] = $id;

        if (!empty($imageUrls)) {
            $this->deleteRecipeImages($id);
        }
        $stmt = $this->conn->prepare($sql);

        // Bind parameters dynamically
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $success = $stmt->execute();
        $this->removeUnusedCategory();

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
        // Insert new images for the recipe
        foreach ($imageUrls as $imageUrl) {
            $this->addRecipeImage($imageUrl, $id);
        }

        return $success;
    }



    public function deleteRecipe($id) {
        $sql = "DELETE FROM recipes WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $this->deleteRecipeImages($id);
        $result = $stmt->execute();
        $this->removeUnusedCategory();
        return $result;
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

    public function deleteRecipeImages($recipeId) {
        // First, retrieve the image paths for the given recipe
        $sql = "SELECT image_url FROM recipe_image WHERE recipe_id = :recipeId";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':recipeId', $recipeId);
        $stmt->execute();
        $imagePaths = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Delete the image files from the server
        foreach ($imagePaths as $imagePath) {
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        // Delete the entries from the recipe_image table
        $sql = "DELETE FROM recipe_image WHERE recipe_id = :recipeId";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':recipeId', $recipeId);
        return $stmt->execute();
    }

    function deleteProfileImage($user_id) {
        $sql = "SELECT profile_img FROM users WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $user_id);
        $stmt->execute();
        $imagePath = $stmt->fetch()['profile_img'];
        if (!empty($imagePath) && file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    function getReviewsByUser($recipe_id, $user_id) {
        if (!empty($user_id) && $this->userExists($user_id)) {
            $sql = "SELECT * FROM reviews WHERE recipe_id = :recipe_id AND user_id = :user_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':recipe_id', $recipe_id); // Bind recipe_id as integer
            $stmt->bindParam(':user_id', $user_id); // Bind user_id as integer
            $stmt->execute();
            $review = $stmt->fetch(PDO::FETCH_ASSOC);
            return $review;
        } else {
            $key = "recipe-sharing-app-user-review-id";
            $id = filter_input(INPUT_COOKIE, $key, FILTER_VALIDATE_INT);
            if (isset($id)) {
                $sql = "SELECT * FROM reviews WHERE id = :id";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(':id', $id); // Bind ID as integer
                $stmt->execute();
                return $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                return null; // No review found for the user in the cookie
            }
        }
    }
        
    function reviewRecipe($rating, $comment, $user_id, $recipe_id) {
        if (!empty($user_id) && $this->userExists($user_id)) {
            $sql = "INSERT INTO reviews (recipe_id, user_id, rating, comment) 
                    VALUES (:recipe_id, :user_id, :rating, :comment)
                    ON DUPLICATE KEY UPDATE rating = :rating, comment = :comment";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':recipe_id', $recipe_id, PDO::PARAM_INT);
            $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt->execute();

        } else {
            $key = "recipe-sharing-app-user-review-id";
            $review_id = filter_input(INPUT_COOKIE, $key, FILTER_VALIDATE_INT);
            $stmt = null;
            if ($review_id) {
                $sql = "UPDATE reviews SET rating = :rating, comment = :comment WHERE id = :review_id";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(':review_id', $review_id, PDO::PARAM_INT);
            } else {
                $sql = "INSERT INTO reviews (recipe_id, user_id, rating, comment) VALUES (:recipe_id, NULL, :rating, :comment)";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(':recipe_id', $recipe_id, PDO::PARAM_INT);
            }
            $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt->execute();
        }

        // Bind parameters and execute SQL statement

        if (empty($user_id) && !$review_id) {
            // If a new review was inserted, get its ID and save it to the cookie
            $review_id = $this->conn->lastInsertId();
            $key = "recipe-sharing-app-user-review-id";
            setcookie($key, $review_id, time() + (86400 * 30), "/");
        }
    }

    function getReviewsByRecipeID($recipe_id) {
        $sql = "SELECT
                    rev.rating,
                    rev.comment,
                    rev.created_at,
                    u.name AS user_name,
                    COALESCE(u.profile_img, './assets/dist/img/no-profile.svg') AS profile_img 
                FROM
                    reviews AS rev
                LEFT JOIN
                    users AS u
                ON
                    rev.user_id = u.id
                WHERE rev.recipe_id = :recipe_id ORDER BY rev.rating DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":recipe_id", $recipe_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getReviewsByRecipeFilter($recipe_id, $filter) {
        $sql = "SELECT
                    rev.rating,
                    rev.comment,
                    rev.created_at,
                    u.name AS user_name,
                    COALESCE(u.profile_img, './assets/dist/img/no-profile.svg') AS profile_img 
                FROM
                    reviews AS rev
                LEFT JOIN
                    users AS u
                ON
                    rev.user_id = u.id
                WHERE rev.recipe_id = :recipe_id AND rev.rating = :filter ORDER BY rev.rating DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":recipe_id", $recipe_id);
        $stmt->bindParam(":filter", $filter);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getCalculateReviewsByRecipe($recipe_id) {
        $sql = "SELECT 
                    r.id AS recipe_id,
                    AVG(rv.rating) AS average_rating,
                    COUNT(rv.id) AS total_count,
                    COUNT(CASE WHEN rv.rating = 1 THEN 1 END) AS rating_1_count,
                    COUNT(CASE WHEN rv.rating = 2 THEN 1 END) AS rating_2_count,
                    COUNT(CASE WHEN rv.rating = 3 THEN 1 END) AS rating_3_count,
                    COUNT(CASE WHEN rv.rating = 4 THEN 1 END) AS rating_4_count,
                    COUNT(CASE WHEN rv.rating = 5 THEN 1 END) AS rating_5_count
                FROM recipes r
                LEFT JOIN reviews rv ON r.id = rv.recipe_id
                WHERE rv.recipe_id = :recipe_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":recipe_id", $recipe_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function removeUnusedCategory() {
        $sql = "DELETE FROM categories 
                WHERE id NOT IN (
                    SELECT DISTINCT category_id 
                    FROM recipe_categories
                )";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
    }
}