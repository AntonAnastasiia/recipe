<?php
/*
Plugin Name: Recipe
Description: Плагін для створення нового типу записів "Рецепти" з додатковими полями.
Version: 1.0
Author: Anton Anastasiia
*/

if (!defined('ABSPATH')) {
    exit; 
}
// Основний клас плагіну
class My_Recipe_Plugin {
    
    // Конструктор класу
    public function __construct() {
        add_action('init', [$this, 'register_recipe_post_type']);
        add_action('add_meta_boxes', [$this, 'add_recipe_metaboxes']);
        add_action('save_post', [$this, 'save_recipe_metadata']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
    }

    public function enqueue_scripts() {
        wp_enqueue_style('style', plugin_dir_url(__FILE__) . 'css/style.css');
    }

    // Реєстрація нового типу запису "Рецепти"
    public function register_recipe_post_type() {
        $labels = [
            'name' => 'Рецепти',
            'singular_name' => 'Рецепт',
            'add_new' => 'Додати новий',
            'add_new_item' => 'Додати новий рецепт',
            'edit_item' => 'Редагувати рецепт',
            'new_item' => 'Новий рецепт',
            'view_item' => 'Переглянути рецепт',
            'search_items' => 'Шукати рецепти',
            'not_found' => 'Рецептів не знайдено',
            'not_found_in_trash' => 'У кошику рецептів не знайдено',
            'menu_name' => 'Рецепти'
        ];

        $args = [
            'labels' => $labels,
            'public' => true,
            'has_archive' => true,
            'rewrite' => ['slug' => 'recipes'],
            'supports' => ['title', 'editor', 'thumbnail'],
            'menu_position' => 5,
            'menu_icon' => 'dashicons-carrot',
        ];

        register_post_type('recipe', $args);
    }

    // Додавання метабоксів для додаткових полів
    public function add_recipe_metaboxes() {
        add_meta_box(
            'recipe_details',
            'Деталі рецепту',
            [$this, 'render_recipe_metabox'],
            'recipe',
            'normal',
            'default'
        );
    }

    // Виведення метабоксу з додатковими полями
    public function render_recipe_metabox($post) {
        // Отримання збережених значень метаданих
        $ingredients = get_post_meta($post->ID, 'recipe_ingredients', true);
        $cooking_time = get_post_meta($post->ID, 'recipe_cooking_time', true);
        $servings = get_post_meta($post->ID, 'recipe_servings', true);

        ?>
        <label for="recipe_ingredients">Інгредієнти:</label>
        <textarea id="recipe_ingredients" name="recipe_ingredients" rows="5" style="width:100%;"><?php echo esc_textarea($ingredients); ?></textarea>

        <label for="recipe_cooking_time">Час готовки (хвилин):</label>
        <input type="number" id="recipe_cooking_time" name="recipe_cooking_time" value="<?php echo esc_attr($cooking_time); ?>" style="width:100%;">

        <label for="recipe_servings">Порції:</label>
        <input type="number" id="recipe_servings" name="recipe_servings" value="<?php echo esc_attr($servings); ?>" style="width:100%;">
        <?php
    }

    // Збереження метаданих при збереженні посту
    public function save_recipe_metadata($post_id) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Збереження даних
        if (isset($_POST['recipe_ingredients'])) {
            update_post_meta($post_id, 'recipe_ingredients', sanitize_textarea_field($_POST['recipe_ingredients']));
        }

        if (isset($_POST['recipe_cooking_time'])) {
            update_post_meta($post_id, 'recipe_cooking_time', intval($_POST['recipe_cooking_time']));
        }

        if (isset($_POST['recipe_servings'])) {
            update_post_meta($post_id, 'recipe_servings', intval($_POST['recipe_servings']));
        }
    }
}

// Ініціалізація плагіну
new My_Recipe_Plugin();

// Функція для підключення шаблону з плагіна
function my_recipe_plugin_template($template) {
    if (is_singular('recipe')) {
        // Шлях до шаблону в папці плагіна
        $plugin_template = plugin_dir_path(__FILE__) . 'templates/single-recipe.php';

        // Перевірка наявності шаблону
        if (file_exists($plugin_template)) {
            return $plugin_template;
        }
    }
    return $template;
}

// Додавання фільтра для підключення шаблону
add_filter('template_include', 'my_recipe_plugin_template');
