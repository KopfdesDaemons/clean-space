<?php
function enqueue_custom_styles()
{
    $theme_directory = get_stylesheet_directory_uri();

    $styles = array(
        'animations' => "/animations.css",
        'custom-font' => '/fonts/fonts.css',
        'custom-styles' => '/style.css',
        'header-styles' => '/header.css',
        'footer-styles' => '/footer.css',
        'sidebar-styles' => '/sidebar.css',
        'comments-styles' => '/comments.css',
        'archive-styles' => '/archive.css',
        'single-styles' => '/single.css',
        'page-style' => '/page.css',
        'search-style' => '/search.css',
        'wp-block-style' => '/wp-block.css',
        'paginantion-style' => '/pagination.css',
        '404-styles' => '/404.css',
        'fontawesome' => '/fonts/fontawesome/css/all.min.css',

        //  template-parts
        'card-posts-list' => '/template-parts/post-list-layouts/cards-posts-list.css',
        'content-creator-posts-list' => '/template-parts/post-list-layouts/content-creator-posts-list.css',
        'frameless-post-list' => '/template-parts/post-list-layouts/frameless-posts-list.css',
        'material2-post-list' => '/template-parts/post-list-layouts/material2-posts-list.css',
        'material3-post-list' => '/template-parts/post-list-layouts/material3-posts-list.css',
        'search-engine-post-list' => '/template-parts/post-list-layouts/search-engine-posts-list.css',
        'social-post-list' => '/template-parts/post-list-layouts/social-posts-list.css',
    );

    foreach ($styles as $handle => $file) {
        wp_enqueue_style($handle, $theme_directory . $file, array(), '1', 'all');
    }
}
add_action('wp_enqueue_scripts', 'enqueue_custom_styles');


// Theme Support
add_theme_support('post-thumbnails');
add_theme_support("title-tag");
add_theme_support('automatic-feed-links');
add_theme_support('html5', array(
    'comment-list',
    'comment-form',
    'search-form',
    'gallery',
    'caption',
));
add_theme_support('align-wide');
add_theme_support('responsive-embeds');

function lime_blog_enqueue_scripts()
{
    // Load the wordpress comment script from the “\wordpress\wp-includes\js” directory.
    // This allows the comment response form to be located below the corresponding comment
    // and not at the very bottom of the page.
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
    // Header Script
    wp_enqueue_script('lime_blog_header_script', get_template_directory_uri() . '/js/lime_blog_header_script.js', null, '1.0', true);
    
    // Author Script
    if(get_theme_mod('author_page_latest_comments', true)) {
        wp_enqueue_script('lime_blog_author_script', get_template_directory_uri() . '/js/lime_blog_author_page.js', null, '1.0', true);
    }
}
add_action('wp_enqueue_scripts', 'lime_blog_enqueue_scripts');

function lime_blog_after_setup_theme()
{
    // For the translation
    load_theme_textdomain('lime-blog', get_template_directory() . '/languages');
    // defaults to the feed as the homepage
    update_option('show_on_front', 'posts');
}
add_action('after_setup_theme', 'lime_blog_after_setup_theme');

function lime_blog_register_menus()
{
    register_nav_menus(
        array(
            'header-menu' => __('Header Menu', 'lime-blog'),
            'footer-menu' => __('Footer Menu', 'lime-blog')
        )
    );
}
add_action('init', 'lime_blog_register_menus');

function lime_blog_register_sidebar()
{
    register_sidebar(array(
        'name' => __('Sidebar', 'lime-blog'),
        'id' => 'my-sidebar',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h2 class="widget-title">',
        'after_title' => '</h2>',
    ));
}
add_action('widgets_init', 'lime_blog_register_sidebar');

function lime_blog_register_landigpage_widget_area()
{
    register_sidebar(array(
        'name' => __('Landingpage Widget Area', 'lime-blog'),
        'id' => 'landingpage-widget-area',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h2 class="widget-title">',
        'after_title' => '</h2>',
    ));
}
add_action('widgets_init', 'lime_blog_register_landigpage_widget_area');

// Custom menu structure
class lime_blog_menu_walker extends Walker_Nav_Menu
{
    function start_el(&$output, $item, $depth = 0, $args = [], $id = 0)
    {
        if (empty($item->title)) return;

        $output .= "<li class='" .  implode(" ", (array)$item->classes) . "'>";
        $output .= "<div class='lime_blog_menuitem_container'>";
        $output .= '<a href="' . esc_url($item->url) . '">';
        $output .= $item->title;
        $output .= '</a>';

        if ($args->walker->has_children) {
            $output .= '<i tabindex="0" class="lime_blog_submenu_toggle fa fa-caret-down"></i>';
        }
        $output .= "</div>";
    }
}

// customizer settings
$customizer_options = [
    'colors-options.php',
    'header-options.php',
    'posts-options.php',
    'feed-options.php',
    'fonts-options.php',
    'author-page-options.php',
    'pages-options.php',
    'searchresults-options.php',
    'layout-options.php',
    'post-list-layouts/cards-options.php',
    'post-list-layouts/frameless-post-list.php',
    'post-list-layouts/material2-post-list.php',
    'post-list-layouts/material3-post-list.php',
    'tag-list-options.php',
    'category-list-options.php',
    'date-list-options.php'
];

foreach ($customizer_options as $option) {
    require_once get_template_directory() . '/customizer-options/' . $option;
}

// Sanitize function to check checkbox value (true/false)
function lime_blog_sanitize_checkbox($input)
{
    return (isset($input) && true === $input) ? true : false;
}

$lime_blog_post_list_layouts = array(
    'cards' => 'cards',
    'content-creator' => 'content creator',
    'frameless' => 'frameless',
    'material2' => 'material 2',
    'material3' => 'material 3',
    'search-engine' => 'search engine',
    'social' => 'social',
);

$lime_blog_sidebar_layouts = array(
    'blocks' => 'blocks',
    'frameless' => 'frameless',
    'frames' => 'frames',
    'material2' => 'material 2',
    'material3' => 'material 3',
    'neon' => 'neon',
    'reddit' => 'Reddit',
    'social' => 'social',
);

$lime_blog_chips_layouts = array(
    'blocks' => 'blocks',
    'color-blocks' => 'color blocks',
    'content-creator' => 'content creator',
    'github' => 'github',
    'hashtag' => 'hashtag',
    'frames' => 'frames',
    'links' => 'links',
    'material2' => 'material 2',
    'material3' => 'material 3',
    'neon' => 'neon',
    'reddit' => 'Reddit',
    'social' => 'social',
    'the_hub' => 'The Hub',
);
