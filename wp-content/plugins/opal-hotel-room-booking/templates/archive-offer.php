<?php

get_header(); ?>
    <section id="main-container" class="inner">
        <div class="row">
            <div id="main-content" class="main-content">
                <div id="primary" class="content-area">
                    <div id="content" class="site-content" role="main">

                        <?php if ( have_posts() ) : ?>

                            <header class="page-header">
                                <h1 class="page-title"> <?php esc_html_e('Find what you are looking for', 'opal-hotel-room-booking');?></h1>
                            </header><!-- .page-header -->

                            <?php
                            // Start the Loop.
                            while ( have_posts() ) : the_post();

                                $subtitle = get_post_meta( get_the_ID(), 'offer_subtitle', true );
                                $includes = get_post_meta( get_the_ID(), 'offer_includes', true );
                                ?>
                                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                                    <div class="offer-featured">
                                        <?php the_post_thumbnail('full'); ?>
                                        <div class="offer-title">
                                            <a href="<?php the_permalink()?>" title="<?php the_title();?>"><?php the_title( '<h1 class="entry-title">', '</h1>' ); ?></a>
                                            <?php if($subtitle): ?>
                                                <div class="subtitle"><?php echo esc_html($subtitle);?></div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="offer-detail pull-right">
                                            <a href="<?php the_permalink()?>" title="<?php the_title();?>"><?php esc_html_e('Offer detail', 'opal-hotel-room-booking');?></a>
                                        </div>
                                    </div>
                                    <div class="offer-content row">
                                        <div class="offer-excerpt col-md-7">
                                            <?php the_excerpt(); ?>
                                        </div><!-- .entry-content -->
                                        <?php if($includes && is_array($includes)): ?>
                                            <div class="offer-include col-md-5">
                                                <h5><?php esc_html_e('Offer Include', 'opal-hotel-room-booking'); ?></h5>
                                                <?php foreach($includes as $include): ?>
                                                    <?php echo '- '. trim($include['offer_include']).'<br>'; ?>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </article><!-- #post-## -->
                            <?php

                            endwhile;
                            // Previous/next page navigation.
                            mazison_fnc_paging_nav();

                        else :
                            // If no content, include the "No posts found" template.
                            get_template_part( 'content', 'none' );

                        endif;
                        ?>
                    </div><!-- #content -->


                </div><!-- #primary -->
                <?php get_sidebar( 'content' ); ?>
            </div><!-- #main-content -->



        </div>
    </section>
<?php
get_footer();

