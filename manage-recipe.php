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
    $categories = array_unique(array_column($recipes, 'category_name'));
?>

<div class="content-wrapper p-5">
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="modal-header">
                    <h5 class="modal-title" style="float: left;">Manage Recipe</h5>
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
                            data-title="<?php echo $recipe['title']?>" 
                            data-description="<?php echo $recipe['description']?>" 
                            data-category="<?php echo $recipe['category_name']?>" 
                            data-ingredients="<?php echo $recipe['ingredients']?>" 
                            data-special-ingredients="<?php echo $recipe['special_instructions']?>" 
                            data-instructions="<?php echo $recipe['instructions']?>" 
                            data-cooking_time="<?php echo $recipe['cooking_time']?>" 
                            data-serving_size="<?php echo $recipe['serving_size']?>">
                            <td class="text-center"><?php echo $key + 1; ?></td>
                            <td><?php echo $recipe['title']; ?></td>
                            <td><?php echo $recipe['description']; ?></td>
                            <td><?php echo $recipe['category_name']; ?></td>
                            <td><?php echo date('Y-m-d', strtotime($recipe['created_at'])); ?></td>
                            <td class="text-center">
                                <button type="button" data-toggle="modal" data-target="#editRecipeModal" class="edit_recipe_btn btn btn-sm btn-transparent p-0"><i class="fas fa-pencil-alt text-primary"></i></button>
                                <a href="./delete_recipe.php?recipe_id=<?php echo $recipe['id'] ?>" onclick="return confirm('Are you sure you want to delete <?php echo $recipe['title'] ?>')"  class="btn btn-sm btn-transparent p-0"><i class="fas fa-trash text-danger"></i></a>

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
                            <label for="title1">Title</label>
                            <input type="text" class="form-control" id="title1" name="title" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="description1">Description</label>
                            <textarea class="form-control" id="description1" name="description"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="ingredients1">Ingredients</label>
                            <textarea class="form-control" id="ingredients1" name="ingredients" required></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="instructions1">Instructions</label>
                            <textarea class="form-control" id="instructions1" name="instructions" required></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="specialInstructions1">Special Instructions</label>
                            <textarea class="form-control" id="specialInstructions1" name="specialInstructions"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="cooking_time1">Cooking Time</label>
                            <input type="text" class="form-control" id="cooking_time1" name="cooking_time" placeholder="minutes">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="serving_size1">Serving Size</label>
                            <input type="text" class="form-control" id="serving_size1" name="serving_size">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="category">Category</label>
                            <input list="categories" type="text" class="form-control" id="category" name="category">
                            <datalist id="categories">
                                <?php foreach ($categories as $category):?>
                                    <option value="<?php echo $category ?>"><?php echo $category; ?></option>
                                <?php endforeach ?>
                            </datalist>
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
                <form method="post" action="update_recipe.php" enctype="multipart/form-data">
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
                        <div class="form-group col-md-12">
                            <label for="specialInstruction">Special Instructions</label>
                            <textarea class="form-control" id="specialInstructions" name="specialInstructions"></textarea>
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
                            <input list="categories2" type="text" class="form-control" id="category" name="category">
                            <datalist id="categories2">
                                <?php foreach ($categories as $category):?>
                                    <option value="<?php echo $category ?>"><?php echo $category; ?></option>
                                <?php endforeach ?>
                            </datalist>
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
        document.querySelector('#editRecipeModal #recipeId').value = recipeRow.getAttribute('data-recipe-id');
        document.querySelector('#editRecipeModal #title').value = recipeRow.getAttribute('data-title');
        document.querySelector('#editRecipeModal #description').value = recipeRow.getAttribute('data-description');
        document.querySelector('#editRecipeModal #category').value = recipeRow.getAttribute('data-category');
        document.querySelector('#editRecipeModal #ingredients').value = recipeRow.getAttribute('data-ingredients');
        document.querySelector('#editRecipeModal #instructions').value = recipeRow.getAttribute('data-instructions');
        document.querySelector('#editRecipeModal #cooking_time').value = recipeRow.getAttribute('data-cooking_time');
        document.querySelector('#editRecipeModal #serving_size').value = recipeRow.getAttribute('data-serving_size');
        document.querySelector('#editRecipeModal #specialInstructions').value = recipeRow.getAttribute('data-special-ingredients');
    }
});
</script>

<?php include './includes/footer.php'; ?>
