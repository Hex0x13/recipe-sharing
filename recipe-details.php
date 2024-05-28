<?php
session_start();
include('./includes/Dbcon.php');
include('./includes/header.php');

// Create an instance of Dbcon
$db = new Dbcon();

// Check if the recipe ID is provided
if (isset($_GET['id'])) {
    $recipeId = $_GET['id'];
    $recipe = $db->getRecipeByIdWithImg($recipeId);
    $_review = $db->getReviewsByUser($recipeId, $_SESSION['user_id'] ?? null);

    $reviws = null;
    if (isset($_GET['filter'])) {
        $filter = $_GET['filter'];
        $reviews = $db->getReviewsByRecipeFilter($recipeId, $filter);
    } else {
        $reviews = $db->getReviewsByRecipeID($recipeId);
    }
    $reviewCalculation = $db->getCalculateReviewsByRecipe($recipeId);
    if (!empty($reviewCalculation)) {
        $averageRating = $reviewCalculation['average_rating'];
        $totalReviews = $reviewCalculation['total_count'];
        $rating1Count = $reviewCalculation['rating_1_count'];
        $rating2Count = $reviewCalculation['rating_2_count'];
        $rating3Count = $reviewCalculation['rating_3_count'];
        $rating4Count = $reviewCalculation['rating_4_count'];
        $rating5Count = $reviewCalculation['rating_5_count'];
    }
} else {
    echo "Invalid recipe ID.";
    exit;
}
?>

<link rel="stylesheet" href="assets/style.css">
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const ratingStars = document.querySelectorAll('.rating-star');
        const ratingInput = document.getElementById('rating');
        ratingStars.forEach((star, index) => {
            star.addEventListener('click', () => {
                const rating = index + 1;
                ratingInput.value = rating;
                ratingStars.forEach((s, i) => {
                    s.classList.toggle('text-warning', i < rating);
                    s.classList.toggle('text-muted', i >= rating);
                });
            });
        });
        const filter = +document.getElementById('dataFilterInfo').getAttribute('data-filter');
        if (filter >= 1 && filter <= 5) {
            document.querySelector(`.filter-tags>*:nth-child(${7 - filter})`).classList.add('active');
        } else {
            document.querySelector(`.filter-tags>*:nth-child(1)`).classList.add('active');
        }
    });
</script>

