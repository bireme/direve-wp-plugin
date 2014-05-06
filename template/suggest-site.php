<?php
/*
Template Name: LIS Detail
*/

$lis_config = get_option('lis_config');

$site_language = strtolower(get_bloginfo('language'));

?>

<?php get_header('direve'); ?>

<div id="content" class="row-fluid">
        <div class="ajusta2">
            <div class="row-fluid">
                <a href="<?php echo home_url(); ?>"><?php _e('Home','lis'); ?></a> > 
                <a href="<?php echo home_url($plugin_slug); ?>"><?php _e('Health Information Locator', 'lis') ?> </a> > 
                <?php _e('Suggest a site','lis'); ?>
            </div>

            <section id="conteudo">
                <header class="row-fluid border-bottom">
                    <h1 class="h1-header"><?php _e('Suggest a site','lis'); ?></h1>
                </header>
                <div class="row-fluid">
                    <article class="conteudo-loop suggest-form">
                        <form method="post" action="<?php echo home_url($plugin_slug) ?>/suggest-site-details">

                            <?php _e('Link', 'lis') ?>
                            <p><input type="text"  name="link" size="80"/></p>
                           
                            <div class="btn-line">
                                <input type="submit" value="<?php _e('Send', 'lis') ?>"/>
                            </div>

                        </form>
    
                    </article>
                </div>
            </section>

        </div>
    </div>

<?php get_footer();?>
