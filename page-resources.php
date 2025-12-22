<?php
/**
 * Template Name: Resources
 * Description: Lists all Resources grouped by category.
 *
 * @package bja-nsvsp
 */

get_header();
?>

<main id="single-page" class="site-main single-page">
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
                      Learn More
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
    // Build a global "Our Newest Downloads" list (top 5 across Resources).
    $recent_downloads = [];

    if ( function_exists( 'get_field' ) ) {
      $recent_posts = new WP_Query( [
        'post_type'      => [ 'post' ], // Resources only
        'post_status'    => 'publish',
        'posts_per_page' => 40,         // search a batch to find up to 5 files
        'orderby'        => 'date',
        'order'          => 'DESC',
        'no_found_rows'  => true,
      ] );

      if ( $recent_posts->have_posts() ) {
        while ( $recent_posts->have_posts() ) {
          $recent_posts->the_post();
          $pid       = get_the_ID();
          $parent_ts = (int) get_post_time( 'U', true, $pid ); // fallback timestamp

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
              $recent_downloads[] = [
                'url'      => $url,
                'label'    => $label !== '' ? $label : basename( parse_url( $url, PHP_URL_PATH ) ),
                'ts'       => $ts,
                'post_id'  => $pid,
                'post_ttl' => get_the_title( $pid ),
              ];
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