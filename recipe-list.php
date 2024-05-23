<?php
include('./includes/Dbcon.php');
include('./includes/header.php');

// Create an instance of Dbcon
$db = new Dbcon();

// Fetch all recipes from the database
$recipes = $db->getRecipes();
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Recipe List</h1>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <?php if (!empty($recipes)) : ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Category</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recipes as $recipe) : ?>
                            <tr>
                                <td><?php echo $recipe['title']; ?></td>
                                <td><?php echo $recipe['description']; ?></td>
                                <td><?php echo $recipe['category_name']; ?></td>
                                <td>
                                    <a href="recipe-details.php?id=<?php echo $recipe['id']; ?>" class="btn btn-primary">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p>No recipes found.</p>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php
include('./includes/footer.php');