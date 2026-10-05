<?php
/**
 * The template for displaying the header
 *
 * @package bja-nsvsp
 */
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="screen-reader-shortcut" href="#<?php echo is_front_page() ? 'front-page' : (is_search() ? 'search-page' : ((is_archive() || is_home() || is_404()) ? 'main-content' : 'single-page')); ?>">Skip to main content</a>

<header id="top" class="header home-header header--new-design" role="banner">
	<div class="header__container">
		<?php if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) : ?>
		<?php the_custom_logo(); ?>
		<?php else : ?>
		<a class="header__logo-link" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<img class="header__logo" src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/DOJ-OJP-BJS-NSVSP-Logo.webp' ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" width="1100" height="376" decoding="async" fetchpriority="high">
		</a>
		<?php endif; ?>

	</div>
</header>

<?php
/**
 * ============================================================
 * Desktop current-page helpers
 * ============================================================
 *
 * These booleans drive DESKTOP nav styling only.
 *
 * Resources should be active for:
 * - /resources page
 * - single Resource posts (standard WP posts)
 * - category archives (if used)
 *
 * Glossary active for:
 * - /glossary page
 *
 * FAQs active for:
 * - /faqs page
 * - single FAQ posts (faq CPT)
 */
$is_resources = is_page( 'resources' ) || is_singular( 'post' ) || is_category();
$is_glossary  = is_page( 'glossary' );
$is_faqs      = is_page( 'faqs' ) || is_singular( 'faq' );
?>

