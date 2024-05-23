<?php
include('./includes/Dbcon.php');
include('./includes/header.php');

// Create an instance of Dbcon
$db = new Dbcon();

// Check if the recipe ID is provided
if (isset($_GET['id'])) {
    $recipeId = $_GET['id'];

    // Fetch the recipe details from the database
    $recipe = $db->getRecipeById($recipeId);
} else {
    // Handle the case when the recipe ID is not provided
    echo "Invalid recipe ID.";
    exit;
}
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Recipe Details</h1>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <?php if (!empty($recipe)) : ?>
                <div class="card">
                    <div class="card-body">
                        <h2><?php echo $recipe['title']; ?></h2>
                        <p><strong>Description:</strong> <?php echo $recipe['description']; ?></p>
                        <p><strong>Category:</strong> <?php echo $recipe['category']; ?></p>
                        <p><strong>Ingredients:</strong> <?php echo nl2br($recipe['ingredients']); ?></p>
                        <p><strong>Instructions:</strong> <?php echo nl2br($recipe['instructions']); ?></p>
                        <p><strong>Cooking Time:</strong> <?php echo $recipe['cooking_time']; ?></p>
                        <p><strong>Serving Size:</strong> <?php echo $recipe['serving_size']; ?></p>
                    </div>
                </div>
            <?php else : ?>
                <p>Recipe not found.</p>
            <?php endif; ?>
        </div>
    </section>
    <a href="./recipe-list.php" class="btn btn-success">Back</a>
</div>

<?php
include('./includes/footer.php');