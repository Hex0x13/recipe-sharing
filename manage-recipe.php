<?php
    include './includes/check-login.php';
    include './includes/header.php';
    include './includes/topbar.php';
    include './includes/sidebar.php';
    include './includes/recipe-modal.php';

    // Include database connection
    include './includes/dbcon.php';

    // Fetch all recipes from the database
    $db = new Dbcon();
    $recipes = $db->getRecipes();
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Manage</h1>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="modal-header">
                    <h5 class="modal-title" style="float: left;">Register user</h5>
                    <div class="card-tools" style="float: right;">
                        <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#addRecipeModal"><i
                            class="fas fa-plus"></i> Add Recipe
                        </button>
                    </div>
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
                        <?php foreach ($recipes as $key => $recipe): ?>
                            <tr id="recipe-<?php echo $recipe['id']; ?>" 
                            data-recipe-id="<?php echo $recipe['id']; ?>" 
                            data-title="<?php echo htmlspecialchars($recipe['title'], ENT_QUOTES, 'UTF-8'); ?>" 
                            data-description="<?php echo htmlspecialchars($recipe['description'], ENT_QUOTES, 'UTF-8'); ?>" 
                            data-category="<?php echo htmlspecialchars($recipe['category_name'], ENT_QUOTES, 'UTF-8'); ?>" 
                            data-ingredients="<?php echo htmlspecialchars($recipe['ingredients'], ENT_QUOTES, 'UTF-8'); ?>" 
                            data-instructions="<?php echo htmlspecialchars($recipe['instructions'], ENT_QUOTES, 'UTF-8'); ?>" 
                            data-cooking_time="<?php echo htmlspecialchars($recipe['cooking_time'], ENT_QUOTES, 'UTF-8'); ?>" 
                            data-serving_size="<?php echo htmlspecialchars($recipe['serving_size'], ENT_QUOTES, 'UTF-8'); ?>">
                            <td class="text-center"><?php echo $key + 1; ?></td>
                            <td><?php echo $recipe['title']; ?></td>
                            <td><?php echo $recipe['description']; ?></td>
                            <td><?php echo $recipe['category_name']; ?></td>
                            <td><?php echo date('Y-m-d', strtotime($recipe['created_at'])); ?></td>
                            <td class="text-center">
                                <button type="button" data-toggle="modal" data-target="#editRecipeModal" class="edit_recipe_btn btn btn-sm btn-primary">Edit</button>
                                <a href="./delete_recipe.php?recipe_id=<?php echo $recipe['id'] ?>" onclick="return confirm('Are you sure you want to delete <?php echo $recipe['title'] ?>')"  class="btn btn-sm btn-danger">Delete</a>

                            </td>

                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>


<div class="modal fade" id="addRecipeModal" tabindex="-1" aria-labelledby="addRecipeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Recipe</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="add_recipe_process.php" enctype="multipart/form-data">
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="title">Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="ingredients">Ingredients</label>
                            <textarea class="form-control" id="ingredients" name="ingredients" required></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="instructions">Instructions</label>
                            <textarea class="form-control" id="instructions" name="instructions" required></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="cooking_time">Cooking Time</label>
                            <input type="text" class="form-control" id="cooking_time" name="cooking_time">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="serving_size">Serving Size</label>
                            <input type="text" class="form-control" id="serving_size" name="serving_size">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="category">Category</label>
                            <input type="text" class="form-control" id="category" name="category">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="images">Images</label>
                            <input type="file" class="form-control" id="images" name="images[]" multiple>
                        </div>
                    </div>
                    <div class="form-group">
                      <input type="submit" value="Add" class="btn btn-info">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="editRecipeModal" tabindex="-1" aria-labelledby="addRecipeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Recipe</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="add_recipe_process.php" enctype="multipart/form-data">
                    <input type="hidden" name="id" id="recipeId">
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="title">Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="ingredients">Ingredients</label>
                            <textarea class="form-control" id="ingredients" name="ingredients" required></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="instructions">Instructions</label>
                            <textarea class="form-control" id="instructions" name="instructions" required></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="cooking_time">Cooking Time</label>
                            <input type="text" class="form-control" id="cooking_time" name="cooking_time">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="serving_size">Serving Size</label>
                            <input type="text" class="form-control" id="serving_size" name="serving_size">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="category">Category</label>
                            <input type="text" class="form-control" id="category" name="category">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="images">Images</label>
                            <input type="file" class="form-control" id="images" name="images[]" multiple>
                        </div>
                    </div>
                    <div class="form-group">
                      <input type="submit" value="Update" class="btn btn-info">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
document.addEventListener('click', event => {
    const editButton = event.target.closest('.edit_recipe_btn');
    if (editButton) {
        const recipeRow = editButton.closest('tr');
        console.log(recipeRow);
        document.querySelector('#editRecipeModal #recipeId').value = recipeRow.getAttribute('data-recipe-id');
        document.querySelector('#editRecipeModal #title').value = recipeRow.getAttribute('data-title');
        document.querySelector('#editRecipeModal #description').value = recipeRow.getAttribute('data-description');
        document.querySelector('#editRecipeModal #category').value = recipeRow.getAttribute('data-category');
        document.querySelector('#editRecipeModal #ingredients').value = recipeRow.getAttribute('data-ingredients');
        document.querySelector('#editRecipeModal #instructions').value = recipeRow.getAttribute('data-instructions');
        document.querySelector('#editRecipeModal #cooking_time').value = recipeRow.getAttribute('data-cooking_time');
        document.querySelector('#editRecipeModal #serving_size').value = recipeRow.getAttribute('data-serving_size');
    }
});
</script>

<?php include './includes/footer.php'; ?>