<div class="navbar">
	<div class="navbar__container">
		<nav class="nav" role="navigation" aria-label="Primary">
			<ul class="nav__menu">
				<li class="nav__li nav__survey">
					<a class="nav__item<?php echo is_front_page() ? ' current-page' : ''; ?>"
					href="https://www.icfsurvey2.com/NSVSP" target="_blank" rel="noopener noreferrer">
					Take Survey<span class="screen-reader-text"> (opens in a new tab)</span>
					</a>
				</li>

				<li class="nav__li nav__has-dropdown">
					<a
						<?php
						/**
						 * DESKTOP: Resources current-page styling
						 */
						?>
						class="nav__item<?php echo $is_resources ? ' is-active' : ''; ?>"
						id="nav-resources"
						href="<?php echo is_front_page() ? '#resources' : esc_url( home_url( '/resources' ) ); ?>"
						<?php echo $is_resources ? ' aria-current="page"' : ''; ?>
						aria-haspopup="true"
						aria-expanded="false"
						aria-controls="resources-submenu"
					>
						Resources

					</a>

					<?php
					// Resources dropdown: list all post categories (hide empty)
					$resource_cats = get_categories([
						'taxonomy'   => 'category',
						'hide_empty' => true,
						'orderby'    => 'name',
						'order'      => 'ASC',
					]);
					if ( ! empty( $resource_cats ) ) : ?>
						<ul class="nav-dropdown" id="resources-submenu" aria-labelledby="nav-resources">
							<?php
							// Get the Resources page URL (by slug "resources")
							$resources_page = get_page_by_path( 'resources' );
							$resources_url  = $resources_page ? get_permalink( $resources_page ) : home_url( '/resources/' );
							// remove any trailing slash so we get /resources#slug instead of /resources/#slug
						$resources_url  = rtrim( $resources_url, '/' );

							foreach ( $resource_cats as $cat ) :
								// Use the category slug as the anchor (e.g., #best-category)
								$anchor = $cat->slug;
								?>
								<li class="nav-dropdown__item">
									<a

										href="<?php echo esc_url( $resources_url . '#' . $anchor ); ?>"
										class="nav-dropdown__link"
									>
										<?php echo esc_html( $cat->name ); ?>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
					</li>

				<li class="nav__li nav__has-dropdown">
					<a
						<?php
						/**
						 * DESKTOP: Glossary current-page styling
						 */
						?>
						class="nav__item<?php echo $is_glossary ? ' is-active' : ''; ?>"
						id="nav-glossary"
						href="<?php echo is_front_page() ? '#glossary' : esc_url( home_url( '/glossary' ) ); ?>"
						<?php echo $is_glossary ? ' aria-current="page"' : ''; ?>
						aria-haspopup="true"
						aria-expanded="false"
						aria-controls="glossary-submenu"
					>
						Glossary

					</a>
					<?php
					// Prefer a dedicated glossary taxonomy if available; otherwise use default categories.
					$glossary_tax = taxonomy_exists('glossary_category') ? 'glossary_category' : 'category';
					$glossary_terms = get_terms([
						'taxonomy'   => $glossary_tax,
						'hide_empty' => true,
						'orderby'    => 'name',
						'order'      => 'ASC',
					]);

					if ( ! is_wp_error( $glossary_terms ) && ! empty( $glossary_terms ) ) :

						// Get the Glossary page URL (by slug "glossary")
						$glossary_page = get_page_by_path( 'glossary' );
						$glossary_url  = $glossary_page ? get_permalink( $glossary_page ) : home_url( '/glossary/' );
						// remove any trailing slash so we get /glossary#slug instead of /glossary/#slug
						$glossary_url  = rtrim( $glossary_url, '/' );
						?>
						<ul class="nav-dropdown" id="glossary-submenu" aria-labelledby="nav-glossary">
							<?php foreach ( $glossary_terms as $term ) : ?>
								<?php $anchor = $term->slug; ?>
								<li class="nav-dropdown__item">
									<a

										href="<?php echo esc_url( $glossary_url . '#' . $anchor ); ?>"
										class="nav-dropdown__link"
									>
										<?php echo esc_html( $term->name ); ?>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</li>

				<li class="nav__li nav__has-dropdown">
					<a
						<?php
						/**
						 * DESKTOP: FAQs current-page styling
						 */
						?>
						class="nav__item<?php echo $is_faqs ? ' is-active' : ''; ?>"
						id="nav-faqs"
						href="<?php echo is_front_page() ? '#faqs' : esc_url( home_url( '/faqs' ) ); ?>"
						<?php echo $is_faqs ? ' aria-current="page"' : ''; ?>
						aria-haspopup="true"
						aria-expanded="false"
						aria-controls="faqs-submenu"
					>
						FAQs

					</a>

					<?php
					// Prefer a dedicated FAQs taxonomy if available; otherwise use default categories.
					$faqs_tax = taxonomy_exists('faq_category') ? 'faq_category' : 'category';

					$faqs_terms = get_terms([
						'taxonomy'   => $faqs_tax,
						'hide_empty' => true,
						'orderby'    => 'name',
						'order'      => 'ASC',
					]);

					if ( ! is_wp_error( $faqs_terms ) && ! empty( $faqs_terms ) ) :

						// Get the FAQs page URL (by slug "faqs")
						$faqs_page = get_page_by_path( 'faqs' );
						$faqs_url  = $faqs_page ? get_permalink( $faqs_page ) : home_url( '/faqs' );
						// remove any trailing slash
						$faqs_url  = rtrim( $faqs_url, '/' );
						?>
						<ul class="nav-dropdown" id="faqs-submenu" aria-labelledby="nav-faqs">
							<?php foreach ( $faqs_terms as $term ) : ?>
								<?php $anchor = $term->slug; ?>
								<li class="nav-dropdown__item">
									<a

										href="<?php echo esc_url( $faqs_url . '#' . $anchor ); ?>"
										class="nav-dropdown__link"
									>
										<?php echo esc_html( $term->name ); ?>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</li>

				<li class="nav__li">
					<a class="nav__item"
					href="<?php echo is_front_page() ? '#contact-us' : esc_url( home_url( '/#contact-us' ) ); ?>">
					Contact Us
					</a>
				</li>
				</ul>
		</nav>

		<form class="search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">

			<input type="search" name="s" placeholder="Search" aria-label="Search">
			<button type="submit" aria-label="Submit search">
				<?php echo svg_icon('search__icon', 'search'); ?><span class="screen-reader-text">Search</span>
			</button>
		</form>
	</div>
</div>