<div class="container" id="dataFilterInfo" data-filter="<?php echo $filter ?? '' ?>">
    <div class="row">
        <div class="col-md-12 offset-md-0">
            <article class="recipe-details">
                <?php if (!empty($recipe)) : ?>
                    <header class="recipe-header">
                        <h1 class="recipe-title"><?php echo $recipe['title']; ?></h1>
                        <div class="recipe-image my-3 bg-secondary">
                            <img src="<?php echo $recipe['image_url']?>" alt="<?php echo $recipe['title']; ?>" style="width: 80%; height: 400px; object-fit: contain;" class="m-auto d-block">
                        </div>
                        <div class="recipe-meta">
                            <span class="recipe-category"><i class="fas fa-folder"></i> <?php echo $recipe['category_name']; ?></span>
                            <span class="recipe-time"><i class="fas fa-clock"></i> <?php echo $recipe['cooking_time']; ?> mins</span>
                            <span class="recipe-servings"><i class="fas fa-utensils"></i> Serves <?php echo $recipe['serving_size']; ?></span>
                        </div>
                    </header>
                    <div class="recipe-content">
                        <div class="recipe-description">
                            <strong>Description:</strong>
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
                        <div class="recipe-instructions">
                            <h3>Special instructions</h3>
                            <p><?php echo nl2br($recipe['special_instructions']); ?></p>
                        </div>
                    </div>
                <?php else : ?>
                    <p>Recipe not found.</p>
                <?php endif; ?>
            </article>

            <div class="recipe-actions pb-3 border-bottom ">
                <a href="./recipe-list.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Recipes</a>
            </div>

            <div class="add-review mt-5">
                <h2>Add a Review</h2>
                <form id="review-form" method="post" action="./includes/review.php">
                    <input type="hidden" name="recipe_id" value="<?php echo $recipeId; ?>">
                    <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id'] ?? null ?>">
                    <div class="form-group">
                        <label for="rating">Rating:</label>
                        <div class="rating">
                            <?php $len = !empty($_review)? $_review['rating'] : 0 ;?>
                            <?php for ($i = 1; $i <= 5; $i++) : ?>
                                <?php if ($i < $len):?>
                                    <i class="fas fa-star text-warning rating-star" data-rating="<?php echo $i; ?>"></i>
                                <?php else:?>
                                    <i class="fas fa-star text-muted rating-star" data-rating="<?php echo $i; ?>"></i>
                                <?php endif?>
                            <?php endfor; ?>
                            <input type="hidden" id="rating" name="rating" value="0">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="comment">Comment:</label>
                        <textarea class="form-control" id="comment" name="comment" rows="3" required><?php if (!empty($_review)){ echo $_review['comment']; }?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Review</button>
                </form>
            </div>

            <div class="reviews mt-5">
                <h2>Rating and reviews</h2>
                <div class="rating-container">
                    <div class="rating-summary">
                        <div class="rating-value text-left"><?php echo number_format($averageRating, 1); ?></div>
                        <div class="rating-stars">
                            <?php for ($i = 1; $i <= 5; $i++) : ?>
                                <i class="fas fa-star <?php echo ($i <= floor($averageRating)) ? 'text-warning' : 'text-muted'; ?>"></i>
                            <?php endfor; ?>
                        </div>
                        <div class="total-reviews text-left"><?php echo $totalReviews; ?> reviews</div>
                    </div>
                    <div class="rating-distribution">
                        <div class="progress-group">
                            <span class="review-number text-gray">5</span>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-primary" style="width: <?php echo ($rating5Count / $totalReviews) * 100; ?>%;"></div>
                            </div>
                        </div>
                        <div class="progress-group">
                            <span class="review-number text-gray">4</span>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-primary" style="width: <?php echo ($rating4Count / $totalReviews) * 100; ?>%;"></div>
                            </div>
                        </div>
                        <div class="progress-group">
                            <span class="review-number text-gray">3</span>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-primary" style="width: <?php echo ($rating3Count / $totalReviews) * 100; ?>%;"></div>
                            </div>
                        </div>
                        <div class="progress-group">
                            <span class="review-number text-gray">2</span>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-primary" style="width: <?php echo ($rating2Count / $totalReviews) * 100; ?>%;"></div>
                            </div>
                        </div>
                        <div class="progress-group">
                            <span class="review-number text-gray">1</span>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-primary" style="width: <?php echo ($rating1Count / $totalReviews) * 100; ?>%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="filter-options mb-3">
                    <div class="filter-tags">
                        <a class="btn btn-white text-gray border border-gray rounded" href="recipe-details.php?id=<?php echo $recipeId?>">All</a>
                        <a class="btn btn-white text-gray border border-gray rounded" href="recipe-details.php?id=<?php echo $recipeId?>&filter=5">5 <i class="fas fa-star text-sm text-warning"></i></a>
                        <a class="btn btn-white text-gray border border-gray rounded" href="recipe-details.php?id=<?php echo $recipeId?>&filter=4">4 <i class="fas fa-star text-sm text-warning"></i></a>
                        <a class="btn btn-white text-gray border border-gray rounded" href="recipe-details.php?id=<?php echo $recipeId?>&filter=3">3 <i class="fas fa-star text-sm text-warning"></i></a>
                        <a class="btn btn-white text-gray border border-gray rounded" href="recipe-details.php?id=<?php echo $recipeId?>&filter=2">2 <i class="fas fa-star text-sm text-warning"></i></a>
                        <a class="btn btn-white text-gray border border-gray rounded" href="recipe-details.php?id=<?php echo $recipeId?>&filter=1">1 <i class="fas fa-star text-sm text-warning"></i></a>
                    </div>
                </div>
                <?php if (!empty($reviews)) : ?>
                    <?php foreach ($reviews as $review) : ?>
                        <div class="review card">
                            <div class="review-author w-100 mb-2">
                                <img class="profile-img rounded-circle border" src="<?php echo $review['profile_img'] ?>" alt="profile">
                                <?php if (!empty($review['user_name'])):?>
                                    <span class="review-author-name ml-2 text-gray"><?php echo $review['user_name']; ?></span>
                                <?php else:?>
                                    <span class="review-author-name ml-2 text-gray" style="opacity: 0.3;">Anonymous</span>
                                <?php endif?>
                            </div>
                            <div class="review-header d-flex justify-content-start align-items-center">
                                <div class="review-rating">
                                    <?php for ($i = 1; $i <= 5; $i++) : ?>
                                        <i class="fas fa-star text-sm <?php echo ($i <= $review['rating']) ? 'text-warning' : 'text-muted'; ?>"></i>
                                    <?php endfor; ?>
                                </div>
                                <span class="review-date ml-2"><?php echo date('m/j/Y', strtotime($review['created_at'])); ?></span>
                            </div>
                            <div class="review-body mt-2">
                                <p><?php echo $review['comment']; ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p>No reviews yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include('./includes/footer.php'); ?>