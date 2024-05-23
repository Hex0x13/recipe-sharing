<?php
include('./includes/check-login.php');
include('./includes/header.php');
include('./includes/topbar.php');
include('./includes/sidebar.php');
include('./includes/recipe-modal.php');
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Add Recipe</h1>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <form method="post" action="add_recipe_process.php">
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="ingredients">Ingredients</label>
                            <textarea class="form-control" id="ingredients" name="ingredients" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="instructions">Instructions</label>
                            <textarea class="form-control" id="instructions" name="instructions" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="cooking_time">Cooking Time</label>
                            <input type="text" class="form-control" id="cooking_time" name="cooking_time">
                        </div>
                        <div class="form-group">
                            <label for="serving_size">Serving Size</label>
                            <input type="text" class="form-control" id="serving_size" name="serving_size">
                        </div>
                        <div class="form-group">
                            <label for="category">Category</label>
                            <input type="text" class="form-control" id="category" name="category">
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
                    <div class="card-body table-responsive p-3">
                        <table class="table align-items-center table-flush table-hover" id="dataTableHover">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Category</th>
                                    <th>Created At</th>
                                    <th class="text-center" style="width: 15%;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                include('./includes/dbcon.php');
                                $db = new Dbcon();
                                // Fetch all recipes from the database
                                $recipes = $db->getRecipes();

                                // Loop through each recipe and display it in the table
                                foreach ($recipes as $recipe) {
                                    echo '<tr>';
                                    echo '<td class="text-center">' . $recipe['id'] . '</td>';
                                    echo '<td>' . $recipe['title'] . '</td>';
                                    echo '<td>' . $recipe['description'] . '</td>';
                                    echo '<td>' . $recipe['category_name'] . '</td>';
                                    echo '<td>' . $recipe['created_at'] . '</td>';
                                    echo '<td class="text-center">';
                                    echo '<button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#editRecipe' . $recipe['id'] . '"><i class="fas fa-edit"></i></button>';
                                    echo '<button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteRecipe' . $recipe['id'] . '"><i class="fas fa-trash"></i></button>';
                                    echo '</td>';
                                    echo '</tr>';

                                    // Add modals for editing and deleting recipes
                                    echo getEditRecipeModal($recipe);
                                    echo getDeleteRecipeModal($recipe);
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
            </div>
        </div>
    </section>
</div>

<?php
include('./includes/footer.php');
?>