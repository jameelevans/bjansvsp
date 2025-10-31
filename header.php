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

	<div class="navbar">
		<div class="navbar__container">
			<nav class="nav" role="navigation" aria-label="Primary">
				<ul class="nav__menu">
					<li>
						<a class="nav__item<?php echo is_front_page() ? ' current-page' : ''; ?>"
						href="<?php echo is_front_page() ? '#take-survey' : esc_url( home_url( '/#take-survey' ) ); ?>">
						Take Survey
						</a>
					</li>
					<li>
						<a class="nav__item<?php echo is_singular('post') ? ' is-active' : ''; ?>"
						href="<?php echo is_front_page() ? '#resources' : esc_url( home_url( '/#resources' ) ); ?>">
						Resources
						</a>
					</li>
					<li>
						<a class="nav__item"
						href="<?php echo is_front_page() ? '#dictionary' : esc_url( home_url( '/#dictionary' ) ); ?>">
						Dictionary
						</a>
					</li>
					<li>
						<a class="nav__item"
						href="<?php echo is_front_page() ? '#faqs' : esc_url( home_url( '/#faqs' ) ); ?>">
						FAQs
						</a>
					</li>
					<li>
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

			<!-- Mobile navigation -->
			 <div class="mobile-navigation">
				<!-- Hidden menu label for accessibility-->
				<span hidden="" id="mobile-menu">Main menu</span>

				<button class="mobile-navigation__menu" aria-controls="mobile-navigation" tabindex="0" aria-expanded="false" aria-labelledby="mobile-menu">

					<!-- navigation menu icon-->
					<i class="mobile-navigation__icon" alt="Menu icon" aria-hidden="true">&nbsp;</i>
				</button>


				<nav class="mobile-navigation__nav" aria-label="Mobile menu" aria-labelledby="mobile-menu" aria-hidden="true">
					
					<ul class="mobile-navigation__list">
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
							<a href="<?php echo is_front_page() ? '#dictionary' : esc_url( home_url( '/#dictionary' ) ); ?>"
							class="mobile-navigation__link" title="Go to the Dictionary section">
							Dictionary
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

			
		</div>
	</div>
</header>