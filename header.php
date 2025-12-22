<?php
/**
 * Header template for the BJA NSVSP theme
 *
 * This file handles:
 * - Site logo / branding
 * - Desktop navigation with dropdown menus
 * - Desktop search
 * - Mobile navigation with slide-in submenus
 *
 * CURRENT PAGE STYLING OVERVIEW
 * -----------------------------
 * We apply active styling using:
 * - CSS class: `is-active`
 * - Accessibility attribute: aria-current="page"
 *
 * IMPORTANT RULES:
 * - Only REAL pages / archives get "current" styling
 * - Anchor links (#take-survey, #contact-us) are NOT marked active
 *   because PHP cannot detect scroll position
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

<!-- Skip link for keyboard + screen reader users -->
<a class="screen-reader-shortcut" href="#front-page">Skip to main content</a>

<header id="top" class="header home-header" role="banner">
	<div class="header__container">

		<?php
		/**
		 * LOGO OUTPUT
		 * -----------
		 * Use WordPress Custom Logo if available.
		 * Fall back to static theme image otherwise.
		 */
		?>
		<?php if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) : ?>
			<?php the_custom_logo(); ?>
		<?php else : ?>
			<a class="header__logo-link" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<img
					class="header__logo"
					src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/DOJ-OJP-BJS-NSVSP-Logo.webp' ); ?>"
					alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"
				>
			</a>
		<?php endif; ?>

	</div>
</header>

<?php
/**
 * ============================================================
 * CURRENT PAGE DETECTION (SINGLE SOURCE OF TRUTH)
 * ============================================================
 *
 * These booleans are reused across:
 * - Desktop navigation
 * - Mobile navigation
 *
 * They define WHEN a top-level nav item is considered "current".
 */

// Front page
$is_home = is_front_page();

// Resources section:
// - /resources page
// - single Resource posts (standard WP posts)
// - category archives
$is_resources =
	is_page( 'resources' ) ||
	is_singular( 'post' ) ||
	is_category();

// Glossary section:
// - /glossary page
// - optional glossary CPT (future-proofed)
$is_glossary =
	is_page( 'glossary' ) ||
	is_singular( 'glossary' ) ||
	is_post_type_archive( 'glossary' );

// FAQs section:
// - /faqs page
// - FAQ CPT single + archive
$is_faqs =
	is_page( 'faqs' ) ||
	is_singular( 'faq' ) ||
	is_post_type_archive( 'faq' );
?>

<div class="navbar">
	<div class="navbar__container">

		<!-- ========================= -->
		<!-- DESKTOP NAVIGATION -->
		<!-- ========================= -->
		<nav class="nav" role="navigation" aria-label="Primary">
			<ul class="nav__menu">

				<!-- Take Survey (anchor link – NOT a real page) -->
				<li class="nav__li nav__survey">
					<a
						class="nav__item<?php echo $is_home ? ' current-page' : ''; ?>"
						href="<?php echo $is_home ? '#take-survey' : esc_url( home_url( '/#take-survey' ) ); ?>"
					>
						Take Survey
					</a>
				</li>

				<!-- ========================= -->
				<!-- RESOURCES -->
				<!-- ========================= -->
				<li class="nav__li nav__has-dropdown">
					<a
						class="nav__item<?php echo $is_resources ? ' is-active' : ''; ?>"
						id="nav-resources"
						href="<?php echo $is_home ? '#resources' : esc_url( home_url( '/resources' ) ); ?>"
						<?php echo $is_resources ? ' aria-current="page"' : ''; ?>
						aria-haspopup="true"
						aria-expanded="false"
						aria-controls="resources-submenu"
					>
						Resources
						<span class="screen-reader-text">, open categories submenu</span>
					</a>

					<?php
					/**
					 * Resources dropdown
					 * ------------------
					 * Lists all WordPress post categories.
					 * Each link points to /resources#category-slug
					 */
					$resource_cats = get_categories([
						'taxonomy'   => 'category',
						'hide_empty' => true,
						'orderby'    => 'name',
						'order'      => 'ASC',
					]);

					$resources_page = get_page_by_path( 'resources' );
					$resources_url  = rtrim(
						$resources_page ? get_permalink( $resources_page ) : home_url( '/resources/' ),
						'/'
					);

					if ( ! empty( $resource_cats ) ) : ?>
						<ul class="nav-dropdown" id="resources-submenu" role="menu" aria-labelledby="nav-resources">
							<?php foreach ( $resource_cats as $cat ) : ?>
								<li class="nav-dropdown__item" role="none">
									<a
										role="menuitem"
										class="nav-dropdown__link"
										href="<?php echo esc_url( $resources_url . '#' . $cat->slug ); ?>"
									>
										<?php echo esc_html( $cat->name ); ?>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</li>

				<!-- ========================= -->
				<!-- GLOSSARY -->
				<!-- ========================= -->
				<li class="nav__li nav__has-dropdown">
					<a
						class="nav__item<?php echo $is_glossary ? ' is-active' : ''; ?>"
						id="nav-glossary"
						href="<?php echo $is_home ? '#glossary' : esc_url( home_url( '/glossary' ) ); ?>"
						<?php echo $is_glossary ? ' aria-current="page"' : ''; ?>
						aria-haspopup="true"
						aria-expanded="false"
						aria-controls="glossary-submenu"
					>
						Glossary
						<span class="screen-reader-text">, open categories submenu</span>
					</a>

					<?php
					$glossary_tax = taxonomy_exists( 'glossary_category' ) ? 'glossary_category' : 'category';
					$glossary_terms = get_terms([
						'taxonomy'   => $glossary_tax,
						'hide_empty' => true,
						'orderby'    => 'name',
						'order'      => 'ASC',
					]);

					$glossary_page = get_page_by_path( 'glossary' );
					$glossary_url  = rtrim(
						$glossary_page ? get_permalink( $glossary_page ) : home_url( '/glossary/' ),
						'/'
					);

					if ( ! empty( $glossary_terms ) && ! is_wp_error( $glossary_terms ) ) : ?>
						<ul class="nav-dropdown" id="glossary-submenu" role="menu" aria-labelledby="nav-glossary">
							<?php foreach ( $glossary_terms as $term ) : ?>
								<li class="nav-dropdown__item" role="none">
									<a
										role="menuitem"
										class="nav-dropdown__link"
										href="<?php echo esc_url( $glossary_url . '#' . $term->slug ); ?>"
									>
										<?php echo esc_html( $term->name ); ?>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</li>

				<!-- ========================= -->
				<!-- FAQS -->
				<!-- ========================= -->
				<li class="nav__li nav__has-dropdown">
					<a
						class="nav__item<?php echo $is_faqs ? ' is-active' : ''; ?>"
						id="nav-faqs"
						href="<?php echo $is_home ? '#faqs' : esc_url( home_url( '/faqs' ) ); ?>"
						<?php echo $is_faqs ? ' aria-current="page"' : ''; ?>
						aria-haspopup="true"
						aria-expanded="false"
						aria-controls="faqs-submenu"
					>
						FAQs
						<span class="screen-reader-text">, open categories submenu</span>
					</a>
				</li>

				<!-- Contact Us (anchor link) -->
				<li class="nav__li">
					<a
						class="nav__item"
						href="<?php echo $is_home ? '#contact-us' : esc_url( home_url( '/#contact-us' ) ); ?>"
					>
						Contact Us
					</a>
				</li>

			</ul>
		</nav>

		<!-- Desktop search -->
		<form class="search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php echo svg_icon( 'search__icon', 'search' ); ?>
			<input type="search" name="s" placeholder="Search" aria-label="Search">
			<button type="submit">
				<span class="screen-reader-text">Search</span>
			</button>
		</form>

	</div>
