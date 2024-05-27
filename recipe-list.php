<?php
include('./includes/Dbcon.php');
include('./includes/header.php');

// Create an instance of Dbcon
$db = new Dbcon();

// Fetch all recipes from the database
$recipes = $db->getRecipeList();

// Fetch distinct categories
$categories = array_unique(array_column($recipes, 'category_name'));
?>

<style>
/* General Styles */
body {
    font-family: 'Open Sans', sans-serif;
    background-color: #f8f8f8;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 40px 20px;
}

/* Page Header */
.page-header {
    margin-bottom: 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.page-title {
    font-size: 36px;
    font-weight: 700;
    margin: 0;
}

.filter-dropdown .form-control {
    max-width: 200px;
}

/* Recipe Card */
.recipe-card {
    background-color: #fff;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    border-radius: 4px;
    overflow: hidden;
    transition: transform 0.3s ease;
}

.recipe-card:hover {
    transform: translateY(-5px);
}

.recipe-card-image img {
    width: 100%;
    height: auto;
}

.recipe-card-body {
    padding: 20px;
}

.recipe-card-title {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 10px;
}

.recipe-card-title a {
    color: #333;
    text-decoration: none;
}

.recipe-card-title a:hover {
    color: #007bff;
}

.recipe-card-description {
    font-size: 14px;
    line-height: 1.5;
    margin-bottom: 15px;
}

.recipe-card-meta {
    font-size: 12px;
    color: #888;
    margin-bottom: 15px;
}

.recipe-card-meta span {
    margin-right: 10px;
}

.recipe-card-meta span i {
    margin-right: 5px;
}

/* Buttons */
.btn {
    display: inline-block;
    font-weight: 400;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    user-select: none;
    border: 1px solid transparent;
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    line-height: 1.5;
    border-radius: 0.25rem;
    transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.btn-primary {
    color: #fff;
    background-color: #007bff;
    border-color: #007bff;
}

</style>


<nav class="main-header navbar navbar-expand navbar-white navbar-light p-5" style="margin-left: 0px;">
    <ul class="navbar-nav">
        <li class="nav-item d-none d-sm-inline-block">
            <a href="login.php" class="nav-link">Login</a>
        </li>
    </ul>
    <ul class="navbar-nav ml-auto">
        <!-- Navbar Search -->
        <li class="nav-item">
            <a class="nav-link" data-widget="navbar-search" href="#" role="button">
                <i class="fas fa-search"></i>
            </a>
            <div class="navbar-search-block">
                <form class="form-inline px-5">
                    <div class="input-group input-group-sm">
                        <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
                        <div class="input-group-append">
                            <button class="btn btn-navbar" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                            <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>
    </ul>
</nav>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <header class="page-header">
                <h1 class="page-title">Recipes</h1>
                <div class="filter-dropdown">
                    <select id="categoryFilter" class="form-control">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $category) : ?>
                            <option value="<?php echo $category; ?>"><?php echo $category; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </header>
        </div>
    </div>

    <div class="row recipe-list">
        <?php if (!empty($recipes)) : ?>
            <?php foreach ($recipes as $recipe) : ?>
                <div class="col-md-6 col-lg-4 mb-4 recipe-card-container" data-category="<?php echo $recipe['category_name']; ?>">
                    <article class="recipe-card">
                        <div class="recipe-card-image">
                            <img src="<?php echo (!empty($recipe))? $recipe['image_url']: "https://via.placeholder.com/400x300.png?text=Recipe+Image"?>" alt="<?php echo $recipe['title']; ?>"
                            style="height: 300px; object-fit: cover;">
                        </div>
                        <div class="recipe-card-body">
                            <h2 class="recipe-card-title"><a href="recipe-details.php?id=<?php echo $recipe['recipe_id']; ?>"><?php echo $recipe['title']; ?></a></h2>
                            <p class="recipe-card-description"><?php echo $recipe['description']; ?></p>
                            <div class="recipe-card-meta">
                                <span class="recipe-category"><i class="fas fa-folder"></i> <?php echo $recipe['category_name']; ?></span>
                            </div>
                            <a href="recipe-details.php?id=<?php echo $recipe['recipe_id']; ?>" class="btn btn-secondary">View</a>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="col-md-12">
                <p>No recipes found.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include('./includes/footer.php'); ?>

<script>
    // Filter recipe cards based on category
    const categoryFilter = document.getElementById('categoryFilter');
    const recipeCards = document.querySelectorAll('.recipe-card-container');

    categoryFilter.addEventListener('change', () => {
        const selectedCategory = categoryFilter.value;

        recipeCards.forEach(card => {
            const cardCategory = card.getAttribute('data-category');

            if (selectedCategory === '' || cardCategory === selectedCategory) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
</script>