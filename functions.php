<?php
/** 
 * Custom Functions
 *
 * ! What the custom functions do:
 * *    1. Enqueues all styles and scripts
 * *    2. Asynchronously load scripts for speed optimization
 *      
 */

// Temporary homepage visibility: set this to true to restore all three sections.
// Their standalone pages and navigation links remain available.
function bjansvsp_show_home_sections() {
  return false;
}

// * * --------| Actions and filters in order |-------- *

  // Action to enque styles and scripts
  add_action( 'wp_enqueue_scripts', 'theme_enqueue_scripts' );

  // Asynchronously load scripts




// * * --------| Functions in order |-------- *

  //Enqueuing styles and scripts
 function theme_enqueue_scripts() {
  // CSS
  wp_enqueue_style('bjansvsp_main_styles', get_stylesheet_uri(), [], filemtime(get_stylesheet_directory() . '/style.css'));

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
    ['in_footer' => true, 'strategy' => 'defer']
  );
}




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
  add_theme_support( 'title-tag' );
  add_theme_support( 'html5', ['search-form', 'gallery', 'caption', 'style', 'script'] );

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
  wp_enqueue_style('bjansvsp_main_styles', get_stylesheet_uri(), [], filemtime(get_stylesheet_directory() . '/style.css'));
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




 

// Shared defaults keep the Customizer preview and the public footer in sync.
function bjansvsp_footer_defaults() {
  return [
    'bjansvsp_footer_text' => "The <b>National Survey of Victim Service Providers</b> is a component of the Office for Victims of Crime, Office of Justice Programs, U.S. Department of Justice.\n\nThis website is funded through xxx. Neither the Bureau Justice Statistics nor any of its components operate, control, are responsible for, or necessarily endorse, this website (including, without limitation, its content, technical infrastructure, and policies, and any services or tools provided). Lorem ipsum dolor sit amet consectetur adipiscing elit. Sit amet consectetur adipiscing elit quisque faucibus ex.",
    'bjansvsp_contact_email' => 'Support@NSVSP.org',
    'bjansvsp_contact_phone' => '(227) 248-9484',
    'bjansvsp_contact_address' => '1902 Reston Metro Plaza | Reston, VA 20190',
  ];
}

// Permit text formatting without allowing client-entered layout or scripts.
function bjansvsp_sanitize_footer_disclaimer($value) {
  return wp_kses($value, [
    'p' => [], 'br' => [], 'strong' => [], 'b' => [], 'em' => [], 'i' => [],
    'a' => ['href' => [], 'title' => []],
  ]);
}

function bjansvsp_format_footer_disclaimer($value) {
  $html = wpautop(bjansvsp_sanitize_footer_disclaimer($value));
  return str_replace('<p>', '<p class="footer__text">', $html);
}

function bjansvsp_validate_contact_email($validity, $value) {
  if (trim($value) !== '' && !is_email($value)) {
    $validity->add('invalid_email', __('Enter a valid email address, or leave this field empty.', 'bjansvsp'));
  }
  return $validity;
}

