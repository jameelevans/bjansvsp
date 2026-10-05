<?php
/**
 * Template Name: Glossary
 * Description: Lists all glossary terms grouped by category, with A–Z groups inside each category.
 *
 * @package bja-nsvsp
 */

get_header();
?>

<main id="single-page" class="site-main single-page" tabindex="-1">
  <div class="single-page__container">

    <?php if ( have_posts() ) : ?>
      <?php while ( have_posts() ) : the_post(); ?>

        <article id="post-<?php the_ID(); ?>" <?php post_class( 'single-page__main' ); ?>>
          <h1 class="h1__heading">
            <?php the_title(); ?>
          </h1>

          <div class="single-page__content">
            <?php the_content(); ?>
          </div>
        </article>

      <?php endwhile; ?>
    <?php endif; ?>

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
    <h2 class="h4__heading">Resource Spotlight</h2>

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