</div>

<!-- ========================================================= -->
<!-- MOBILE NAVIGATION -->
<!-- ========================================================= -->
<div class="mobile-navigation">

	<span hidden id="mobile-menu">Main menu</span>

	<button
		class="mobile-navigation__menu"
		aria-controls="mobile-navigation"
		aria-expanded="false"
		aria-labelledby="mobile-menu"
	>
		<i class="mobile-navigation__icon" aria-hidden="true"></i>
	</button>

	<nav class="mobile-navigation__nav" aria-label="Mobile menu" aria-hidden="true">

		<div class="mobile-navigation__controls">

			<!-- Close button -->
			<button class="mobile-navigation__close" type="button" aria-label="Close menu">
				<?php echo svg_icon( 'mobile-navigation__x', 'times' ); ?>
			</button>

			<!-- Back button (visible when submenu is open) -->
			<button class="mobile-navigation__back" type="button" aria-label="Back to main menu" aria-hidden="true">
				<?php echo svg_icon( 'mobile-navigation__chevron-left', 'chevron-left' ); ?>
				<span class="screen-reader-text">Back</span>
			</button>

		</div>

		<ul class="mobile-navigation__list">

			<li class="mobile-navigation__item">
				<a class="mobile-navigation__link<?php echo $is_home ? ' is-active' : ''; ?>"
				   href="<?php echo esc_url( home_url( '/' ) ); ?>">
					Home
				</a>
			</li>

			<li class="mobile-navigation__item">
				<a class="mobile-navigation__link"
				   href="<?php echo $is_home ? '#take-survey' : esc_url( home_url( '/#take-survey' ) ); ?>">
					Take Survey
				</a>
			</li>

			<li class="mobile-navigation__item mobile-navigation__item--has-submenu">
				<div class="mobile-navigation__row">
					<a class="mobile-navigation__link<?php echo $is_resources ? ' is-active' : ''; ?>"
					   href="<?php echo esc_url( home_url( '/resources' ) ); ?>"
					   <?php echo $is_resources ? ' aria-current="page"' : ''; ?>>
						Resources
					</a>
				</div>
			</li>

			<li class="mobile-navigation__item mobile-navigation__item--has-submenu">
				<div class="mobile-navigation__row">
					<a class="mobile-navigation__link<?php echo $is_glossary ? ' is-active' : ''; ?>"
					   href="<?php echo esc_url( home_url( '/glossary' ) ); ?>"
					   <?php echo $is_glossary ? ' aria-current="page"' : ''; ?>>
						Glossary
					</a>
				</div>
			</li>

			<li class="mobile-navigation__item mobile-navigation__item--has-submenu">
				<div class="mobile-navigation__row">
					<a class="mobile-navigation__link<?php echo $is_faqs ? ' is-active' : ''; ?>"
					   href="<?php echo esc_url( home_url( '/faqs' ) ); ?>"
					   <?php echo $is_faqs ? ' aria-current="page"' : ''; ?>>
						FAQs
					</a>
				</div>
			</li>

			<li class="mobile-navigation__item">
				<a class="mobile-navigation__link"
				   href="<?php echo $is_home ? '#contact-us' : esc_url( home_url( '/#contact-us' ) ); ?>">
					Contact Us
				</a>
			</li>

		</ul>

	</nav>
</div>

<?php wp_footer(); ?>
</body>
</html>