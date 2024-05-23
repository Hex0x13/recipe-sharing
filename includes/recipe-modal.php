<?php

function getEditRecipeModal($recipe)
{
    $modal = '<div class="modal fade" id="editRecipe' . $recipe['id'] . '" tabindex="-1" role="dialog" aria-labelledby="editRecipeLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editRecipeLabel">Edit Recipe</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="update_recipe.php" method="post">
                        <input type="hidden" name="recipe_id" value="' . $recipe['id'] . '">
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" class="form-control" id="title" name="title" value="' . $recipe['title'] . '" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description">' . $recipe['description'] . '</textarea>
                        </div>
                        <div class="form-group">
                            <label for="ingredients">Ingredients</label>
                            <textarea class="form-control" id="ingredients" name="ingredients" required>' . $recipe['ingredients'] . '</textarea>
                        </div>
                        <div class="form-group">
                            <label for="instructions">Instructions</label>
                            <textarea class="form-control" id="instructions" name="instructions" required>' . $recipe['instructions'] . '</textarea>
                        </div>
                        <div class="form-group">
                            <label for="cooking_time">Cooking Time</label>
                            <input type="text" class="form-control" id="cooking_time" name="cooking_time" value="' . $recipe['cooking_time'] . '">
                        </div>
                        <div class="form-group">
                            <label for="serving_size">Serving Size</label>
                            <input type="text" class="form-control" id="serving_size" name="serving_size" value="' . $recipe['serving_size'] . '">
                        </div>
                        <div class="form-group">
                            <label for="category">Category</label>
                            <input type="text" class="form-control" id="category" name="category" value="' . $recipe['category_name'] . '">
                        </div>
                        <button type="submit" class="btn btn-primary">Update Recipe</button>
                    </form>
                </div>
            </div>
        </div>
    </div>';

    return $modal;
}

function getDeleteRecipeModal($recipe)
{
    $modal = '<div class="modal fade" id="deleteRecipe' . $recipe['id'] . '" tabindex="-1" role="dialog" aria-labelledby="deleteRecipeLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteRecipeLabel">Delete Recipe</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this recipe?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <form action="delete_recipe.php" method="post">
                        <input type="hidden" name="recipe_id" value="' . $recipe['id'] . '">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>';

    return $modal;
}