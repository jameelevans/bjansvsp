<?php
/** 
 * Custom Functions
 *
 * ! What the custom functions do:
 * *    1. Enqueues all styles and scripts
 * *    2. Asynchronously load scripts for speed optimization
 *      
 */

// * * --------| Actions and filters in order |-------- *

  // Action to enque styles and scripts
  add_action( 'wp_enqueue_scripts', 'theme_enqueue_scripts' );

  // Asynchronously load scripts




// * * --------| Functions in order |-------- *

  //Enqueuing styles and scripts
 function theme_enqueue_scripts() {
  // CSS
  wp_enqueue_style('bjansvsp_main_styles', get_stylesheet_uri());

  // JS (cache-busted by filemtime)
  // Adjust path if your build outputs elsewhere
  $rel  = '/assets/js/scripts-bundled.js';
  $path = get_stylesheet_directory() . $rel;
  $uri  = get_stylesheet_directory_uri() . $rel;

  wp_enqueue_script(
    'Bundled_js',               // NEW handle (use this in the defer filter below)
    $uri,
    [],                              // Add deps if you truly depend on them
    file_exists($path) ? filemtime($path) : null,
    true                             // in footer
  );
}

add_filter('script_loader_tag', function ($tag, $handle, $src) {
  if ($handle === 'Bundled_js') {
    // Choose ONE: async or defer (defer is usually safer for bundles)
    // $tag = str_replace(' src', ' async src', $tag);
    $tag = str_replace(' src', ' defer src', $tag);
  }
  return $tag;
}, 10, 3);


   //* 3. Activates the ability to add custom logo in customizer
function bjansvsp_custom_logo_setup() {
  $defaults = array(
      'height'      => 38,
      'width'       => 38,
      'flex-height' => true,
      'flex-width'  => true,
      'header-text' => array( 'BJANSVSP', 'National Survey of Victim Service Providers' ),
  );
  add_theme_support( 'custom-logo', $defaults );

  //* 4. Enable support for custom sized Post Thumbnails on posts and pages
  add_image_size( 'my-thumbnail', 300, 169, false);
  add_image_size( 'x-small', 450, 253, false);
  add_image_size( 'small', 600, 338, false);
  add_image_size( 'medium', 768, 432, false);
  add_image_size( 'regular', 1024, 576, false);
  add_image_size( 'large', 1200, 675, false);
  add_image_size( 'med-large', 1600, 901, false);
  add_image_size( 'x-large', 2000, 1125, false);
  add_image_size( 'xx-large', 3000, 1688, false);
  add_image_size( 'full-size', 3200, 1801, false);
  add_image_size( 'staff-headshot', 304, 350, true);
  add_image_size('pageBanner', 1300, 700, true);
}
add_action( 'after_setup_theme', 'bjansvsp_custom_logo_setup' );
add_theme_support( 'post-thumbnails' );
// .Activate the ability to add custom logo in customizer
// .Enable support for Post Thumbnails on posts and pages


//* 5. Add site link to logo on login screen
function ourHeaderUrl() {
  return esc_url(site_url('/'));
}
add_filter('login_headerurl', 'ourHeaderUrl');
// .Add site link to logo on login screen





//* 4. Make css styles available to login screen
function bjansvsp_login_css() {
  wp_enqueue_style('bjansvsp_main_styles', get_stylesheet_uri());
  }
add_action('login_enqueue_scripts', 'bjansvsp_login_css');
// .Make css styles available to login screen

//* 5. Replace WP logo with site title name on login screen
function bjansvsp_login_title() {
  return get_bloginfo('name');
}
add_filter('login_headertitle', 'bjansvsp_login_title');
// .Replace WP logo with site title name on login screen


//* 7. Add theme title to login screen
function ourLoginTitle() {
  return get_bloginfo('name');
}
add_filter('login_headertitle', 'ourLoginTitle');
// .Add theme title to login screen

 //* 7.  Display inline svg icon from sprite sheet with custom class
function svg_icon($class, $icon) { ?>
  <svg class="<?php echo $class ?>" aria-hidden="true">
    <use
      xlink:href="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/img/sprite.svg' ); ?>#icon-<?php echo $icon ?>">
    </use>
  </svg>
  <?php } 
  // .Display inline svg icon from sprite sheet with custom class




 

