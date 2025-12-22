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
    /**
     * ============================================================
     * Resource Spotlight (ACF Options) — Sidebar (Spotlight ONLY)
     * ============================================================
     *
     * PURPOSE
     * -------
     * This sidebar is used on:
     * - Resources page
     * - Glossary page
     * - FAQs page
     *
     * These pages have NO per-page downloads.
     * We intentionally show ONLY the curated Resource Spotlight list.
     *
     * Editors manage this list via an ACF Options Page.
     *
     * ACF Options Repeater:
     * - resource_spotlight_items
     *
     * Sub-fields:
     * - spotlight_type     (resource | file)
     * - spotlight_resource (Post Object)  [when type = resource]
     * - spotlight_file     (File array)   [when type = file]
     * - spotlight_label    (Text)         [optional override]
     *
     * MARKUP RULES
     * ------------
     * - Uses existing sidebar wrapper: <aside class="downloads">
     * - Uses existing link class: .downloads__link
     * - Uses existing empty text class: .downloads__empty
     * - NO downloads logic
     * - NO newest-downloads fallback
     */

    $spotlight_items = array();

    if ( function_exists( 'have_rows' ) && have_rows( 'resource_spotlight_items', 'option' ) ) {
      while ( have_rows( 'resource_spotlight_items', 'option' ) ) {
        the_row();

        $type  = (string) get_sub_field( 'spotlight_type' );
        $label = (string) get_sub_field( 'spotlight_label' );

        $url         = '';
        $final_label = '';
        $new_tab     = false;

        // --------------------------------------------------------
        // Spotlight type: RESOURCE (internal post link)
        // --------------------------------------------------------
        if ( $type === 'resource' ) {
          $post_obj = get_sub_field( 'spotlight_resource' );
          if ( $post_obj ) {
            $url         = get_permalink( $post_obj );
            $final_label = $label ? $label : get_the_title( $post_obj );
            $new_tab     = false;
          }
        }

        // --------------------------------------------------------
        // Spotlight type: FILE (media library download)
        // --------------------------------------------------------
        if ( $type === 'file' ) {
          $file = get_sub_field( 'spotlight_file' );
          if ( is_array( $file ) && ! empty( $file['url'] ) ) {
            $url = $file['url'];

            // Label priority:
            // 1) Manual override
            // 2) File title
            // 3) Filename
            // 4) URL basename
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

        // --------------------------------------------------------
        // Store only valid items
        // --------------------------------------------------------
        if ( $url && $final_label ) {
          $spotlight_items[] = array(
            'url'   => $url,
            'label' => $final_label,
            'blank' => $new_tab,
          );
        }
      }
    }
    ?>

    <aside class="downloads">
      <h4 class="h4__heading">Resource Spotlight</h4>

      <?php if ( ! empty( $spotlight_items ) ) : ?>

        <p class="downloads__empty">Explore our featured resources.</p>

        <?php foreach ( $spotlight_items as $item ) : ?>
          <a class="downloads__link"
            href="<?php echo esc_url( $item['url'] ); ?>"
            <?php if ( ! empty( $item['blank'] ) ) : ?>target="_blank" rel="noopener"<?php endif; ?>>
            <?php echo esc_html( $item['label'] ); ?>
          </a>
        <?php endforeach; ?>

      <?php else : ?>

        <p class="downloads__empty">
          There are no featured resources to show at this time.
        </p>

      <?php endif; ?>
    </aside>

</main>

<?php
get_footer();