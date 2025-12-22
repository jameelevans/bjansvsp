<?php
/**
 * Search results template
 *
 * This page shows:
 * 1) Search results grid (all public post types)
 * 2) Sidebar: ONLY the curated "Resource Spotlight" list from ACF Options
 *
 * Editors manage Resource Spotlight in WP Admin via an ACF Options Page.
 *
 * @package bja-nsvsp
 */

get_header();
?>

<main id="search-page">
  <section class="search">

    <?php
      /**
       * ============================================================
       * Build the search query
       * ============================================================
       *
       * We search across ALL public post types (Posts, Pages, CPTs),
       * matching your current behavior.
       */
      $query_string = get_search_query();
      $paged        = max( 1, get_query_var( 'paged' ) );

      $args = [
        'post_type'      => 'any',
        'post_status'    => 'publish',
        's'              => $query_string,
        'paged'          => $paged,
        'posts_per_page' => get_option( 'posts_per_page' ),
      ];

      $the_query = new WP_Query( $args );
    ?>

    <h1 class="h2__heading">Search Results for: <?php echo esc_html( $query_string ); ?></h1>

    <?php if ( $the_query->have_posts() ) : ?>
      <div class="search__grid">
        <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
          <a class="search__container"
             href="<?php the_permalink(); ?>"
             title="<?php echo esc_attr( 'View ' . get_the_title() ); ?>">

            <header>
              <h3 class="h3__heading"><?php the_title(); ?></h3>

              <?php
                /**
                 * Built-in WordPress Categories (text only).
                 * Some post types may not have categories, so this may be empty.
                 */
                $cats = get_the_terms( get_the_ID(), 'category' );
                if ( ! is_wp_error( $cats ) && ! empty( $cats ) ) {
                  $cat_names = implode( ', ', wp_list_pluck( $cats, 'name' ) );
                  echo '<p class="search__category">' . esc_html( $cat_names ) . '</p>';
                }
              ?>
            </header>

            <p class="search__description">
              <?php
                /**
                 * Prefer excerpt if available; otherwise trim the content.
                 * (Keeps results consistent and avoids huge blocks of text.)
                 */
                if ( has_excerpt() ) {
                  echo esc_html( wp_trim_words( get_the_excerpt(), 25 ) );
                } else {
                  echo esc_html( wp_trim_words( wp_strip_all_tags( get_the_content() ), 25 ) );
                }
              ?>
            </p>
          </a>
        <?php endwhile; ?>
      </div>

      <?php
      /**
       * ============================================================
       * Pagination (matches your Resources pagination styles)
       * ============================================================
       */
      $total_pages = (int) $the_query->max_num_pages;

      if ( $total_pages > 1 ) {
        $links = paginate_links( [
          'base'      => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
          'format'    => '?paged=%#%',
          'current'   => $paged,
          'total'     => $total_pages,
          'mid_size'  => 1,
          'end_size'  => 1,
          'prev_next' => true,
          'prev_text' => '&lt; Previous',
          'next_text' => 'Next &gt;',
          'type'      => 'array',
        ] );

        if ( $links ) {
          echo '<nav class="pagination" aria-label="Search results pagination"><ul class="pagination__list">';
          foreach ( $links as $link ) {
            echo '<li class="pagination__item">' . $link . '</li>';
          }
          echo '</ul></nav>';
        }
      }

      wp_reset_postdata();
      ?>

    <?php else : ?>
      <div class="alert alert-info">
        <p>Sorry, but nothing matched your search criteria. Please try again with some different keywords.</p>
      </div>
    <?php endif; ?>

  </section>

  <?php
  /**
   * ============================================================
   * Resource Spotlight (ACF Options) — Sidebar ONLY
   * ============================================================
   *
   * This search page sidebar intentionally shows ONLY the curated Resource Spotlight list.
   *
   * ACF Options field group structure:
   * - Repeater: resource_spotlight_items (max 5)
   *   - spotlight_type      (radio: 'resource' | 'file')
   *   - spotlight_resource  (Post Object)  [shown when type = resource]
   *   - spotlight_file      (File array)   [shown when type = file]
   *   - spotlight_label     (Text)         [optional label override]
   */
  $spotlight_items = array();

  // Only attempt to read ACF fields if ACF is active and the repeater has rows.
  if ( function_exists( 'have_rows' ) && have_rows( 'resource_spotlight_items', 'option' ) ) {
    while ( have_rows( 'resource_spotlight_items', 'option' ) ) {
      the_row();

      $type  = (string) get_sub_field( 'spotlight_type' );
      $label = (string) get_sub_field( 'spotlight_label' );

      $url         = '';
      $final_label = '';
      $new_tab     = false;

      /**
       * Type: resource
       * - Links internally to the selected Resource post/page.
       */
      if ( $type === 'resource' ) {
        $post_obj = get_sub_field( 'spotlight_resource' );
        if ( $post_obj ) {
          $url         = get_permalink( $post_obj );
          $final_label = $label ? $label : get_the_title( $post_obj );
          $new_tab     = false;
        }
      }

      /**
       * Type: file
       * - Links directly to a file from the Media Library.
       * - Opens in a new tab (download behavior).
       */
      if ( $type === 'file' ) {
        $file = get_sub_field( 'spotlight_file' );
        if ( is_array( $file ) && ! empty( $file['url'] ) ) {
          $url = $file['url'];

          // Prefer: label override > file title > filename > URL basename
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

      // Only keep valid items.
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

      <p class="downloads__empty">There are no featured resources to show at this time.</p>

    <?php endif; ?>
  </aside>
</main>

<?php get_footer(); ?>