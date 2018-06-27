<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

get_header(); ?>
    <section id="main-container" class="">
        <div class="row">
            <div id="main-content" class="main-content">
                <div id="primary" class="content-area">
                    <div id="content" class="site-content" role="main">
                        <?php
                            // Start the Loop.
                            while ( have_posts() ) : the_post();
                                $subtitle = get_post_meta( get_the_ID(), 'offer_subtitle', true );
                                $includes = get_post_meta( get_the_ID(), 'offer_includes', true);
                        ?>
                                <article id="post-<?php the_ID(); ?>" <?php post_class('offers-single'); ?>>
                                    <div class="offer-title">
                                        <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
                                        <?php if($subtitle): ?>
                                            <div class="subtitle"><?php echo esc_html($subtitle);?></div>
                                        <?php endif; ?>
                                    </div>
                                    <header class="entry-header">
                                        <?php the_post_thumbnail('full'); ?>
                                        
                                    </header><!-- .entry-header -->

                                    
                                    <div class="offer-content">
                                        <div class="entry-content">
                                            <?php the_content(); ?>
                                        </div><!-- .entry-content -->
                                        <?php if($includes && is_array($includes)): ?>
                                            <div class="offer-include">
                                                <h5><?php esc_html_e('Offer Include', 'opal-hotel-room-booking'); ?></h5>
                                                <?php foreach($includes as $include): ?>
                                                    <?php echo '- '. trim($include['offer_include']).'<br>'; ?>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </article><!-- #post-## -->
                        <?php endwhile; ?>
                    </div><!-- #content -->
                </div><!-- #primary -->
            </div>
        </div>
    </section>
<?php
get_footer();
