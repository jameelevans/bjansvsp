<?php
/**
 * * The template for displaying the footer
 *
 * @package your-wp-project
 */

$footer_defaults = bjansvsp_footer_defaults();
$footer_disclaimer = get_theme_mod('bjansvsp_footer_text', $footer_defaults['bjansvsp_footer_text']);
$contact_email = sanitize_email(get_theme_mod('bjansvsp_contact_email', $footer_defaults['bjansvsp_contact_email']));
$contact_phone = sanitize_text_field(get_theme_mod('bjansvsp_contact_phone', $footer_defaults['bjansvsp_contact_phone']));
$contact_address = sanitize_textarea_field(get_theme_mod('bjansvsp_contact_address', $footer_defaults['bjansvsp_contact_address']));
$contact_phone_number = preg_replace('/[^0-9]/', '', $contact_phone);
if (strpos($contact_phone, '+') === 0) $contact_phone_number = '+' . $contact_phone_number;
?>
    <!--Footer-->
    <footer class="footer">
        <div class="footer__top">
            <div class="footer__container">
                <nav class="footer__nav" aria-label="Footer">
                <ul class="footer__list">
                    <li class="footer__item"><a href="https://www.icfsurvey2.com/NSVSP" target="_blank" rel="noopener noreferrer" class="footer__links">Log In<span class="screen-reader-text"> (opens in a new tab)</span></a></li>
                    <li class="footer__item"><a href="<?php echo esc_url(home_url('/')); ?>" class="footer__links"<?php echo is_front_page() ? ' aria-current="page"' : ''; ?>>Home</a></li>
                    <li class="footer__item"><a href="<?php echo esc_url(home_url('/resources/')); ?>" class="footer__links">Resources</a></li>
                    <li class="footer__item"><a href="<?php echo esc_url(home_url('/glossary/')); ?>" class="footer__links">Glossary</a></li>
                    <li class="footer__item"><a href="<?php echo esc_url(home_url('/faqs/')); ?>" class="footer__links">FAQs</a></li>
                    <li class="footer__item"><a href="<?php echo esc_url(home_url('/#contact-us')); ?>" class="footer__links">Contact Us</a></li>
                </ul>
            </nav>
            </div>
        </div>
        <div class="footer__bottom">
            <div class="footer__container">
                <div class="footer__content">
                    <div class="footer__disclaimer">
                        <?php echo bjansvsp_format_footer_disclaimer($footer_disclaimer); ?>
                    </div>
                
                    <div id="contact-us" class="contact" tabindex="-1">
                        <h2 class="footer__h4">Contact Us</h2>
                        <ul class="contact__list">
                            <?php if ($contact_email !== '') : ?>
                                <li class="contact__item"><?php echo svg_icon('contact__icon', 'envelope');?> <a class="contact__link" href="<?php echo esc_url('mailto:' . $contact_email); ?>"><?php echo esc_html($contact_email); ?></a></li>
                            <?php endif; ?>
                            <?php if ($contact_phone !== '') : ?>
                                <li class="contact__item"><?php echo svg_icon('contact__icon', 'phone');?>
                                    <?php if ($contact_phone_number !== '' && $contact_phone_number !== '+') : ?>
                                        <a class="contact__link" href="<?php echo esc_url('tel:' . $contact_phone_number); ?>"><?php echo esc_html($contact_phone); ?></a>
                                    <?php else : ?>
                                        <?php echo esc_html($contact_phone); ?>
                                    <?php endif; ?>
                                </li>
                            <?php endif; ?>
                            <?php if ($contact_address !== '') : ?>
                                <li class="contact__item"><?php echo svg_icon('contact__icon', 'map');?> <?php echo nl2br(esc_html($contact_address)); ?></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
                
                
            </div> 
            <div class="footer__container">
                <p class="external-links">
                  <?php
                  $links = ['BJA.OJP.gov' => 'https://bja.ojp.gov/'];
                  foreach (['accessibility' => 'Accessibility', 'plain-language' => 'Plain Language', 'legal' => 'Legal Policies and Disclaimer', 'no-fear' => 'No FEAR Act', 'foia' => 'Freedom of Information Act'] as $key => $label) {
                    $url = get_theme_mod('bjansvsp_policy_' . $key);
                    if ($url) $links[$label] = $url;
                  }
                  if (get_privacy_policy_url()) $links['Privacy Policy'] = get_privacy_policy_url();
                  $links['USA.gov'] = 'https://www.usa.gov/';
                  $links['Justice.gov'] = 'https://www.justice.gov/';
                  $rendered = [];
                  foreach ($links as $label => $url) $rendered[] = '<a href="' . esc_url($url) . '">' . esc_html($label) . '</a>';
                  echo implode(' | ', $rendered);
                  ?>
                </p>
                <div class="footer__logos">
                    <a href="<?php echo esc_url(home_url('/')); ?>"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/DOJ-OJP-BJS-NSVSP-Logo-footer.webp'); ?>" alt="National Survey of Victim Service Providers" width="293" height="100" loading="lazy" decoding="async"></a>
                    <a href="https://www.ojp.gov/"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/us-office-of-justice-programs-logo-footer.webp'); ?>" alt="Office of Justice Programs" width="100" height="100" loading="lazy" decoding="async"></a>
                    <a href="https://bjs.ojp.gov/"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/bjs-bureau-of-justice-statistics-seeklogo-footer.webp'); ?>" alt="Bureau of Justice Statistics" width="337" height="100" loading="lazy" decoding="async"></a>
                    <a href="https://ovc.ojp.gov/"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/ovc-logo-footer.webp'); ?>" alt="Office for Victims of Crime" width="280" height="100" loading="lazy" decoding="async"></a>
                    <a href="https://www.icf.com/"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/icf-logo-footer.webp'); ?>" alt="ICF" width="123" height="100" loading="lazy" decoding="async"></a>
                </div>
            </div>
        </div>
            
            
            <a class="back-top" href="#top" aria-label="Go back to the top"><?php echo svg_icon('back-top__icon', 'up');?>Top</a>
        
    </footer>
  


    <?php wp_footer(); ?>
</body>
</html>
