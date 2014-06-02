<?php
/*
Template Name: DirEve Detail
*/

$direve_config = get_option('direve_config');

$request_uri = $_SERVER["REQUEST_URI"];
$request_parts = explode('/', $request_uri);
$event_id = end($request_parts);

$site_language = strtolower(get_bloginfo('language'));
$lang_dir = substr($site_language,0,2);

$direve_service_url = $direve_config['service_url'];
$direve_disqus_id  = $direve_config['disqus_shortname'];
$direve_addthis_id = $direve_config['addthis_profile_id'];
$direve_service_request = $direve_service_url . 'api/event/search/?id=events.event.' .$event_id . '&op=related&lang=' . $lang_dir;

$response = @file_get_contents($direve_service_request);

if ($response){
    $response_json = json_decode($response);

    $resource = $response_json->diaServerResponse[0]->match->docs[0];
    $related_list = $response_json->diaServerResponse[0]->response->docs;
}

?>

<?php get_header('direve'); ?>

<div id="content" class="row-fluid">
        <div class="ajusta2">
            <div class="row-fluid breadcrumb">
                <a href="<?php echo real_site_url(); ?>"><?php _e('Home','direve'); ?></a> > 
                <a href="<?php echo real_site_url($eve_plugin_slug); ?>"><?php _e('Events Directory', 'direve') ?> </a> > 
                <?php _e('Resource','direve'); ?>
            </div>
            <!-- div class="row-fluid">
                <section class="header-search">
                    <?php if ($direve_config['show_form']) : ?>
                        <form role="search" method="get" id="searchform" action="<?php echo real_site_url($eve_plugin_slug); ?>">
                            <input value="<?php echo $query ?>" name="q" class="input-search" id="s" type="text" placeholder="<?php _e('Search', 'direve'); ?>...">
                            <input id="searchsubmit" value="<?php _e('Search', 'direve'); ?>" type="submit">
                        </form>
                    <?php endif; ?>
                </section>
                <div class="pull-right">
                    <a href="<?php echo real_site_url($eve_plugin_slug); ?>suggest-event">
                        <img class="header-colabore" src="<?php echo DIREVE_PLUGIN_URL . 'template/images/' . $lang_dir .'/indique.png' ?>" title="<?php _e('Suggest a site','direve'); ?>"/>
                    </a>
                </div>   
            </div-->

            <section id="conteudo">
                <header class="row-fluid border-bottom">
                    <h1 class="h1-header"><?php echo $resource->title; ?></h1>
                </header>
                <div class="row-fluid">
                    <article class="conteudo-loop">
                        <div class="conteudo-loop-rates">
                            <div class="star" data-score="1"></div>
                        </div>
                        
                        <?php if ($resource->city || $resource->country): ?>
                            <div class="row-fluid">
                                <?php echo $resource->city . ' ' . $resource->country ;?>
                            </div>
                        <?php endif; ?>

                        <div id="conteudo-loop-data" class="row-fluid margintop05">
                            <span class="conteudo-loop-data-tit"><?php _e('Date','direve'); ?>:</span>
                            <?php echo format_date($resource->start_date); ?> - 
                            <?php echo format_date($resource->end_date); ?>
                        </div>

                        <?php if ($resource->link[0]): ?>
                            <p class="row-fluid margintop05">
                                <a href="<?php echo $resource->link[0]; ?>"><?php echo $resource->link[0]; ?></a><br/>
                            </p>
                        <?php endif; ?>

                        <?php if ($resource->contact_info): ?>
                            <p class="row-fluid margintop05">
                                <?php echo $resource->contact_info; ?>
                            </p>
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
                                    <i class="ico-compartilhar"> </i>
                                    <!-- AddThis Button BEGIN -->
                                    <a class="addthis_button" href="http://www.addthis.com/bookmark.php?v=300&amp;pubid=<?php echo $direve_addthis_id; ?>"><?php _e('Share','direve'); ?></a>
                                    <script type="text/javascript">var addthis_config = {"data_track_addressbar":true};</script>
                                    <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=<?php echo $direve_addthis_id; ?>"></script>
                                    <!-- AddThis Button END -->                                    
                                </li>
                                <li class="conteudo-loop-icons-li">
                                    <span class="reportar-erro-open">
                                        <i class="ico-reportar"></i>
                                        <?php _e('Report error','direve'); ?>
                                    </span>

                                    <div class="reportar-erro"> 
                                        <div class="erro-form">
                                            <form action="<?php echo $direve_service_url ?>report-error" id="reportErrorForm">
                                                <input type="hidden" name="resource_type" value="event"/>
                                                <input type="hidden" name="resource_id" value="<?php echo $resource_id; ?>"/>
                                                <div class="reportar-erro-close">[X]</div>
                                                <span class="reportar-erro-tit"><?php _e('Reason','direve'); ?></span>

                                                <div class="row-fluid margintop05">
                                                    <input type="radio" name="code" id="txtMotivo1" value="0">
                                                    <label class="reportar-erro-lbl" for="txtMotivo1"><?php _e('Invalid Link','direve'); ?></label>
                                                </div>

                                                <div class="row-fluid">
                                                    <input type="radio" name="code" id="txtMotivo2" value="1">
                                                    <label class="reportar-erro-lbl" for="txtMotivo2"><?php _e('Bad content','direve'); ?></label>
                                                </div>

                                                <div class="row-fluid">
                                                    <input type="radio" name="code" id="txtMotivo3" value="3">
                                                    <label class="reportar-erro-lbl" for="txtMotivo3"><?php _e('Other','direve'); ?></label>
                                                </div>

                                                <div class="row-fluid margintop05">
                                                    <textarea name="description" id="txtArea" class="reportar-erro-area" cols="20" rows="2"></textarea>
                                                </div>

                                                <div class="row-fluid border-bottom2"></div>

                                                <span class="reportar-erro-tit margintop05"><?php _e('New Link (Optional)','direve'); ?></span>
                                                <div class="row-fluid margintop05">
                                                    <textarea name="new_link" id="txtUrl" class="reportar-erro-area" cols="20" rows="2"></textarea>
                                                </div>

                                                <div class="row-fluid border-bottom2"></div>

                                                <div class="row-fluid margintop05">
                                                    <button class="pull-right reportar-erro-btn"><?php _e('Send','direve'); ?></button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </li>                                
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
                	<section class="header-search">
                        	<?php if ($direve_config['show_form']) : ?>
                        	<form role="search" method="get" id="searchform" action="<?php echo real_site_url($eve_plugin_slug); ?>">
                	                <input value="<?php echo $query ?>" name="q" class="input-search" id="s" type="text" placeholder="<?php _e('Search', 'direve'); ?>...">
                       		        <input id="searchsubmit" value="<?php _e('Search', 'direve'); ?>" type="submit">
                        	</form>
                        	<?php endif; ?>
                	</section>
                	<a href="<?php echo real_site_url($eve_plugin_slug); ?>suggest-event" class="header-colabore"><?php _e('Suggest a site','direve'); ?></a>
        	</aside>
        </div>
    </div>

<?php get_footer();?>