// Add Footer Text Setting to Customizer
function bjansvsp_customize_register($wp_customize) {
  // Add Footer Section
  $wp_customize->add_section('bjansvsp_footer_section', array(
      'title'       => __('Footer Settings', 'bjansvsp'),
      'priority'    => 200,
      'description' => 'Customize the footer text',
  ));

  // Add Footer Text Setting
  $wp_customize->add_setting('bjansvsp_footer_text', array(
      'default'           => '', // Default is blank; admin must provide text
      'sanitize_callback' => 'wp_kses_post', // Allows safe HTML for formatting
      'transport'         => 'refresh',
  ));

  // Add Footer Text Control
  $wp_customize->add_control('bjansvsp_footer_text_control', array(
      'label'       => __('Footer Text', 'bjansvsp'),
      'section'     => 'bjansvsp_footer_section',
      'settings'    => 'bjansvsp_footer_text',
      'type'        => 'textarea', // Allows for longer text
  ));
}
add_action('customize_register', 'bjansvsp_customize_register');


// Rename built-in "Posts" to "Resources"
add_filter('post_type_labels_post', function ($labels) {
  $labels->name                     = 'Resources';
  $labels->singular_name            = 'Resource';
  $labels->menu_name                = 'Resources';
  $labels->name_admin_bar           = 'Resource';
  $labels->all_items                = 'All Resources';
  $labels->add_new                  = 'Add Resource';
  $labels->add_new_item             = 'Add New Resource';
  $labels->edit_item                = 'Edit Resource';
  $labels->new_item                 = 'Resource';
  $labels->view_item                = 'View Resource';
  $labels->search_items             = 'Search Resources';
  $labels->not_found                = 'No resources found';
  $labels->not_found_in_trash       = 'No resources found in Trash';
  $labels->archives                 = 'Resource Archives';
  return $labels;
});

// (Optional) Rename Categories & Tags to Resource labels
add_action('init', function () {
  global $wp_taxonomies;
  if ( isset($wp_taxonomies['category']->labels) ) {
    $cat = &$wp_taxonomies['category']->labels;
    $cat->name = 'Resource Categories';
    $cat->singular_name = 'Resource Category';
    $cat->menu_name = 'Resource Categories';
  }
  if ( isset($wp_taxonomies['post_tag']->labels) ) {
    $tag = &$wp_taxonomies['post_tag']->labels;
    $tag->name = 'Resource Tags';
    $tag->singular_name = 'Resource Tag';
    $tag->menu_name = 'Resource Tags';
  }
}, 11);


// Custom Post Types
add_action('init', function() {
  // --- Dictionary ---
  register_post_type('dictionary', [
    'labels' => [
      'name' => 'Dictionary',
      'singular_name' => 'Term',
      'add_new_item' => 'Add New Term',
      'edit_item' => 'Edit Term',
      'new_item' => 'New Term',
      'view_item' => 'View Term',
      'search_items' => 'Search Terms',
      'not_found' => 'No terms found',
    ],
    'public' => true,
    'has_archive' => true,
    'rewrite' => ['slug' => 'dictionary'],
    'menu_icon' => 'dashicons-book', // 📘
    'supports' => ['title', 'editor', 'excerpt'],
    'show_in_rest' => true,
  ]);

  // --- FAQs ---
  register_post_type('faq', [
    'labels' => [
      'name' => 'FAQs',
      'singular_name' => 'FAQ',
      'add_new_item' => 'Add New FAQ',
      'edit_item' => 'Edit FAQ',
      'new_item' => 'New FAQ',
      'view_item' => 'View FAQ',
      'search_items' => 'Search FAQs',
      'not_found' => 'No FAQs found',
    ],
    'public' => true,
    'has_archive' => false,
    'rewrite' => ['slug' => 'faqs'],
    'menu_icon' => 'dashicons-editor-help', // ❓
    'supports' => ['title', 'editor', 'excerpt', 'revisions', 'page-attributes'],
    'show_in_rest' => true,
  ]);
});

// Attach built-in Categories to FAQs so we can match by category
add_action('init', function () {
  register_taxonomy_for_object_type('category', 'faq');
}, 20);



