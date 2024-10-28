<?php get_header(); ?>

<div class="recipe-container">

    <!-- Заголовок рецепта -->
    <div class="recipe-header">
        <h1><?php the_title(); ?></h1>
    </div>

    <!-- Секція зображення -->
    <?php if (has_post_thumbnail()) : ?>
        <div class="recipe-thumbnail">
            <?php the_post_thumbnail('large'); ?>
        </div>
    <?php endif; ?>

    <!-- Метадані рецепта -->
    <div class="recipe-meta">
        <?php
            $ingredients = get_post_meta(get_the_ID(), 'recipe_ingredients', true);
            $cooking_time = get_post_meta(get_the_ID(), 'recipe_cooking_time', true);
            $servings = get_post_meta(get_the_ID(), 'recipe_servings', true);
        ?>

        <?php if ($cooking_time): ?>
            <p><strong>Час готовки:</strong> <?php echo esc_html($cooking_time); ?> хвилин</p>
        <?php endif; ?>

        <?php if ($servings): ?>
            <p><strong>Порції:</strong> <?php echo esc_html($servings); ?></p>
        <?php endif; ?>
    </div>

    <!-- Секція інгредієнтів -->
    <?php if ($ingredients): ?>
        <div class="recipe-ingredients">
            <h2>Інгредієнти:</h2>
            <p><?php echo nl2br(esc_html($ingredients)); ?></p>
        </div>
    <?php endif; ?>

    <!-- Основний контент -->
    <div class="recipe-content">
        <?php the_content(); ?>
    </div>

</div>

<?php get_footer(); ?>
