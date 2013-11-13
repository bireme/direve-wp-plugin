<?php
/*
Template Name: LIS Detail
*/

$direve_config = get_option('direve_config');

$request_uri = $_SERVER["REQUEST_URI"];
$request_parts = explode('/', $request_uri);
$event_id = end($request_parts);

$direve_service_url = $direve_config['service_url'];
$direve_disqus_id  = $direve_config['disqus_shortname'];
$direve_addthis_id = $direve_config['addthis_profile_id'];
$direve_service_request = $direve_service_url . 'api/event/search/?id=events.event.' .$event_id . '&op=related';

$response = @file_get_contents($direve_service_request);

if ($response){
    $response_json = json_decode($response);

    $resource = $response_json->diaServerResponse[0]->match->docs[0];
    $related_list = $response_json->diaServerResponse[0]->response->docs;
}

?>

<?php get_header(); ?>

<div id="content" class="row-fluid">
        <div class="ajusta2">
            <div class="row-fluid breadcrumb">
                <a href="<?php echo home_url(); ?>"><?php _e('Home','direve'); ?></a> > 
                <a href="<?php echo home_url($eve_plugin_slug); ?>"><?php _e('Events Directory', 'direve') ?> </a> > 
                <?php _e('Resource','direve'); ?>
            </div>
            <div class="row-fluid">
                <section class="header-search">
                    <?php if ($direve_config['show_form']) : ?>
                        <form role="search" method="get" id="searchform" action="<?php echo home_url($eve_plugin_slug); ?>">
                            <input value="<?php echo $query ?>" name="q" class="input-search" id="s" type="text" placeholder="<?php _e('Search', 'direve'); ?>...">
                            <input id="searchsubmit" value="<?php _e('Search', 'direve'); ?>" type="submit">
                        </form>
                    <?php endif; ?>
                </section>
                <div class="pull-right">
                    <a href="<?php echo home_url($eve_plugin_slug); ?>/suggest-site" class="header-colabore"><?php _e('Suggest a site','direve'); ?></a>
                </div>   
            </div>

            <section id="conteudo">
                <header class="row-fluid border-bottom">
                    <h1 class="h1-header"><?php echo $resource->title; ?></h1>
                </header>
                <div class="row-fluid">
                    <article class="conteudo-loop">
                        <div class="conteudo-loop-rates">
                            <div class="star" data-score="1"></div>
                        </div>
                        
                        <?php if ($resource->link): ?>
                            <p class="row-fluid margintop05">
                                <a href="<?php echo $link; ?>"><?php echo $link; ?></a><br/>
                            </p>
                        <?php endif; ?>


                        <?php if ($resource->created_date): ?>
                            <div id="conteudo-loop-data" class="row-fluid margintop05">
                                <span class="conteudo-loop-data-tit"><?php _e('Resource added in','direve'); ?>:</span>
                               <?php echo print_formated_date($resource->created_date); ?>                           
                            </div>
                        <?php endif; ?>

                        <?php if ($resource->source_language_display): ?>
                            <div id="conteudo-loop-idiomas" class="row-fluid">
                               <span class="conteudo-loop-idiomas-tit"><?php _e('Available languages','direve'); ?>:</span>
                               <?php print_lang_value($resource->source_language_display, $site_language); ?>
                            </div>
                        <?php endif; ?>                            

                        <?php if ($resource->descriptor || $resource->keyword ) : ?>
                            <div id="conteudo-loop-tags" class="row-fluid margintop10">
                                <i class="ico-tags"> </i>   
                                    <?php 
                                        $descriptors = (array)$resource->descriptor;
                                        $keywords = (array)$resource->keyword;
                                    ?>                               
                                    <?php echo implode(", ", array_merge( $descriptors, $keywords) ); ?>
                              </div>
                        <?php endif; ?>

                        <footer class="row-fluid margintop05">
                            <ul class="conteudo-loop-icons">
                                <li class="conteudo-loop-icons-li">
                                    <i class="ico-compartilhar"></i>
                                    <!-- AddThis Button BEGIN -->
                                    <a class="addthis_button" href="http://www.addthis.com/bookmark.php?v=300&amp;pubid=<?php echo $direve_addthis_id; ?>"><?php _e('Share','direve'); ?></a>
                                    <script type="text/javascript">var addthis_config = {"data_track_addressbar":true};</script>
                                    <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=<?php echo $direve_addthis_id; ?>"></script>
                                    <!-- AddThis Button END -->
                                    <!--
                                    <a href="#">                                       
                                        <?php _e('Share','direve'); ?>
                                    </a>
                                    -->
                                </li>

                                <li class="conteudo-loop-icons-li">
                                    <span class="sugerir-tag-open">
                                        <i class="ico-tag"></i>
                                        <?php _e('Suggest tag','direve'); ?>
                                    </span>
                                    <div class="sugerir-tag">
                                        <div class="sugerir-form">
                                            <form action="<?php echo $direve_service_url ?>suggest-tag" id="tagForm">
                                                <input type="hidden" name="resource_id" value="<?php echo $resource_id; ?>"/>
                                                <div class="sugerir-tag-close">[X]</div>
                                                <span class="sugerir-tag-tit"><?php _e('Suggestions','direve'); ?></span>
                                                    
                                                <div class="row-fluid margintop05 marginbottom10">
                                                    <input type="text" name="txtTag" class="sugerir-tag-input" id="txtTag">
                                                </div>                                                

                                                <div class="row-fluid margintop05">
                                                    <span class="sugerir-tag-separator"><?php _e('Separated by comma','direve'); ?></span>
                                                    <button class="pull-right colaboracion-enviar"><?php _e('Send','direve'); ?></button>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="sugerir-tag-result">
                                            <div class="sugerir-tag-close">[X]</div>
                                            <div id="result-ok">
                                                <?php _e('Thank you for your suggestion.','direve'); ?>
                                            </div>
                                            <div id="result-problem">
                                                <?php _e('Communication problem. Please try again later.','direve'); ?>
                                            </div>                                            
                                        </div>
                                    </div>
                                </li>

                                <!--li class="conteudo-loop-icons-li">
                                    <span class="reportar-erro-open">
                                        <i class="ico-reportar"></i>
                                        <?php _e('Report error','direve'); ?>
                                    </span>

                                    <div class="reportar-erro"> 
                                        <form action="">
                                            <div class="reportar-erro-close">[X]</div>
                                            <span class="reportar-erro-tit">Motivo</span>

                                            <div class="row-fluid margintop05">
                                                <input type="radio" name="txtMotivo" id="txtMotivo1">
                                                <label class="reportar-erro-lbl" for="txtMotivo1">Motivo 01</label>
                                            </div>

                                            <div class="row-fluid">
                                                <input type="radio" name="txtMotivo" id="txtMotivo2">
                                                <label class="reportar-erro-lbl" for="txtMotivo2">Motivo 02</label>
                                            </div>

                                            <div class="row-fluid">
                                                <input type="radio" name="txtMotivo" id="txtMotivo3">
                                                <label class="reportar-erro-lbl" for="txtMotivo3">Motivo 03</label>
                                            </div>

                                            <div class="row-fluid margintop05">
                                                <textarea name="txtArea" id="txtArea" class="reportar-erro-area" cols="20" rows="2"></textarea>
                                            </div>

                                            <div class="row-fluid border-bottom2"></div>

                                            <span class="reportar-erro-tit margintop05">Nueva URL (Opcional)</span>
                                            <div class="row-fluid margintop05">
                                                <textarea name="txtUrl" id="txtUrl" class="reportar-erro-area" cols="20" rows="2"></textarea>
                                            </div>

                                            <div class="row-fluid border-bottom2"></div>

                                            <div class="row-fluid margintop05">
                                                <button class="pull-right reportar-erro-btn">Enviar</button>
                                            </div>
                                        </form>
                                    </div>
                                </li-->
                            </ul>
                        </footer>

                        <?php if ($direve_disqus_id != '') :?>
                            <div id="disqus_thread" class="row-fluid margintop25"></div>
                            <script type="text/javascript">
                                /* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
                                var disqus_shortname = '<?php echo $direve_disqus_id; ?>'; // required: replace example with your forum shortname

                                /* * * DON'T EDIT BELOW THIS LINE * * */
                                (function() {
                                    var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
                                    dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
                                    (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
                                })();
                            </script>
                            <noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
                            <a href="http://disqus.com" class="dsq-brlink">comments powered by <span class="logo-disqus">Disqus</span></a>
                        <?php endif; ?>
    
                    </article>
                </div>
            </section>

            <aside id="sidebar">
                <section class="row-fluid marginbottom25 widget_categories">
                    <header class="row-fluid border-bottom marginbottom15">
                        <h1 class="h1-header"><?php _e('Related','direve'); ?></h1>
                    </header>
                    <ul>
                        <?php foreach ( $related_list as $related) { ?>
                            <li class="cat-item">
                                <a href="<?php echo home_url($eve_plugin_slug); ?>/resource/<?php echo $related->django_id; ?>"><?php echo $related->title ?></a>
                            </li>
                        <?php } ?>
                    </ul>
                </section>
            </aside>

        </div>
    </div>

<?php get_footer();?>
