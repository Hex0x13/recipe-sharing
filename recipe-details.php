<?php
include('./includes/Dbcon.php');
include('./includes/header.php');

// Create an instance of Dbcon
$db = new Dbcon();

// Check if the recipe ID is provided
if (isset($_GET['id'])) {
    $recipeId = $_GET['id'];
    // Fetch the recipe details from the database
    $recipe = $db->getRecipeByIdWIthImg($recipeId);
} else {
    // Handle the case when the recipe ID is not provided
    echo "Invalid recipe ID.";
    exit;
}
?>
<link rel="stylesheet" href="assets/style.css">
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <article class="recipe-details">
                <?php if (!empty($recipe)) : ?>
                    <header class="recipe-header">
                        <h1 class="recipe-title"><?php echo $recipe['title']; ?></h1>
                        <div class="recipe-image my-3">
                            <img src="<?php echo (!empty($recipe))? $recipe['image_url']: "https://via.placeholder.com/400x300.png?text=Recipe+Image"?>" alt="<?php echo $recipe['title']; ?>"
                            style="width: 80%; height: 200px; object-fit: cover;" class="m-auto d-block">
                        </div>
                        <div class="recipe-meta">
                            <span class="recipe-category"><i class="fas fa-folder"></i> <?php echo $recipe['category_name']; ?></span>
                            <span class="recipe-time"><i class="fas fa-clock"></i> <?php echo $recipe['cooking_time']; ?></span>
                            <span class="recipe-servings"><i class="fas fa-utensils"></i> Serves <?php echo $recipe['serving_size']; ?></span>
                        </div>
                    </header>

                    <div class="recipe-content">
                        <div class="recipe-description">
                            <p><?php echo $recipe['description']; ?></p>
                        </div>

                        <div class="recipe-ingredients">
                            <h3>Ingredients</h3>
                            <p><?php echo nl2br($recipe['ingredients']); ?></p>
                        </div>

                        <div class="recipe-instructions">
                            <h3>Instructions</h3>
                            <p><?php echo nl2br($recipe['instructions']); ?></p>
                        </div>
                    </div>
                <?php else : ?>
                    <p>Recipe not found.</p>
                <?php endif; ?>
            </article>

            <div class="recipe-actions">
                <a href="./recipe-list.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Recipes</a>
            </div>
        </div>
    </div>
</div>

<?php include('./includes/footer.php'); ?>