function bjansvsp_customize_register($wp_customize) {
  $defaults = bjansvsp_footer_defaults();
  $wp_customize->add_section('bjansvsp_footer_section', [
    'title' => __('Footer Settings', 'bjansvsp'),
    'priority' => 200,
    'description' => __('Update the footer disclaimer and contact details. Preview your changes, then select Publish to save them.', 'bjansvsp'),
  ]);

  // Retain the existing setting ID so previously saved footer text still works.
  $wp_customize->add_setting('bjansvsp_footer_text', [
    'default' => $defaults['bjansvsp_footer_text'],
    'sanitize_callback' => 'bjansvsp_sanitize_footer_disclaimer',
    'transport' => 'refresh',
  ]);
  $wp_customize->add_control('bjansvsp_footer_text_control', [
    'label' => __('Footer Disclaimer', 'bjansvsp'),
    'description' => __('Separate paragraphs with a blank line. The footer keeps its current font, spacing and colors. Leave empty to remove the disclaimer.', 'bjansvsp'),
    'section' => 'bjansvsp_footer_section',
    'settings' => 'bjansvsp_footer_text',
    'type' => 'textarea',
    'input_attrs' => ['rows' => 10],
    'priority' => 10,
  ]);

  $fields = [
    'email' => ['label' => __('Contact Email', 'bjansvsp'), 'type' => 'email', 'sanitize' => 'sanitize_email'],
    'phone' => ['label' => __('Contact Phone', 'bjansvsp'), 'type' => 'tel', 'sanitize' => 'sanitize_text_field'],
    'address' => ['label' => __('Contact Address', 'bjansvsp'), 'type' => 'textarea', 'sanitize' => 'sanitize_textarea_field'],
  ];
  foreach ($fields as $key => $field) {
    $id = 'bjansvsp_contact_' . $key;
    $args = [
      'default' => $defaults[$id],
      'sanitize_callback' => $field['sanitize'],
      'transport' => 'refresh',
    ];
    if ($key === 'email') $args['validate_callback'] = 'bjansvsp_validate_contact_email';
    $wp_customize->add_setting($id, $args);
    $wp_customize->add_control($id, [
      'label' => $field['label'],
      'description' => $key === 'address'
        ? __('Line breaks are preserved. Leave empty to hide the address.', 'bjansvsp')
        : __('Leave empty to hide this contact detail.', 'bjansvsp'),
      'section' => 'bjansvsp_footer_section',
      'type' => $field['type'],
      'priority' => 20,
    ]);
  }
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
  // --- Glossary ---
register_post_type('glossary', [
  'labels' => [
    'name' => 'Glossary',
    'singular_name' => 'Term',
    'add_new_item' => 'Add New Term',
    'edit_item' => 'Edit Term',
    'new_item' => 'New Term',
    'view_item' => 'View Term',
    'search_items' => 'Search Terms',
    'not_found' => 'No terms found',
  ],
  'public' => true,
  'has_archive' => false, // 🔴 Disable archive so /glossary/ uses your Page
  'rewrite' => ['slug' => 'glossary-term'], // 🔴 Move single terms to /glossary-term/slug/
  'menu_icon' => 'dashicons-book',
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


// Attach built-in Categories to FAQs and Glossary so we can match by category
add_action('init', function () {
  // Allow FAQs to use regular WP Categories
  register_taxonomy_for_object_type('category', 'faq');

  // Allow Glossary terms to use regular WP Categories
  register_taxonomy_for_object_type('category', 'glossary');
}, 20);

/**
 * One-time migration: Convert all existing "Dictionary" posts to "Glossary".
 *
 * ⚠️ IMPORTANT:
 * - Add this temporarily to functions.php (or a custom plugin).
 * - Visit your site once (this will run on 'init').
 * - Then remove or comment out this code to prevent it from running again.

add_action('init', function() {
    global $wpdb;
    $wpdb->update(
        $wpdb->posts,
        ['post_type' => 'glossary'],
        ['post_type' => 'dictionary']
    );
}); */



// Font requests stay on the same server as the theme. Preload only normal text faces.
add_action('wp_head', function () {
  foreach (['Roboto-Variable-Latin.woff2', 'OpenSans-Variable-Latin.woff2'] as $font) {
    printf('<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n", esc_url(get_stylesheet_directory_uri() . '/assets/fonts/' . $font));
  }
}, 2);

// Let an installed SEO plugin own descriptions; provide a theme fallback otherwise.
add_action('wp_head', function () {
  if (defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION') || defined('AIOSEO_VERSION') || defined('SEOPRESS_VERSION')) return;
  if (is_front_page()) {
    $description = 'The National Survey of Victim Service Providers (NSVSP) provides national data on organizations serving victims of crime or abuse. Explore resources, FAQs and the survey.';
  } elseif (is_singular()) {
    $post = get_queried_object();
    $description = $post->post_excerpt ?: $post->post_content;
  } elseif (is_search()) {
    $description = 'Search results for ' . get_search_query() . ' on ' . get_bloginfo('name') . '.';
  } elseif (is_archive()) {
    $description = get_the_archive_description() ?: wp_strip_all_tags(get_the_archive_title()) . ' resources from ' . get_bloginfo('name') . '.';
  } else {
    return;
  }
  $description = trim(preg_replace('/\s+/', ' ', wp_strip_all_tags(strip_shortcodes($description))));
  $description = wp_html_excerpt($description, 160, '…');
  $description = apply_filters('bjansvsp_meta_description', $description);
  if ($description) printf('<meta name="description" content="%s">' . "\n", esc_attr($description));
}, 1);

// Select an image sized for the displayed logo, rather than the entire viewport.
add_filter('wp_get_attachment_image_attributes', function ($attrs) {
  if (strpos($attrs['class'] ?? '', 'custom-logo') !== false) {
    $attrs['sizes'] = '(min-width: 1480px) 338px, (min-width: 1000px) calc((100vw - 90px) / 4), (min-width: 800px) calc((100vw - 84px) / 4), 220px';
    $attrs['loading'] = 'eager';
    $attrs['decoding'] = 'async';
    $attrs['fetchpriority'] = 'high';
  }
  return $attrs;
});

// Native emoji rendering avoids downloading a frontend polyfill.
add_action('init', function () {
  remove_action('wp_head', 'print_emoji_detection_script', 7);
  remove_action('wp_print_styles', 'print_emoji_styles');
  remove_action('wp_enqueue_scripts', 'wp_enqueue_emoji_styles');
});

// Agency policy links provide working defaults and remain editable by the client.
function bjansvsp_footer_policy_links() {
  return [
    'accessibility' => ['label' => 'Accessibility', 'url' => 'https://www.justice.gov/accessibility/accessibility-statement'],
    'plain-language' => ['label' => 'Plain Language', 'url' => 'https://www.justice.gov/open/plain-writing-act'],
    'privacy' => ['label' => 'Privacy Policy', 'url' => 'https://www.justice.gov/doj/privacy-policy'],
    'legal' => ['label' => 'Legal Policies and Disclaimer', 'url' => 'https://www.justice.gov/legalpolicies'],
    'no-fear' => ['label' => 'No FEAR Act', 'url' => 'https://www.justice.gov/jmd/eeo-program-status-report'],
    'foia' => ['label' => 'Freedom of Information Act', 'url' => 'https://www.ojp.gov/program/ojp-freedom-information-act/foia-overview'],
  ];
}

add_action('customize_register', function ($wp_customize) {
  foreach (bjansvsp_footer_policy_links() as $key => $link) {
    $setting = 'bjansvsp_policy_' . $key;
    $wp_customize->add_setting($setting, ['default' => $link['url'], 'sanitize_callback' => 'esc_url_raw']);
    $wp_customize->add_control($setting, [
      'label' => $link['label'] . ' URL',
      'description' => __('Defaults to the official agency page. Replace with your own URL, or leave empty to hide the link. A published WordPress privacy page takes priority over the Privacy Policy URL.', 'bjansvsp'),
      'section' => 'bjansvsp_footer_section', 'type' => 'url', 'priority' => 30,
    ]);
  }
});
