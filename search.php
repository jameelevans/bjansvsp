<?php get_header(); ?>

<main id="search-page">
  <section class="search">

    <?php
      $query_string = get_search_query();
      $paged        = max( 1, get_query_var('paged') );

      // Search ALL public post types (Posts, Pages, CPTs)
      $args = [
        'post_type'      => 'any',
        'post_status'    => 'publish',
        's'              => $query_string,
        'paged'          => $paged,
        'posts_per_page' => get_option('posts_per_page'), // use WP setting
      ];
      $the_query = new WP_Query($args);
    ?>

    <h1 class="h2__heading">Search Results for: <?php echo esc_html($query_string); ?></h1>

    <?php if ( $the_query->have_posts() ) : ?>
      <div class="search__grid">
        <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
          <a class="search__container" href="<?php the_permalink(); ?>" title="<?php echo esc_attr( 'View ' . get_the_title() ); ?>">
            <header>
              <h3 class="h3__heading"><?php the_title(); ?></h3>
              <?php
                // Built-in WordPress Categories (text only, may be empty on some CPTs)
                $cats = get_the_terms( get_the_ID(), 'category' );
                if ( ! is_wp_error( $cats ) && ! empty( $cats ) ) {
                  $cat_names = implode( ', ', wp_list_pluck( $cats, 'name' ) );
                  echo '<p class="search__category">' . esc_html( $cat_names ) . '</p>';
                }
              ?>
            </header>

            <p class="search__description">
              <?php
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
      // Pagination (matches your Resources pagination styles)
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
          'type'      => 'array', // IMPORTANT: get an array so we can wrap li like the front page
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
</main>

<?php get_footer(); ?>