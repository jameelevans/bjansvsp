<?php
/**
 * * The template for displaying the about page
 *
 * @package your-wp-project
 */

 get_header('general');

?>
	<main id="main-content">
    <section class="error">
      
      <h1 class="error__header">The requested page could not be found.</h1>
      <p class="error__p">Please go back to the <a href="<?php echo esc_url( home_url('/')); ?>">home</a> page.</p> 
      <?php echo svg_icon('error__icon', 'exclamation');?>
    </section>
	</main>
<?php get_footer(); ?>
