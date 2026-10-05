<?php
/**
 * The template for displaying a single Resource page
 *
 * SIDEBAR LOGIC:
 * - If the current Resource has downloads (download_file_1..4), show ONLY those downloads.
 * - If the Resource has NO downloads, show ONLY the Resource Spotlight items
 *   from the ACF Options repeater.
 *
 * No “Newest Downloads” fallback.
 * Same markup, classes, and CSS as existing downloads sidebar.
 *
 * @package bja-nsvsp
 */

get_header();
?>

<main id="single-page" tabindex="-1">
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

  <section class="single-resource">
    <h1 class="h1__heading"><?php the_title(); ?></h1>

    <?php
    // ------------------------------------------------------------
    // Display Categories (text only)
    // ------------------------------------------------------------
    $cats = get_the_terms( get_the_ID(), 'category' );
    if ( ! is_wp_error( $cats ) && ! empty( $cats ) ) {
      $cat_names = implode( ', ', wp_list_pluck( $cats, 'name' ) );
      echo '<p class="single-resource__category">Category: ' . esc_html( $cat_names ) . '</p>';
    }
    ?>

    <p class="single-resource__date">
      Date Published: <?php echo esc_html( get_the_date( 'F j, Y' ) ); ?>
    </p>

    <div class="single-resource__content">
      <?php
        the_content();
        wp_link_pages([
          'before' => '<div class="page-links">',
          'after'  => '</div>',
        ]);
      ?>
    </div>

    <?php
    // ------------------------------------------------------------
    // Related FAQs (by shared Category)
    // ------------------------------------------------------------
    if ( get_post_type() === 'post' ) : ?>
      <div id="faqs" class="faqs related-faqs">
        <h2 class="h2__heading">Related FAQs</h2>

        <?php
        $cats    = get_the_terms( get_the_ID(), 'category' );
        $cat_ids = ( $cats && ! is_wp_error( $cats ) ) ? wp_list_pluck( $cats, 'term_id' ) : [];

        if ( ! empty( $cat_ids ) ) :
          $related_faqs = new WP_Query([
            'post_type'      => 'faq',
            'posts_per_page' => 4,
            'post_status'    => 'publish',
            'tax_query'      => [[
              'taxonomy' => 'category',
              'field'    => 'term_id',
              'terms'    => $cat_ids,
            ]],
            'orderby' => 'date',
            'order'   => 'DESC',
          ]);
        ?>

          <?php if ( $related_faqs->have_posts() ) : ?>
            <div class="faqs__accordion">
              <?php while ( $related_faqs->have_posts() ) : $related_faqs->the_post();
                $answer = apply_filters( 'the_content', get_post_field( 'post_content', get_the_ID() ) );
              ?>
                <details class="faq">
                  <summary class="faq__question">
                    <span class="faq__q-text"><?php the_title(); ?></span>
                    <span class="faq__icon" aria-hidden="true"></span>
                  </summary>
                  <div class="faq__answer">
                    <?php echo $answer; ?>
                  </div>
                </details>
              <?php endwhile; wp_reset_postdata(); ?>
            </div>
          <?php else : ?>
            <p class="related-faqs__empty">There are no related FAQs for this resource at this time.</p>
          <?php endif; ?>

        <?php else : ?>
          <p class="related-faqs__empty">There are no related FAQs for this resource at this time.</p>
        <?php endif; ?>
      </div>
    <?php endif; ?>

  </section><!-- /.single-resource -->

  <?php
  // ============================================================
  // SIDEBAR DATA COLLECTION
  // ============================================================

  // ------------------------------------------------------------
  // 1) Collect this resource's downloads
  // ------------------------------------------------------------
  $downloads = [];

  if ( function_exists( 'get_field' ) ) {
    for ( $i = 1; $i <= 4; $i++ ) {
      $val = get_field( 'download_file_' . $i );
      if ( empty( $val ) ) continue;

      $url   = '';
      $label = '';

      if ( is_array( $val ) ) {
        // ACF File array
        $url   = $val['url'] ?? '';
        $label = $val['title'] ?? ( $val['filename'] ?? '' );
      } elseif ( is_numeric( $val ) ) {
        // Attachment ID
        $att_id = (int) $val;
        $url    = wp_get_attachment_url( $att_id ) ?: '';
        $label  = get_the_title( $att_id ) ?: '';
      } elseif ( is_string( $val ) ) {
        // Raw URL
        $url   = $val;
        $label = basename( parse_url( $url, PHP_URL_PATH ) );
      }

      if ( $url ) {
        $downloads[] = [
          'url'   => $url,
          'label' => $label !== '' ? $label : basename( parse_url( $url, PHP_URL_PATH ) ),
        ];
      }
    }
  }

  // ------------------------------------------------------------
  // 2) If no downloads, collect Resource Spotlight items
  // ------------------------------------------------------------
  $spotlight_items = [];

  if ( empty( $downloads ) && function_exists( 'have_rows' ) && have_rows( 'resource_spotlight_items', 'option' ) ) {
    while ( have_rows( 'resource_spotlight_items', 'option' ) ) {
      the_row();

      $type        = get_sub_field( 'spotlight_type' );   // resource | file
      $label       = get_sub_field( 'spotlight_label' );  // optional override
      $url         = '';
      $final_label = '';
      $new_tab     = false;

      if ( $type === 'resource' ) {
        // Resource = Post Object
        $post_obj = get_sub_field( 'spotlight_resource' );
        if ( $post_obj ) {
          $url         = get_permalink( $post_obj );
          $final_label = $label ? $label : get_the_title( $post_obj );
          $new_tab     = false;
        }
      }

      if ( $type === 'file' ) {
        // File = ACF File array
        $file = get_sub_field( 'spotlight_file' );
        if ( is_array( $file ) && ! empty( $file['url'] ) ) {
          $url = $file['url'];

          if ( $label ) {
            $final_label = $label;
          } elseif ( ! empty( $file['title'] ) ) {
            $final_label = $file['title'];
          } elseif ( ! empty( $file['filename'] ) ) {
            $final_label = $file['filename'];
          } else {
            $final_label = basename( parse_url( $url, PHP_URL_PATH ) );
          }

          $new_tab = true;
        }
      }

      if ( $url && $final_label ) {
        $spotlight_items[] = [
          'url'   => $url,
          'label' => $final_label,
          'blank' => $new_tab,
        ];
      }
    }
  }
  ?>

  <aside class="downloads">
    <?php if ( ! empty( $downloads ) ) : ?>

      <h2 class="h4__heading">Downloads</h2>

      <?php foreach ( $downloads as $dl ) : ?>
        <a class="downloads__link"
           href="<?php echo esc_url( $dl['url'] ); ?>"
           target="_blank"
           rel="noopener">
          <?php echo esc_html( $dl['label'] ); ?>
        </a>
      <?php endforeach; ?>

    <?php else : ?>

      <h2 class="h4__heading">Resource Spotlight</h2>

      <?php if ( ! empty( $spotlight_items ) ) : ?>
        <?php foreach ( $spotlight_items as $item ) : ?>
          <a class="downloads__link"
             href="<?php echo esc_url( $item['url'] ); ?>"
             <?php if ( $item['blank'] ) : ?>target="_blank" rel="noopener"<?php endif; ?>>
            <?php echo esc_html( $item['label'] ); ?>
          </a>
        <?php endforeach; ?>
      <?php else : ?>
        <p class="downloads__empty">There are no downloads to show at this time.</p>
      <?php endif; ?>

    <?php endif; ?>

    <a class="btn" href="https://www.icfsurvey2.com/NSVSP" target="_blank" rel="noopener noreferrer">Take Survey<span class="screen-reader-text"> (opens in a new tab)</span></a>
  </aside>

<?php endwhile; endif; ?>
</main>

<?php get_footer(); ?>