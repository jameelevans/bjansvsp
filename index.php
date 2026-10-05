<?php get_header(); ?>
<main id="main-content" class="archive-content" tabindex="-1">
  <h1 class="h1__heading"><?php echo is_archive() ? wp_kses_post(get_the_archive_title()) : esc_html(get_bloginfo('name')); ?></h1>
  <?php if (is_archive()) the_archive_description('<div class="sub__heading">', '</div>'); ?>
  <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <article class="archive-resource">
      <h2 class="h2__heading"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
      <div class="sub__heading"><?php the_excerpt(); ?></div>
    </article>
  <?php endwhile; the_posts_pagination(); else : ?>
    <p class="sub__heading">No resources found.</p>
  <?php endif; ?>
</main>
<?php get_footer(); ?>