<!-- Mobile navigation -->
<div class="mobile-navigation">
	<!-- Hidden menu label for accessibility-->
	<span hidden="" id="mobile-menu">Main menu</span>

	<button type="button" class="mobile-navigation__menu" aria-controls="mobile-navigation" aria-expanded="false" aria-labelledby="mobile-menu">

		<!-- navigation menu icon-->
		<span class="mobile-navigation__menu-icon" aria-hidden="true"></span>
	</button>


	<div id="mobile-navigation" class="mobile-navigation__nav" role="dialog" aria-modal="true" aria-labelledby="mobile-menu" aria-hidden="true" tabindex="-1" inert>
		<div class="mobile-navigation__controls">
			<!-- Close (X) - visible on main menu -->
			<button class="mobile-navigation__close" type="button" aria-label="Close menu">
				<?php echo svg_icon('mobile-navigation__x', 'times'); ?>
			</button>

			<!-- Back - visible when submenu is open -->
			<button class="mobile-navigation__back" type="button" aria-label="Back to main menu" aria-hidden="true">
				<?php echo svg_icon('mobile-navigation__chevron-left', 'chevron-left'); ?>
				<span class="screen-reader-text">Back</span>
			</button>
		</div>

		<nav aria-label="Mobile">
		<ul class="mobile-navigation__list">
			<li class="mobile-navigation__item">
				<a href="<?php echo esc_url( home_url() ); ?>"
				class="mobile-navigation__link" title="Home">
				Home
				</a>
			</li>
			<li class="mobile-navigation__item">
				<a href="https://www.icfsurvey2.com/NSVSP" target="_blank" rel="noopener noreferrer"
				class="mobile-navigation__link" title="Take the NSVSP survey (opens in a new tab)">
				Take Survey<span class="screen-reader-text"> (opens in a new tab)</span>
				</a>
			</li>

			<?php
			/**
			 * MOBILE NAV NOTE:
			 * We intentionally do NOT add current-page styling (no is-active / aria-current)
			 * on mobile links per your requirement.
			 */
			?>

			<li class="mobile-navigation__item mobile-navigation__item--has-submenu">
				<div class="mobile-navigation__row">
					<a class="mobile-navigation__link"
					href="<?php echo esc_url( home_url( '/resources' ) ); ?>">
					Resources
					</a>

					<button class="mobile-navigation__submenu-toggle"
							type="button"
							aria-label="Open Resources categories"
							aria-expanded="false"
							aria-controls="mobile-submenu-resources" data-submenu="mobile-submenu-resources">
					<?php echo svg_icon('mobile-navigation__chevron', 'chevron-right'); ?>
					</button>
				</div>
			</li>
			<li class="mobile-navigation__item mobile-navigation__item--has-submenu">
				<div class="mobile-navigation__row">
					<a class="mobile-navigation__link"
					href="<?php echo esc_url( home_url( '/glossary' ) ); ?>">
					Glossary
					</a>

					<button class="mobile-navigation__submenu-toggle"
							type="button"
							aria-label="Open Glossary categories"
							aria-expanded="false"
							aria-controls="mobile-submenu-glossary" data-submenu="mobile-submenu-glossary">
					<?php echo svg_icon('mobile-navigation__chevron', 'chevron-right'); ?>
					</button>
				</div>
			</li>
			<li class="mobile-navigation__item mobile-navigation__item--has-submenu">
				<div class="mobile-navigation__row">
					<a class="mobile-navigation__link"
					href="<?php echo esc_url( home_url( '/faqs' ) ); ?>">
					FAQs
					</a>

					<button class="mobile-navigation__submenu-toggle"
							type="button"
							aria-label="Open FAQs categories"
							aria-expanded="false"
							aria-controls="mobile-submenu-faqs" data-submenu="mobile-submenu-faqs">
					<?php echo svg_icon('mobile-navigation__chevron', 'chevron-right'); ?>
					</button>
				</div>
			</li>
			<li class="mobile-navigation__item">
				<a href="<?php echo is_front_page() ? '#contact-us' : esc_url( home_url( '/#contact-us' ) ); ?>"
				class="mobile-navigation__link" title="Go to the Contact Us section">
				Contact Us
				</a>
			</li>

			<li class="mobile-navigation__item">
				<form class="mobile-navigation__search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">

				<input type="search" name="s" placeholder="Search" aria-label="Search">
				<button type="submit" aria-label="Submit search">
					<?php echo svg_icon('search__icon', 'search'); ?><span class="screen-reader-text">Search</span>
				</button>
				</form>
			</li>
			</ul>

		<div class="mobile-navigation__submenus" aria-hidden="true">
			<?php
			// Resources submenu items
			$resources_page = get_page_by_path( 'resources' );
			$resources_url  = $resources_page ? get_permalink( $resources_page ) : home_url( '/resources/' );
			$resources_url  = rtrim( $resources_url, '/' );

			$resource_cats = get_categories([
				'taxonomy'   => 'category',
				'hide_empty' => true,
				'orderby'    => 'name',
				'order'      => 'ASC',
			]);

			// Glossary submenu items
			$glossary_tax   = taxonomy_exists('glossary_category') ? 'glossary_category' : 'category';
			$glossary_terms = get_terms([
				'taxonomy'   => $glossary_tax,
				'hide_empty' => true,
				'orderby'    => 'name',
				'order'      => 'ASC',
			]);
			$glossary_page = get_page_by_path( 'glossary' );
			$glossary_url  = $glossary_page ? get_permalink( $glossary_page ) : home_url( '/glossary/' );
			$glossary_url  = rtrim( $glossary_url, '/' );

			// FAQs submenu items
			$faqs_tax   = taxonomy_exists('faq_category') ? 'faq_category' : 'category';
			$faqs_terms = get_terms([
				'taxonomy'   => $faqs_tax,
				'hide_empty' => true,
				'orderby'    => 'name',
				'order'      => 'ASC',
			]);
			$faqs_page = get_page_by_path( 'faqs' );
			$faqs_url  = $faqs_page ? get_permalink( $faqs_page ) : home_url( '/faqs' );
			$faqs_url  = rtrim( $faqs_url, '/' );
			?>

			<!-- Resources submenu -->
			<ul class="mobile-navigation__submenu" id="mobile-submenu-resources" aria-hidden="true">
				<li class="mobile-navigation__submenu-item">
				<a class="mobile-navigation__sublink" href="<?php echo esc_url( $resources_url ); ?>">
					All Resources
				</a>
				</li>
				<?php foreach ( $resource_cats as $cat ) : ?>
				<li class="mobile-navigation__submenu-item">
					<a class="mobile-navigation__sublink" href="<?php echo esc_url( $resources_url . '#' . $cat->slug ); ?>">
					<?php echo esc_html( $cat->name ); ?>
					</a>
				</li>
				<?php endforeach; ?>
			</ul>

			<!-- Glossary submenu -->
			<?php if ( ! is_wp_error( $glossary_terms ) ) : ?>
				<ul class="mobile-navigation__submenu" id="mobile-submenu-glossary" aria-hidden="true">
				<li class="mobile-navigation__submenu-item">
					<a class="mobile-navigation__sublink" href="<?php echo esc_url( $glossary_url ); ?>">
					All Glossary
					</a>
				</li>
				<?php foreach ( $glossary_terms as $term ) : ?>
					<li class="mobile-navigation__submenu-item">
					<a class="mobile-navigation__sublink" href="<?php echo esc_url( $glossary_url . '#' . $term->slug ); ?>">
						<?php echo esc_html( $term->name ); ?>
					</a>
					</li>
				<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<!-- FAQs submenu -->
			<?php if ( ! is_wp_error( $faqs_terms ) ) : ?>
				<ul class="mobile-navigation__submenu" id="mobile-submenu-faqs" aria-hidden="true">
				<li class="mobile-navigation__submenu-item">
					<a class="mobile-navigation__sublink" href="<?php echo esc_url( $faqs_url ); ?>">
					All FAQs
					</a>
				</li>
				<?php foreach ( $faqs_terms as $term ) : ?>
					<li class="mobile-navigation__submenu-item">
					<a class="mobile-navigation__sublink" href="<?php echo esc_url( $faqs_url . '#' . $term->slug ); ?>">
						<?php echo esc_html( $term->name ); ?>
					</a>
					</li>
				<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</nav>
	</div>
</div>
