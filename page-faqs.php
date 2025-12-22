<?php
/**
 * Template Name: FAQs
 * Description: Lists all FAQs in accordions, grouped by category (similar to Resources page).
 *
 * @package bja-nsvsp
 */

get_header();
?>

<main id="single-page" class="site-main single-page">
  <div class="single-page__container">

    <?php
    // Use the page content as intro (like your single layout header).
    if ( have_posts() ) :
      while ( have_posts() ) :
        the_post();
        ?>
        <header class="single-page__header">
          <h1 class="single-page__title h1__heading">
            <?php the_title(); ?>
          </h1>

          <?php
          // Optional: intro/description text from the page content.
          $content = get_the_content();
          if ( ! empty( $content ) ) :
            ?>
            <div class="sub__heading">
              <?php
              echo wp_kses_post( apply_filters( 'the_content', $content ) );
              ?>
            </div>
          <?php endif; ?>
        </header>
        <?php
      endwhile;
    endif;
    ?>

    <section class="single-page__main">
      <?php
      // Optional global FAQs subheading from ACF on this page.
      $faqs_subheading = function_exists( 'get_field' ) ? get_field( 'faqs_subheading', get_queried_object_id() ) : '';
      if ( ! empty( $faqs_subheading ) ) :
        ?>
        <p class="sub__heading">
          <?php echo esc_html( $faqs_subheading ); ?>
        </p>
      <?php endif; ?>

      <?php
      // Determine taxonomy: prefer a dedicated FAQ taxonomy if it exists.
      $faq_tax = taxonomy_exists( 'faq_category' ) ? 'faq_category' : 'category';

      // Get all categories/terms that have FAQ posts.
      $faq_terms = get_terms( array(
        'taxonomy'   => $faq_tax,
        'hide_empty' => true,
        'orderby'    => 'name',
        'order'      => 'ASC',
      ) );

      if ( ! empty( $faq_terms ) && ! is_wp_error( $faq_terms ) ) :

        foreach ( $faq_terms as $term ) :

          // Query FAQs in this category/term.
          $faqs = new WP_Query( array(
            'post_type'      => 'faq',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => array(
              'menu_order' => 'ASC',
              'date'       => 'DESC',
            ),
            'tax_query'      => array(
              array(
                'taxonomy' => $faq_tax,
                'field'    => 'term_id',
                'terms'    => $term->term_id,
              ),
            ),
            'no_found_rows'  => true,
          ) );

          if ( $faqs->have_posts() ) :
            ?>
            <section id="faqs" class="faqs-section">
              <h2 id="<?php echo esc_html( $term->slug ); ?>" class="h2__heading  mt-xl">
                <?php echo esc_html( $term->name ); ?>
              </h2>

              <?php
              // Term description or special uncategorized blurb.
              if ( ! empty( $term->description ) ) :
                ?>
                <p class="sub__heading">
                  <?php echo esc_html( $term->description ); ?>
                </p>
              <?php elseif ( 'uncategorized' === $term->slug ) : ?>
                <p class="sub__heading">
                  These resources don’t yet belong to a specific FAQ category, but they still provide helpful information and context. As our content grows and is reviewed, items listed here will be organized into the categories that best match their focus and purpose. For now, this section serves as a temporary home for commonly asked questions that are still being evaluated and properly classified.
                </p>
              <?php endif; ?>

              <div class="faqs__accordion" role="region" aria-label="<?php echo esc_attr( $term->name . ' FAQs' ); ?>">
                <?php
                $i = 0;
                while ( $faqs->have_posts() ) :
                  $faqs->the_post();
                  $i++;
                  $panel_id = 'faq-' . get_the_ID();
                  ?>
                  <details class="faq" <?php if ( 1 === $i ) echo 'open'; // first FAQ in each category open by default ?>>
                    <summary class="faq__question">
                      <span class="faq__q-text"><?php the_title(); ?></span>
                      <span class="faq__icon" aria-hidden="true"></span>
                    </summary>
                    <div class="faq__answer" id="<?php echo esc_attr( $panel_id ); ?>">
                      <?php echo wpautop( wp_kses_post( get_the_content() ) ); ?>
                    </div>
                  </details>
                  <?php
                endwhile;
                ?>
              </div>
            </section>
            <?php
          endif;

          wp_reset_postdata();

        endforeach;

      else :
        ?>
        <p>No FAQs available at this time.</p>
      <?php endif; ?>
    </section>

  </div>

  <?php
  // Build a global "Our Newest Downloads" list (top 5 across Resources).
  $recent_downloads = array();

  if ( function_exists( 'get_field' ) ) {
    $recent_posts = new WP_Query(
      array(
        'post_type'      => array( 'post' ), // Resources only.
        'post_status'    => 'publish',
        'posts_per_page' => 40,             // Search a batch to find up to 5 files.
        'orderby'        => 'date',
        'order'          => 'DESC',
        'no_found_rows'  => true,
      )
    );

    if ( $recent_posts->have_posts() ) {
      while ( $recent_posts->have_posts() ) {
        $recent_posts->the_post();
        $pid       = get_the_ID();
        $parent_ts = (int) get_post_time( 'U', true, $pid ); // Fallback timestamp.

        for ( $i = 1; $i <= 4; $i++ ) {
          $val = get_field( 'download_file_' . $i, $pid );
          if ( empty( $val ) ) {
            continue;
          }

          $url   = '';
          $label = '';
          $ts    = $parent_ts;

          if ( is_array( $val ) ) {
            // ACF File array.
            $url   = $val['url'] ?? '';
            $label = $val['title'] ?? ( $val['filename'] ?? '' );

            if ( ! empty( $val['ID'] ) && is_numeric( $val['ID'] ) ) {
              $att_id = (int) $val['ID'];
              $ts     = (int) get_post_time( 'U', true, $att_id ) ?: $parent_ts;
            }
          } elseif ( is_numeric( $val ) ) {
            // Attachment ID.
            $att_id = (int) $val;
            $url    = wp_get_attachment_url( $att_id ) ?: '';
            $label  = get_the_title( $att_id ) ?: '';
            $ts     = (int) get_post_time( 'U', true, $att_id ) ?: $parent_ts;
          } elseif ( is_string( $val ) ) {
            // Raw URL string.
            $url   = $val;
            $label = basename( parse_url( $url, PHP_URL_PATH ) );
          }

          if ( $url ) {
            $recent_downloads[] = array(
              'url'      => $url,
              'label'    => $label !== '' ? $label : basename( parse_url( $url, PHP_URL_PATH ) ),
              'ts'       => $ts,
              'post_id'  => $pid,
              'post_ttl' => get_the_title( $pid ),
            );
          }
        }
      }
      wp_reset_postdata();
    }

    if ( ! empty( $recent_downloads ) ) {
      // Newest first, keep top 5 only.
      usort(
        $recent_downloads,
        function ( $a, $b ) {
          return $b['ts'] <=> $a['ts'];
        }
      );
      $recent_downloads = array_slice( $recent_downloads, 0, 5 );
    }
  }
  ?>

  <aside class="downloads">
    <h4 class="h4__heading">Resource Spotlight</h4>

    <?php if ( ! empty( $recent_downloads ) ) : ?>

      <p class="downloads__empty">Download our newest resources.</p>

      <?php foreach ( $recent_downloads as $item ) : ?>
        <a class="downloads__link"
          href="<?php echo esc_url( $item['url'] ); ?>"
          target="_blank"
          rel="noopener"
          title="<?php echo esc_attr( 'From: ' . $item['post_ttl'] ); ?>">
          <?php echo esc_html( $item['label'] ); ?>
        </a>
      <?php endforeach; ?>

    <?php else : ?>

      <p class="downloads__empty">There are no downloads to show at this time.</p>

    <?php endif; ?>
  </aside>

</main>

<?php
get_footer();