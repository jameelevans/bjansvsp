<?php
/**
 * * The template for displaying the footer
 *
 * @package your-wp-project
 */

?>
    <!--Footer-->
    <footer class="footer">
        <div class="footer__top">
            <div class="footer__container">
                <nav class="footer__nav" aria-label="Footer">
                <ul class="footer__list">
                    <li class="footer__item"><a href="https://www.icfsurvey2.com/NSVSP" target="_blank" rel="noopener noreferrer" class="footer__links">Log In<span class="screen-reader-text"> (opens in a new tab)</span></a></li>
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
                        <p class="footer__text">The <b>National Survey of Victim Service Providers</b> is a component of the Office for Victims of Crime, Office of Justice Programs, U.S. Department of Justice.</p>
                        <p class="footer__text">This website is funded through xxx. Neither the Bureau Justice Statistics nor any of its components operate, control,
                        are responsible for, or necessarily endorse, this website (including, without limitation, its content, technical
                        infrastructure, and policies, and any services or tools provided). Lorem ipsum dolor sit amet consectetur adipiscing
                        elit. Sit amet consectetur adipiscing elit quisque faucibus ex.</p>
                    </div>
                
                    <div id="contact-us" class="contact" tabindex="-1">
                        <h2 class="footer__h4">Contact Us</h2>
                        <ul class="contact__list">
                            <li class="contact__item"><?php echo svg_icon('contact__icon', 'envelope');?> our-email@EMAIL.COM</li>
                            <li class="contact__item"><?php echo svg_icon('contact__icon', 'phone');?> (444)444-4444</li>
                            <li class="contact__item"><?php echo svg_icon('contact__icon', 'map');?> 123 N Best Street, City, ST 22222</li>
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
