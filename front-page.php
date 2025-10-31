<?php
/**
 * * The template for displaying the front page
 *
 * @package your-wp-project
 */

get_header();

?>
	<main id="front-page">
		<div class="main-content">
			<section id="take-survey">
				<h1 class="h1__heading"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></h1>
				<p class="sub__heading"><?php
				$home_id = get_option('page_on_front');
				$home_content = get_post_field('post_content', $home_id);
				echo esc_html( wp_strip_all_tags( $home_content ) );
				?></p>
				<a class="btn" href="#">Take Survey</a>
			</section>
			<section id="resources">
				<h2 class="h2__heading">Resources</h2>
				<p class="sub__heading"><?php echo esc_html( get_field('resource_subheading', get_queried_object_id()) ); ?></p>

				<div class="resources__container">
					<?php
					// Pagination-aware query (works on static Front Page too)
					$paged = max( 1, get_query_var('paged') ? : get_query_var('page') );

					$resources_query = new WP_Query([
					'post_type'      => 'post',   // your renamed "Resources"
					'posts_per_page' => 6,
					'paged'          => $paged,
					]);

					if ( $resources_query->have_posts() ) :
					while ( $resources_query->have_posts() ) : $resources_query->the_post(); ?>
						<div class="resource">
						<h3 class="h3__heading"><?php the_title(); ?></h3>

						<?php
						// Optional: first category as "Resource Type"
						$categories = get_the_category();
						if ( ! empty( $categories ) ) :
							echo '<p class="resource__category">' . esc_html( $categories[0]->name ) . '</p>';
						endif;
						?>

						<p class="resource__description">
							<?php echo esc_html( wp_trim_words( get_the_content(), 20, '…' ) ); ?>
						</p>

						<a href="<?php the_permalink(); ?>" class="resource__btn">Learn More</a>
						</div>
					<?php endwhile;

					// Build numbered pagination
					$links = paginate_links([
						'base'      => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
						'format'    => '?paged=%#%',
						'current'   => $paged,
						'total'     => $resources_query->max_num_pages,
						'mid_size'  => 1,
						'end_size'  => 1,
						'prev_next' => true,
						'prev_text' => '&lt; Previous', // <Previous
						'next_text' => 'Next &gt;',     // Next>
						'type'      => 'array',
											]);

					if ( $links ) {
						echo '<nav class="pagination" aria-label="Resources pagination"><ul class="pagination__list">';
						foreach ( $links as $link ) {
						echo '<li class="pagination__item">' . $link . '</li>';
						}
						echo '</ul></nav>';
					}

					wp_reset_postdata();

					else : ?>
					<p>No resources available at this time.</p>
					<?php endif; ?>
				</div>
			</section>
			<section id="dictionary">
				<h2 class="h2__heading">Dictionary</h2>
				<p class="sub__heading"><?php echo esc_html( get_field('dictionary_subheading', get_queried_object_id()) ); ?></p>
				<p class="sub__heading">Find a topic by its first letter:</p>

				<?php
					// 1) Fetch all dictionary terms (published), sorted A→Z
					$dict_posts = get_posts([
					'post_type'      => 'dictionary',   // CPT slug
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'orderby'        => 'title',
					'order'          => 'ASC',
					'suppress_filters' => false,
					]);

					// 2) Group by first letter
					$groups = [];
					foreach ($dict_posts as $p) {
					$letter = strtoupper( mb_substr( $p->post_title, 0, 1, 'UTF-8' ) );
					$letter = ctype_alpha($letter) ? $letter : '#'; // non-letters bucket
					$groups[$letter][] = $p;
					}

					// 3) Letter bar (A–Z) without counts
					echo '<div class="dict__letters">';
					foreach (range('A','Z') as $L) {
					$has_terms = !empty($groups[$L]);
					$href = $has_terms ? '#dict-' . $L : '#';
					$cls = 'dict__letter' . ($has_terms ? '' : ' is-empty');
					echo '<a class="' . esc_attr($cls) . '" href="' . esc_url($href) . '" aria-disabled="' . ($has_terms ? 'false' : 'true') . '">';
					echo esc_html($L);
					echo '</a>';
					}
					echo '</div>';

					// 4) Output groups with headings and term items
					foreach (range('A','Z') as $L) {
					if ( empty($groups[$L]) ) continue;

					$items = $groups[$L];
					echo '<section id="dict-'. esc_attr($L) .'" class="dict__group">';
					echo '<h3 class="h3__heading">'. esc_html($L) .' (<span class="dict__total">'. count($items) .'</span> Total)</h3>';

					foreach ($items as $p) {
						$title = get_the_title($p);
						$desc  = has_excerpt($p) ? get_the_excerpt($p) : wp_trim_words( wp_strip_all_tags( $p->post_content ), 60, '…' );

						echo '<article class="dict__item">';
						echo   '<h4 class="h4__heading--orange"><span class="dict__chip">'. esc_html($title) .':</span></h4>';
						echo   '<p class="dict__desc">'. esc_html($desc) .'</p>';
						echo '</article>';
					}

					echo '</section>';
					}

					// Optional: non-letter bucket (#)
					if ( !empty($groups['#']) ) {
					$items = $groups['#'];
					echo '<section id="dict-nonalpha" class="dict__group">';
					echo '<h3 class="dict__group-title"># (<span class="dict__total">'. count($items) .'</span> Total)</h3>';
					foreach ($items as $p) {
						$title = get_the_title($p);
						$desc  = has_excerpt($p) ? get_the_excerpt($p) : wp_trim_words( wp_strip_all_tags( $p->post_content ), 60, '…' );
						echo '<article class="dict__item">';
						echo   '<h4 class="dict__term"><span class="dict__chip">'. esc_html($title) .'</span></h4>';
						echo   '<p class="dict__desc">'. esc_html($desc) .'</p>';
						echo '</article>';
					}
					echo '</section>';
					}
					?>
			</section>
			<section id="faqs">
				<h2 class="h2__heading">FAQs</h2>
				<p class="sub__heading"><?php echo esc_html( get_field('faqs_subheading', get_queried_object_id()) ); ?></p>
				
				<?php
					// FAQs loop (ordered by Menu Order, then date)
					$faqs = new WP_Query([
					'post_type'      => 'faq',
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'orderby'        => ['menu_order' => 'ASC', 'date' => 'DESC'],
					'no_found_rows'  => true, // perf: no pagination needed
					]);

					if ($faqs->have_posts()) :
					$i = 0;
					echo '<div class="faqs__accordion" role="region" aria-label="FAQs">';
					while ($faqs->have_posts()) : $faqs->the_post();
						$i++;
						$panel_id = 'faq-' . get_the_ID();
						?>
						<details class="faq" <?php if ($i === 1) echo 'open'; // first one open ?>>
						<summary class="faq__question">
							<span class="faq__q-text"><?php the_title(); ?></span>
							<span class="faq__icon" aria-hidden="true"></span>
						</summary>
						<div class="faq__answer" id="<?php echo esc_attr($panel_id); ?>">
							<?php echo wpautop( wp_kses_post( get_the_content() ) ); ?>
						</div>
						</details>
						<?php
					endwhile;
					echo '</div>';
					wp_reset_postdata();
					else :
					echo '<p>No FAQs available at this time.</p>';
					endif;
					?>

			</section>
			<section id="contact-us"></section>
		</div>
		<aside class="side-nav">
			<h4 class="h4__heading">On this page</h4>
			<ul class="side-nav__list">
				<li class="side-nav__item"><a class="side-nav__link" href="#take-survey">Take Survey</a></li>
				<li class="side-nav__item"><a class="side-nav__link" href="#resources">Resources</a></li>
				<li class="side-nav__item"><a class="side-nav__link" href="#dictionary">Dictionary</a></li>
				<li class="side-nav__item"><a class="side-nav__link" href="#faqs">FAQs</a></li>
			</ul>
			<form class="aside-search" role="aside-search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<input type="search" name="s" placeholder="Search" aria-label="Search">
				<button type="submit" aria-label="Submit search">
					<span class="screen-reader-text">Search</span>
				</button>
			</form>
			<a class="btn" href="#">Take Survey</a>
		</aside>
	</main>
<?php get_footer(); ?>
