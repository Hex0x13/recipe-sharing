<?php
include('./includes/Dbcon.php');
include('./includes/header.php');

// Create an instance of Dbcon
$db = new Dbcon();

// Fetch all recipes from the database
$recipes = $db->getRecipeList();

// Fetch distinct categories
$categories = array_unique(array_column($recipes, 'category_name'));
$filterCategory = null;
if (isset($_GET['category'])) {
    $filterCategory = $_GET['category'];
}
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
    height: 300px;
    object-fit: contain;
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
    height: 50px;
    text-overflow: ellipsis;
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
            <div class="navbar-search-block bg-white">
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
                    <div class="input-group input-group-sm d-flex justify-content-end mt-2">
                        <div class="input-group-sm d-flex">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="inputGroupSelect01">Type</label>
                            </div>
                            <select class="custom-select" id="inputGroupSelect01">
                                <option selected>Choose...</option>
                                <option value="1">One</option>
                                <option value="2">Two</option>
                                <option value="3">Three</option>
                            </select>
                        </div>
                        <div class="input-group-sm d-flex mx-2">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="inputGroupSelect01">Sort Order</label>
                            </div>
                            <select class="custom-select" id="inputGroupSelect01">
                                <option selected>Choose...</option>
                                <option value="1">One</option>
                                <option value="2">Two</option>
                                <option value="3">Three</option>
                            </select>
                        </div>
                        <div class="input-group-sm d-flex">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="inputGroupSelect01">Order by</label>
                            </div>
                            <select class="custom-select" id="inputGroupSelect01">
                                <option selected>Choose...</option>
                                <option value="1">One</option>
                                <option value="2">Two</option>
                                <option value="3">Three</option>
                            </select>
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
                        <option value="" <?php if (empty($filterCategory)) echo'selected' ?>>All Categories</option>
                        <?php foreach ($categories as $category) : ?>
                            <option value="<?php echo $category; ?>" <?php if (!empty($filterCategory) && $filterCategory === $category) echo'selected' ?>>
                                <?php echo $category ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </header>
        </div>
    </div>

    <div class="row recipe-list">
        <?php if (!empty($recipes)) : ?>
            <?php foreach ($recipes as $recipe) :
                if (!empty($filterCategory) && $recipe['category_name'] === $filterCategory) {
                    continue;
                }?>
                <div class="col-md-6 col-lg-4 mb-4 recipe-card-container"  data-category="<?php echo $recipe['category_name']; ?>">
                    <article class="recipe-card" >
                        <div class="recipe-card-image bg-secondary">
                            <img src="<?php echo $recipe['image_url']?>" alt="<?php echo $recipe['title']; ?>">
                        </div>
                        <div class="recipe-card-body">
                            <h2 class="recipe-card-title"><a href="recipe-details.php?id=<?php echo $recipe['recipe_id']; ?>"><?php echo $recipe['title']; ?></a></h2>
                            <p class="recipe-card-description"><?php echo $recipe['description']; ?></p>
                            <div class="recipe-card-meta">
                                <span class="recipe-category"><i class="fas fa-folder"></i> <?php echo $recipe['category_name']; ?></span>
                                <span class="recipe-time"><i class="fas fa-clock"></i> <?php echo $recipe['cooking_time']; ?> mins</span>
                                <span class="recipe-servings"><i class="fas fa-utensils"></i> Serves <?php echo $recipe['serving_size']; ?></span>
                            </div>
                            <div class="recipe-card-footer d-flex justify-content-between align-items-center">                             
                                <a href="recipe-details.php?id=<?php echo $recipe['recipe_id']; ?>" class="btn btn-secondary">View</a>
                                <div class="rating mt-auto">
                                    <?php $avgRating = number_format($db->getCalculateReviewsByRecipe($recipe['recipe_id'])['average_rating'], 1); ?>
                                    <span class="text-sm text-gray mr-2"><?php echo $avgRating; ?></span>
                                    <?php for ($i = 1; $i <= 5; $i++) : ?>
                                        <?php if ($i < $avgRating):?>
                                            <i class="fas fa-star text-warning rating-star" data-rating="<?php echo $i; ?>"></i>
                                        <?php else:?>
                                            <i class="fas fa-star text-muted rating-star" data-rating="<?php echo $i; ?>"></i>
                                        <?php endif?>
                                    <?php endfor; ?>
                                </div>
                            </div>
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

    const selectCategory = document.getElementById("categoryFilter");
    function redirectToCategory() {
        const selectedCategory = selectCategory.value;
        if (selectedCategory) {
            window.location.href = "recipe-list.php?category=" + encodeURIComponent(selectedCategory);
        } else {
            window.location.href = "recipe-list.php";
        }
    }
    selectCategory.addEventListener('change', redirectToCategory);
</script>