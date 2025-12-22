<?php
/**
 * Template Name: Glossary
 * Description: Lists all glossary terms grouped by category, with A–Z groups inside each category.
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
      <section id="glossary">
        <?php
        // Optional Glossary subheading from ACF on this page.
        $glossary_subheading = function_exists( 'get_field' ) ? get_field( 'glossary_subheading', get_queried_object_id() ) : '';
        if ( ! empty( $glossary_subheading ) ) :
          ?>
          <p class="sub__heading">
            <?php echo esc_html( $glossary_subheading ); ?>
          </p>
        <?php endif; ?>

        <p class="sub__heading">Browse glossary terms by category and first letter:</p>

        <?php
        // ========= 1) TRY CATEGORY-GROUPED VIEW =========
        $gloss_tax = 'category';
        $terms     = get_terms(
          array(
            'taxonomy'   => $gloss_tax,
            'hide_empty' => true, // may count other post types too; we still filter by glossary below.
            'orderby'    => 'name',
            'order'      => 'ASC',
          )
        );

        $has_category_output = false;

        if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) :

          foreach ( $terms as $term ) {

            // Fetch ONLY glossary posts in this category.
            $gloss_posts = get_posts(
              array(
                'post_type'        => 'glossary',
                'post_status'      => 'publish',
                'posts_per_page'   => -1,
                'orderby'          => 'title',
                'order'            => 'ASC',
                'tax_query'        => array(
                  array(
                    'taxonomy' => $gloss_tax,
                    'field'    => 'term_id',
                    'terms'    => $term->term_id,
                  ),
                ),
                'suppress_filters' => false,
              )
            );

            if ( empty( $gloss_posts ) ) {
              continue; // no glossary terms in this category – skip it.
            }

            $has_category_output = true;

            // Group this category's glossary posts by first letter.
            $groups = array();
            foreach ( $gloss_posts as $p ) {
              $letter = strtoupper( mb_substr( $p->post_title, 0, 1, 'UTF-8' ) );
              $letter = ctype_alpha( $letter ) ? $letter : '#'; // non-letters bucket.
              $groups[ $letter ][] = $p;
            }

            // ID prefix for anchors inside this category (avoid duplicate IDs).
            $id_prefix = 'glossary-' . sanitize_html_class( $term->slug );
            ?>
            <section class="glossary-category">
              <h2 id="<?php echo esc_attr( $term->slug ); ?>" class="h2__heading mt-xl">
                <?php echo esc_html( $term->name ); ?>
              </h2>

              <?php
              // Category description or generic prompt if blank.
              if ( ! empty( $term->description ) ) :
                ?>
                <p class="sub__heading">
                  <?php echo esc_html( $term->description ); ?>
                </p>
              <?php else : ?>
                <p class="sub__heading">
                  Please add a Category Description to show here.
                </p>
              <?php endif; ?>

              <?php
              // Letter bar for this category.
              echo '<div class="glossary__letters">';
              foreach ( range( 'A', 'Z' ) as $L ) {
                $has_terms = ! empty( $groups[ $L ] );
                $href      = $has_terms ? '#' . $id_prefix . '-' . $L : '#';
                $cls       = 'glossary__letter' . ( $has_terms ? '' : ' is-empty' );

                echo '<a class="' . esc_attr( $cls ) . '" href="' . esc_url( $href ) . '" aria-disabled="' . ( $has_terms ? 'false' : 'true' ) . '">';
                echo esc_html( $L );
                echo '</a>';
              }
              echo '</div>';

              // Output A–Z groups for this category.
              foreach ( range( 'A', 'Z' ) as $L ) {
                if ( empty( $groups[ $L ] ) ) {
                  continue;
                }

                $items = $groups[ $L ];
                ?>
                <section id="<?php echo esc_attr( $id_prefix . '-' . $L ); ?>" class="glossary__group">
                  <h3 class="h3__heading--orange mt-xl">
                    <?php echo esc_html( $L ); ?>
                  </h3>

                  <?php
                  foreach ( $items as $p ) {
                    $title = get_the_title( $p );
                    $desc  = has_excerpt( $p )
                      ? get_the_excerpt( $p )
                      : wp_trim_words( wp_strip_all_tags( $p->post_content ), 60, '…' );
                    ?>
                    <article class="glossary__item">
                      <h4 class="glossary__heading">
                        <?php echo esc_html( $title ); ?>
                      </h4>
                      <p class="glossary__desc">
                        <?php echo esc_html( $desc ); ?>
                      </p>
                    </article>
                    <?php
                  }
                  ?>
                </section>
                <?php
              }

              // Optional non-letter bucket (#) for this category.
              if ( ! empty( $groups['#'] ) ) {
                $items = $groups['#'];
                ?>
                <section id="<?php echo esc_attr( $id_prefix . '-nonalpha' ); ?>" class="glossary__group">
                  <h3 class="h3__heading--orange mt-xl">
                    #
                    (<span class="glossary__total"><?php echo count( $items ); ?></span> Total)
                  </h3>
                  <?php
                  foreach ( $items as $p ) {
                    $title = get_the_title( $p );
                    $desc  = has_excerpt( $p )
                      ? get_the_excerpt( $p )
                      : wp_trim_words( wp_strip_all_tags( $p->post_content ), 60, '…' );
                    ?>
                    <article class="glossary__item">
                      <h4 class="glossary__heading">
                        <?php echo esc_html( $title ); ?>
                      </h4>
                      <p class="glossary__desc">
                        <?php echo esc_html( $desc ); ?>
                      </p>
                    </article>
                    <?php
                  }
                  ?>
                </section>
                <?php
              }
              ?>
            </section>
            <?php
          } // end foreach $terms
        endif;

        // ========= 2) FALLBACK: ORIGINAL GLOBAL A–Z VIEW =========
        if ( ! $has_category_output ) :
          ?>
          <p class="sub__heading">Find a topic by its first letter:</p>
          <?php
          // Fetch all glossary terms (published), sorted A→Z (your original logic).
          $dict_posts = get_posts(
            array(
              'post_type'        => 'glossary',   // Glossary CPT slug.
              'post_status'      => 'publish',
              'posts_per_page'   => -1,
              'orderby'          => 'title',
              'order'            => 'ASC',
              'suppress_filters' => false,
            )
          );

          // Group by first letter.
          $groups = array();
          foreach ( $dict_posts as $p ) {
            $letter = strtoupper( mb_substr( $p->post_title, 0, 1, 'UTF-8' ) );
            $letter = ctype_alpha( $letter ) ? $letter : '#'; // non-letters bucket.
            $groups[ $letter ][] = $p;
          }

          // Letter bar (A–Z) without counts.
          echo '<div class="glossary__letters">';
          foreach ( range( 'A', 'Z' ) as $L ) {
            $has_terms = ! empty( $groups[ $L ] );
            $href      = $has_terms ? '#glossary-' . $L : '#';
            $cls       = 'glossary__letter' . ( $has_terms ? '' : ' is-empty' );
            echo '<a class="' . esc_attr( $cls ) . '" href="' . esc_url( $href ) . '" aria-disabled="' . ( $has_terms ? 'false' : 'true' ) . '">';
            echo esc_html( $L );
            echo '</a>';
          }
          echo '</div>';

          // Output groups with headings and term items.
          foreach ( range( 'A', 'Z' ) as $L ) {
            if ( empty( $groups[ $L ] ) ) {
              continue;
            }

            $items = $groups[ $L ];
            echo '<section id="glossary-' . esc_attr( $L ) . '" class="glossary__group">';
            echo '<h3 class="h3__heading--orange mt-xl">' . esc_html( $L ) . ' (<span class="glossary__total">' . count( $items ) . '</span> Total)</h3>';

            foreach ( $items as $p ) {
              $title = get_the_title( $p );
              $desc  = has_excerpt( $p )
                ? get_the_excerpt( $p )
                : wp_trim_words( wp_strip_all_tags( $p->post_content ), 60, '…' );

              echo '<article class="glossary__item">';
              echo '<h4 class="glossary__heading">' . esc_html( $title ) . '</h4>';
              echo '<p class="glossary__desc">' . esc_html( $desc ) . '</p>';
              echo '</article>';
            }

            echo '</section>';
          }

          // Optional: non-letter bucket (#).
          if ( ! empty( $groups['#'] ) ) {
            $items = $groups['#'];
            echo '<section id="glossary-nonalpha" class="glossary__group">';
            echo '<h3 class="h3__heading--orange mt-xl"># (<span class="glossary__total">' . count( $items ) . '</span> Total)</h3>';
            foreach ( $items as $p ) {
              $title = get_the_title( $p );
              $desc  = has_excerpt( $p )
                ? get_the_excerpt( $p )
                : wp_trim_words( wp_strip_all_tags( $p->post_content ), 60, '…' );
              echo '<article class="glossary__item">';
              echo '<h4 class="glossary__heading">' . esc_html( $title ) . '</h4>';
              echo '<p class="glossary__desc">' . esc_html( $desc ) . '</p>';
              echo '</article>';
            }
            echo '</section>';
          }
        endif; // end fallback
        ?>
      </section>
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