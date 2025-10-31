<?php
/**
 * The template for displaying a single Resource page
 *
 * @package bja-nsvsp
 */

get_header();
?>

<main id="single-page">
  <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

    <section class="single-resource">
      <h1 class="h1__heading"><?php the_title(); ?></h1>

      <?php
      // Built-in WordPress Categories (text only, no links)
      $cats = get_the_terms( get_the_ID(), 'category' );
      if ( ! is_wp_error( $cats ) && ! empty( $cats ) ) {
        $cat_names = implode( ', ', wp_list_pluck( $cats, 'name' ) );
        echo '<p class="single-resource__category">Category: ' . esc_html( $cat_names ) . '</p>';
      }
      ?>

      <p class="single-resource__date">Date Published: <?php echo esc_html( get_the_date( 'F j, Y' ) ); ?></p>

      <div class="single-resource__content">
        <?php
          the_content();
          wp_link_pages( [
            'before' => '<div class="page-links">',
            'after'  => '</div>',
          ] );
        ?>
      </div>

      <?php // Related FAQs by shared Category (matches front-page FAQ CSS)
      if ( get_post_type() === 'post' ) : ?>
        <div id="faqs" class="faqs related-faqs">
          <h2 class="h2__heading">Related FAQs</h2>

          <?php
          $cats    = get_the_terms( get_the_ID(), 'category' );
          $cat_ids = ( $cats && ! is_wp_error( $cats ) ) ? wp_list_pluck( $cats, 'term_id' ) : [];

          if ( ! empty( $cat_ids ) ) :
            $related_faqs = new WP_Query( [
              'post_type'      => 'faq',
              'posts_per_page' => 4,
              'post_status'    => 'publish',
              'tax_query'      => [[
                'taxonomy' => 'category',
                'field'    => 'term_id',
                'terms'    => $cat_ids,
              ]],
              'orderby'        => 'date',
              'order'          => 'DESC',
            ] );
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

    </section><!-- closes .single-resource (removed the extra one) -->

    <?php
      // ==== Collect this post's downloads BEFORE checking for fallback ====
      $downloads = [];
      if ( function_exists('get_field') ) {
        for ( $i = 1; $i <= 4; $i++ ) {
          $val = get_field( 'download_file_' . $i );
          if ( empty( $val ) ) continue;

          $url = '';
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

      $is_resource          = ( get_post_type() === 'post' );
      $show_recent_fallback = empty( $downloads );

      // ==== Build recent Resource downloads (top 5) only if needed ====
      $recent_downloads = [];
      if ( $show_recent_fallback ) {
        $recent_posts = new WP_Query([
          'post_type'      => ['post'], // Resources only
          'post_status'    => 'publish',
          'posts_per_page' => 40,       // search a batch to find 5 files
          'orderby'        => 'date',
          'order'          => 'DESC',
          'no_found_rows'  => true,
        ]);

        if ( $recent_posts->have_posts() ) {
          while ( $recent_posts->have_posts() ) {
            $recent_posts->the_post();
            $pid       = get_the_ID();
            $parent_ts = (int) get_post_time('U', true, $pid); // fallback timestamp

            for ( $i = 1; $i <= 4; $i++ ) {
              $val = function_exists('get_field') ? get_field('download_file_' . $i, $pid) : null;
              if ( empty($val) ) continue;

              $url = '';
              $label = '';
              $ts = $parent_ts;

              if ( is_array($val) ) {
                $url   = $val['url'] ?? '';
                $label = $val['title'] ?? ($val['filename'] ?? '');
                if ( !empty($val['ID']) && is_numeric($val['ID']) ) {
                  $att_id = (int) $val['ID'];
                  $ts     = (int) get_post_time('U', true, $att_id ) ?: $parent_ts;
                }
              } elseif ( is_numeric($val) ) {
                $att_id = (int) $val;
                $url    = wp_get_attachment_url($att_id) ?: '';
                $label  = get_the_title($att_id) ?: '';
                $ts     = (int) get_post_time('U', true, $att_id ) ?: $parent_ts;
              } elseif ( is_string($val) ) {
                $url   = $val;
                $label = basename( parse_url($url, PHP_URL_PATH) );
              }

              if ( $url ) {
                $recent_downloads[] = [
                  'url'      => $url,
                  'label'    => $label !== '' ? $label : basename( parse_url($url, PHP_URL_PATH) ),
                  'ts'       => $ts,
                  'post_id'  => $pid,
                  'post_ttl' => get_the_title($pid),
                ];
              }
            }
          }
          wp_reset_postdata();
        }

        // newest first and keep top 5
        usort($recent_downloads, function($a,$b){ return $b['ts'] <=> $a['ts']; });
        $recent_downloads = array_slice($recent_downloads, 0, 5);
      }
    ?>

    <aside class="downloads">
      <h4 class="h4__heading">
        <?php echo !empty($downloads) ? 'Downloads' : 'Our Newest Downloads'; ?>
      </h4>

      <?php if ( !empty($downloads) ) : ?>

        <?php foreach ( $downloads as $dl ) : ?>
          <a class="downloads__link" href="<?php echo esc_url( $dl['url'] ); ?>" target="_blank" rel="noopener">
            <?php echo function_exists( 'svg_icon' ) ? svg_icon( 'downloads__icon', 'download' ) : ''; ?>
            <?php echo esc_html( $dl['label'] ); ?>
          </a>
        <?php endforeach; ?>

      <?php elseif ( !empty($recent_downloads) ) : ?>

        <p class="downloads__empty">
          <?php echo $is_resource
            ? 'This resource has no downloads. Try these:'
            : 'Download our newest resources.'; ?>
        </p>

        <?php foreach ( $recent_downloads as $item ) : ?>
          <a class="downloads__link" href="<?php echo esc_url( $item['url'] ); ?>" target="_blank" rel="noopener"
             title="<?php echo esc_attr('From: ' . $item['post_ttl']); ?>">
            <?php echo function_exists( 'svg_icon' ) ? svg_icon( 'downloads__icon', 'download' ) : ''; ?>
            <?php echo esc_html( $item['label'] ); ?>
          </a>
        <?php endforeach; ?>

      <?php else : ?>

        <p class="downloads__empty">There are no downloads to show at this time.</p>

      <?php endif; ?>

      <a class="btn" href="#take-survey">Take Survey</a>
    </aside>

  <?php endwhile; endif; ?>
</main>

<?php get_footer(); ?>