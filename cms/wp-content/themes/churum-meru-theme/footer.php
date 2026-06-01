<?php
/**
 * Footer Template – Churum Meru Theme
 * Datei: footer.php
 * Version: 2.1.0 – Footer-Logo separates Theme Mod
 */
?>

</main><!-- /#main.site-main -->

<footer class="site-footer" role="contentinfo">

    <!-- ── Footer Top ─────────────────────────────────────── -->
    <div class="footer-top">
        <div class="container">

            <!-- Spalte 1: Footer-Logo (eigenes Theme Mod, unabhängig vom Header-Logo) -->
            <div class="footer-widget footer-col-logo">
                <?php
                $home_url = function_exists('pll_home_url') ? pll_home_url() : home_url('/');

                // 1. Footer-spezifisches Logo (via Customizer → Footer → Footer-Logo)
                $footer_logo_id = get_theme_mod('footer_logo');

                if ( $footer_logo_id ) :
                    $logo = wp_get_attachment_image_src( $footer_logo_id, 'full' );
                    if ( $logo ) : ?>
                        <a href="<?php echo esc_url( $home_url ); ?>" rel="home">
                            <img src="<?php echo esc_url( $logo[0] ); ?>"
                                 alt="<?php bloginfo('name'); ?>"
                                 class="footer-logo-img"
                                 loading="lazy">
                        </a>
                    <?php endif;

                // 2. Fallback: globales Header-Logo
                elseif ( $header_logo_id = get_theme_mod('custom_logo') ) :
                    $logo = wp_get_attachment_image_src( $header_logo_id, 'full' );
                    if ( $logo ) : ?>
                        <a href="<?php echo esc_url( $home_url ); ?>" rel="home">
                            <img src="<?php echo esc_url( $logo[0] ); ?>"
                                 alt="<?php bloginfo('name'); ?>"
                                 class="footer-logo-img"
                                 loading="lazy">
                        </a>
                    <?php endif;

                // 3. Fallback: Seitenname als Text
                else : ?>
                    <a href="<?php echo esc_url( $home_url ); ?>"
                       class="footer-site-name" rel="home">
                        <?php bloginfo('name'); ?>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Spalte 2: Letzte Einträge -->
            <div class="footer-widget footer-col-recent">
                <h3 class="widget-title">
                    <?php esc_html_e( 'Letzte Einträge', 'churum-meru-theme' ); ?>
                </h3>
                <?php
                $recent = new WP_Query([
                    'post_type'      => 'post',
                    'posts_per_page' => 5,
                    'post_status'    => 'publish',
                    'no_found_rows'  => true,
                ]);
                if ( $recent->have_posts() ) : ?>
                    <ul>
                        <?php while ( $recent->have_posts() ) : $recent->the_post(); ?>
                            <li>
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_title(); ?>
                                </a>
                            </li>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </ul>
                <?php endif; ?>
            </div>

            <!-- Spalte 3: Gesetzlich -->
            <div class="footer-widget footer-col-legal">
                <h3 class="widget-title">
                    <?php esc_html_e( 'Gesetzlich', 'churum-meru-theme' ); ?>
                </h3>
                <?php
                if ( has_nav_menu('footer-legal') ) :
                    wp_nav_menu([
                        'theme_location' => 'footer-legal',
                        'container'      => false,
                        'depth'          => 1,
                        'fallback_cb'    => false,
                    ]);
                endif; ?>
            </div>

            <!-- Spalte 4: Kontakt Widget-Area -->
            <div class="footer-widget footer-col-contact">
                <h3 class="widget-title">
                    <?php esc_html_e( 'Kontakt', 'churum-meru-theme' ); ?>
                </h3>
                <?php churummeru_footer_contact_sidebar(); ?>
            </div>

        </div><!-- /.container -->
    </div><!-- /.footer-top -->

    <!-- ── Footer Bottom ──────────────────────────────────── -->
    <div class="footer-bottom">
        <div class="container">
            <p class="footer-copyright">
                <?php churummeru_footer_copyright(); ?>
            </p>
        </div>
    </div><!-- /.footer-bottom -->

</footer><!-- /.site-footer -->

<?php wp_footer(); ?>
</body>
</html>
