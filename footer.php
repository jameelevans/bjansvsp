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
                <nav class="footer__nav">
                <ul class="footer__list">
                    <li class="footer__item"><a href="#take-survey" class="footer__links">Take Survey</a></li>
                    <li class="footer__item"><a href="#resources" class="footer__links">Resources</a></li>
                    <li class="footer__item"><a href="#dictionary" class="footer__links">Dictionary</a></li>
                    <li class="footer__item"><a href="#faqs" class="footer__links">FAQs</a></li>
                    <li class="footer__item"><a href="#contact-us" class="footer__links">Contact Us</a></li>
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
                
                    <div id="contact-us" class="contact">
                        <h4 class="footer__h4">Contact Us</h4>
                        <ul class="contact__list">
                            <li class="contact__item"><?php echo svg_icon('contact__icon', 'envelope');?> our-email@EMAIL.COM</li>
                            <li class="contact__item"><?php echo svg_icon('contact__icon', 'phone');?> (444)444-4444</li>
                            <li class="contact__item"><?php echo svg_icon('contact__icon', 'map');?> 123 N Best Street, City, ST 22222</li>
                        </ul>
                    </div>
                </div>
                
                
            </div> 
            <div class="footer__container">
                <p class="external-links"><a href="#">BJA.OJP.gov</a> | <a href="">Accessibility</a> | <a href="">Plain Language</a> | <a href="">Privacy Policy</a> | <a href="">Legal Policies and Disclaimer</a> | <a href="">No FEAR Act</a> | <a href="">Freedom of Information Act</a> | <a href="">USA.gov</a> | <a href="">Justice.gov</a>
                    </p>
                <div class="footer__logos">
                    <a href=""><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/DOJ-OJP-BJS-NSVSP-Logo.webp' ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" alt=""></a>
                    <a href=""><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/us-office-of-justice-programs-logo.webp' ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" alt=""></a>
                    <a href=""><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/bjs-bureau-of-justice-statistics-seeklogo.webp' ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" alt=""></a>
                    <a href=""><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/ovc-logo.webp' ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" alt=""></a>
                    <a href=""><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/icf-logo.webp' ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" alt=""></a>
                </div>
            </div>
        </div>
            
            
            <a class="back-top" href="#top" aria-label="Go back to the top"><?php echo svg_icon('back-top__icon', 'up');?>Top</a>
        </div>
        
    </footer>
  


    <?php wp_footer(); ?>
</body>
</html>
