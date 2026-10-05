<?php
/**
 * Template Name: Resources
 * Description: Lists all Resources grouped by category.
 *
 * @package bja-nsvsp
 */

get_header();
?>

<main id="single-page" class="site-main single-page" tabindex="-1">
  <div class="single-page__container">

    <?php
    // Use the page content as intro (like your single layout header)
    if ( have_posts() ) :
      while ( have_posts() ) :
        the_post();
        ?>
        <header class="single-page__header">
          <h1 class="single-page__title h1__heading">
            <?php the_title(); ?>
          </h1>

          <?php
          // Optional: intro/description text from the page content
          $content = get_the_content();
          if ( ! empty( $content ) ) : ?>
            <div class="sub__heading">
              <?php
              // safe content output
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
      // Get all categories that have Resources (posts)
      $resource_cats = get_categories( array(
        'taxonomy'   => 'category',
        'hide_empty' => true,
        'orderby'    => 'name',
        'order'      => 'ASC',
      ) );

      if ( ! empty( $resource_cats ) ) :

        foreach ( $resource_cats as $cat ) :

          // Query Resources (posts) in this category
          $resources_in_cat = new WP_Query( array(
            'post_type'      => 'post',          // your "Resources" are posts with relabeled UI
            'no_found_rows'  => true,
            'posts_per_page' => -1,              // show all for now
            'orderby'        => 'date',
            'order'          => 'DESC',
            'tax_query'      => array(
              array(
                'taxonomy' => 'category',
                'field'    => 'term_id',
                'terms'    => $cat->term_id,
              ),
            ),
          ) );

          if ( $resources_in_cat->have_posts() ) :
            ?>
            <section class="resources-section">
              <h2 id="<?php echo esc_html( $cat->slug ); ?>" class="h2__heading mt-xl">
                <?php echo esc_html( $cat->name ); ?>
              </h2>

              <?php if ( ! empty( $cat->description ) ) : ?>
                <p class="sub__heading">
                  <?php echo esc_html( $cat->description ); ?>
                </p>
              <?php endif; ?>

              <div class="resources__container resources__container--landing">
                <?php
                while ( $resources_in_cat->have_posts() ) :
                  $resources_in_cat->the_post();
                  ?>
                  <article class="resource">
                    <h3 class="resource__heading">
                      <a href="<?php the_permalink(); ?>">
                        <?php the_title(); ?>
                      </a>
                    </h3>

                   

                    <p class="resource__description">
                      <?php
                      // Use existing excerpt or trim content
                      if ( has_excerpt() ) {
                        the_excerpt();
                      } else {
                        echo esc_html( wp_trim_words( get_the_content(), 30, '…' ) );
                      }
                      ?>
                    </p>

                    <a href="<?php the_permalink(); ?>" class="btn">
                      Learn More<span class="screen-reader-text"> about <?php the_title(); ?></span>
                    </a>
                  </article>
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
        <p class="resources__empty">
          There are no resources to display at this time.
        </p>
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
        <h2 class="h4__heading">Resource Spotlight</h2>

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