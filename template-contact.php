<?php
/**
 * Template Name: Contact Us
 * Description: A contact card using the editable footer details, without a sidebar.
 * @package bja-nsvsp
 */
$contact_details = bjansvsp_contact_details();
get_header();
?>
<main id="single-page" class="contact-page" tabindex="-1">
  <?php while (have_posts()) : the_post(); ?>
    <article <?php post_class('contact-page__content'); ?>>
      <header class="contact-page__intro">
        <h1 class="h1__heading"><?php the_title(); ?></h1>
        <div class="single-page__content"><?php the_content(); ?></div>
      </header>
      <section class="contact-card" aria-labelledby="contact-card-heading">
        <p class="contact-card__eyebrow">NSVSP Support</p>
        <h2 id="contact-card-heading" class="h2__heading">Get in touch</h2>
        <dl class="contact-card__details">
          <?php if ($contact_details['email'] !== '') : ?>
            <div class="contact-card__detail">
              <dt><span class="contact-card__icon-wrap" aria-hidden="true"><?php svg_icon('contact-card__icon', 'envelope'); ?></span>Email</dt>
              <dd><a href="<?php echo esc_url('mailto:' . $contact_details['email']); ?>"><?php echo esc_html($contact_details['email']); ?></a></dd>
            </div>
          <?php endif; ?>
          <?php if ($contact_details['phone'] !== '') : ?>
            <div class="contact-card__detail">
              <dt><span class="contact-card__icon-wrap" aria-hidden="true"><?php svg_icon('contact-card__icon', 'phone'); ?></span>Phone</dt>
              <dd>
                <?php if ($contact_details['phone_number'] !== '' && $contact_details['phone_number'] !== '+') : ?>
                  <a href="<?php echo esc_url('tel:' . $contact_details['phone_number']); ?>"><?php echo esc_html($contact_details['phone']); ?></a>
                <?php else : ?>
                  <?php echo esc_html($contact_details['phone']); ?>
                <?php endif; ?>
              </dd>
            </div>
          <?php endif; ?>
          <?php if ($contact_details['address'] !== '') : ?>
            <div class="contact-card__detail">
              <dt><span class="contact-card__icon-wrap" aria-hidden="true"><?php svg_icon('contact-card__icon', 'map'); ?></span>Address</dt>
              <dd><?php echo nl2br(esc_html($contact_details['address'])); ?></dd>
            </div>
          <?php endif; ?>
        </dl>
      </section>
    </article>
  <?php endwhile; ?>
</main>
<?php get_footer(); ?>
