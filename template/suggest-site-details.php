<?php
/*
Template Name: LIS Detail
*/

$lis_config = get_option('lis_config');

$lis_service_url = $lis_config['service_url'];
$site_language = strtolower(get_bloginfo('language'));

$site_link = $_POST['link'];
$site_meta_tags = array();

if ($site_link != ''){
    if ( preg_match('/^http/',$site_link) == false) {
        $site_link = "http://" . $site_link;
    }
    $site_meta_tags = get_site_meta_tags($site_link);

}

?>

<?php get_header(); ?>

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

                        <form method="post" action="<?php echo $lis_service_url ?>suggest-resource">

                            <?php _e('Link', 'lis') ?>
                            <p><input type="text"  name="link" size="80" value="<?php echo $site_link ?>"/></p>

 
                            <?php _e('Title', 'lis') ?> 
                            <p><input type="text" name="title" size="80" value="<?php echo $site_meta_tags['title'] ?>"/></p>

                            <?php _e('Keywords', 'lis') ?>
                            <p><input type="text" placeholder="" name="keywords" size="80" value="<?php echo $site_meta_tags['keywords'] ?>"/></p>

                            <?php _e('Abstract', 'lis') ?>
                            <p><textarea placeholder="" name="abstract" rows="6" cols="80"><?php echo $site_meta_tags['description'] ?></textarea>
                            </p>

                            <script type="text/javascript">
                                var RecaptchaOptions = {
                                    theme : 'clean',
                                    lang : '<?php echo substr($site_language, 0,2); ?>'
                                };
                            </script>
                            <script type="text/javascript"
                               src="http://www.google.com/recaptcha/api/challenge?k=6LcV0ugSAAAAAEpxBvqmNlnOZIAKSf_E6M-s8abc">
                            </script>
                            <noscript>
                               <iframe src="http://www.google.com/recaptcha/api/noscript?k=6LcV0ugSAAAAAEpxBvqmNlnOZIAKSf_E6M-s8abc"
                                   height="300" width="500" frameborder="0"></iframe><br>
                               <textarea name="recaptcha_challenge_field" rows="3" cols="40">
                               </textarea>
                               <input type="hidden" name="recaptcha_response_field" value="manual_challenge">
                            </noscript>


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
