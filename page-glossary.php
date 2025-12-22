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