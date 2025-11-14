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
<a class="screen-reader-shortcut" href="#front-page	">Skip to main content</a>

<header id="top" class="header home-header" role="banner">
	<div class="header__container">
		<?php if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) : ?>
		<?php the_custom_logo(); ?>
		<?php else : ?>
		<a class="header__logo-link" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<img class="header__logo" src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/DOJ-OJP-BJS-NSVSP-Logo.webp' ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
		</a>
		<?php endif; ?>
		
	</div>
</header>

<div class="navbar">
	<div class="navbar__container">
		<nav class="nav" role="navigation" aria-label="Primary">
			<ul class="nav__menu">
				<li class="nav__li nav__survey">
					<a class="nav__item<?php echo is_front_page() ? ' current-page' : ''; ?>"
					href="<?php echo is_front_page() ? '#take-survey' : esc_url( home_url( '/#take-survey' ) ); ?>">
					Take Survey
					</a>
				</li>
				<li class="nav__li nav__has-dropdown">
					<a
						class="nav__item<?php echo is_singular('post') ? ' is-active' : ''; ?>"
						id="nav-resources"
						href="<?php echo is_front_page() ? '#resources' : esc_url( home_url( '/resources' ) ); ?>"
						aria-haspopup="true"
						aria-expanded="false"
						aria-controls="resources-submenu"
					>
						Resources
						<span class="screen-reader-text">, open categories submenu</span>
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
						<ul class="nav-dropdown" id="resources-submenu" role="menu" aria-labelledby="nav-resources">
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
								<li class="nav-dropdown__item" role="none">
									<a
										role="menuitem"
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
						class="nav__item"
						id="nav-glossary"
						href="<?php echo is_front_page() ? '#glossary' : esc_url( home_url( '/glossary' ) ); ?>"
						aria-haspopup="true"
						aria-expanded="false"
						aria-controls="glossary-submenu"
					>
						Glossary
						<span class="screen-reader-text">, open categories submenu</span>
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
						<ul class="nav-dropdown" id="glossary-submenu" role="menu" aria-labelledby="nav-glossary">
							<?php foreach ( $glossary_terms as $term ) : ?>
								<?php $anchor = $term->slug; ?>
								<li class="nav-dropdown__item" role="none">
									<a
										role="menuitem"
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
						class="nav__item"
						id="nav-faqs"
						href="<?php echo is_front_page() ? '#faqs' : esc_url( home_url( '/faqs' ) ); ?>"
						aria-haspopup="true"
						aria-expanded="false"
						aria-controls="faqs-submenu"
					>
						FAQs
						<span class="screen-reader-text">, open categories submenu</span>
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
						<ul class="nav-dropdown" id="faqs-submenu" role="menu" aria-labelledby="nav-faqs">
							<?php foreach ( $faqs_terms as $term ) : ?>
								<?php $anchor = $term->slug; ?>
								<li class="nav-dropdown__item" role="none">
									<a
										role="menuitem"
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
			<?php echo svg_icon('search__icon', 'search');?>
			<input type="search" name="s" placeholder="Search" aria-label="Search">
			<button type="submit" aria-label="Submit search">
				<span class="screen-reader-text">Search</span>
			</button>
		</form>
	</div>
</div>

<!-- Mobile navigation -->
<div class="mobile-navigation">
	<!-- Hidden menu label for accessibility-->
	<span hidden="" id="mobile-menu">Main menu</span>

	<button class="mobile-navigation__menu" aria-controls="mobile-navigation" tabindex="0" aria-expanded="false" aria-labelledby="mobile-menu">

		<!-- navigation menu icon-->
		<i class="mobile-navigation__icon" alt="Menu icon" aria-hidden="true">&nbsp;</i>
	</button>


	<nav class="mobile-navigation__nav" aria-label="Mobile menu" aria-labelledby="mobile-menu" aria-hidden="true">
		<button class="mobile-navigation__close" type="button" aria-label="Close menu">
			<?php echo svg_icon('mobile-navigation__x', 'times');?>
		</button>
		
		<ul class="mobile-navigation__list">
			<li class="mobile-navigation__item">
				<a href="<?php echo esc_url( home_url() ); ?>"
				class="mobile-navigation__link" title="Go to the Take Survey section">
				Home
				</a>
			</li>
			<li class="mobile-navigation__item">
				<a href="<?php echo is_front_page() ? '#take-survey' : esc_url( home_url( '/#take-survey' ) ); ?>"
				class="mobile-navigation__link" title="Go to the Take Survey section">
				Take Survey
				</a>
			</li>
			<li class="mobile-navigation__item">
				<a href="<?php echo is_front_page() ? '#resources' : esc_url( home_url( '/#resources' ) ); ?>"
				class="mobile-navigation__link<?php echo is_singular('post') ? ' is-active' : ''; ?>"
				title="Go to the Resources section">
				Resources
				</a>
			</li>
			<li class="mobile-navigation__item">
				<a href="<?php echo is_front_page() ? '#glossary' : esc_url( home_url( '/#glossary' ) ); ?>"
				class="mobile-navigation__link" title="Go to the Glossary section">
				Glossary
				</a>
			</li>
			<li class="mobile-navigation__item">
				<a href="<?php echo is_front_page() ? '#faqs' : esc_url( home_url( '/#faqs' ) ); ?>"
				class="mobile-navigation__link" title="Go to the FAQs section">
				FAQs
				</a>
			</li>
			<li class="mobile-navigation__item">
				<a href="<?php echo is_front_page() ? '#contact-us' : esc_url( home_url( '/#contact-us' ) ); ?>"
				class="mobile-navigation__link" title="Go to the Contact Us section">
				Contact Us
				</a>
			</li>

			<li class="mobile-navigation__item">
				<form class="mobile-navigation__search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php echo svg_icon('search__icon', 'search'); ?>
				<input type="search" name="s" placeholder="Search" aria-label="Search">
				<button type="submit" aria-label="Submit search">
					<span class="screen-reader-text">Search</span>
				</button>
				</form>
			</li>
			</ul>

		
	</nav>

</div>
