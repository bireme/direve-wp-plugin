<?php
/*
Template Name: DirEve Detail
*/

global $direve_service_url, $direve_plugin_slug, $similar_docs_url;

$direve_config = get_option('direve_config');
$event_id = sanitize_text_field($_GET['id']);

$site_language = strtolower(get_bloginfo('language'));
$lang_dir = substr($site_language,0,2);

$direve_disqus_id  = $direve_config['disqus_shortname'];
$direve_addthis_id = $direve_config['addthis_profile_id'];
$direve_service_request = $direve_service_url . 'api/event/search/?id=events.event.' .$event_id . '&op=related&lang=' . $lang_dir;

$event_modality = array(
    'in-person' => __('In-person', 'direve'),
    'hybrid' => __('Hybrid', 'direve'),
    'online' => __('Online', 'direve')
);

$response = @file_get_contents($direve_service_request);

if ($response){
    $response_json = json_decode($response);

    $resource = $response_json->diaServerResponse[0]->match->docs[0];
    $related_list = $response_json->diaServerResponse[0]->response->docs;

    // echo "<pre>"; print_r($resource); echo "</pre>"; die();

    // create param to find similars
    $similar_text = $resource->title;
    if (isset($resource->mj)){
        $similar_text .= ' ' . implode(' ', $resource->mj);
    }

    $similar_docs_url = $similar_docs_url . '?adhocSimilarDocs=' . urlencode($similar_text);
    $similar_docs_request = ( $direve_config['default_filter_db'] ) ? $similar_docs_url . '&sources=' . $direve_config['default_filter_db'] : $similar_docs_url;
    $similar_query = urlencode($similar_docs_request);
    $related_query = urlencode($similar_docs_url);
}

?>

<?php get_header('direve'); ?>

<div id="content" class="row-fluid">
    <div class="ajusta2">
        <div class="row-fluid breadcrumb">
            <a href="<?php echo real_site_url(); ?>"><?php _e('Home','direve'); ?></a> >
            <a href="<?php echo real_site_url($direve_plugin_slug); ?>"><?php _e('Events Directory', 'direve') ?> </a> >
            <?php _e('Resource','direve'); ?>
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

                    <?php if ($resource->event_modality): ?>
                        <div class="row-fluid">
                            <?php echo __('Event','direve') . ' ' . $event_modality[$resource->event_modality[0]]; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($resource->address[0]): ?>
                        <div class="row-fluid">
                            <?php
                                $address =  $resource->address[0];

                                if ( preg_match('/, undefined$/', $address) ) {
                                    $address = preg_replace('/, undefined$/', '', $address);
                                }

                                echo $address;
                            ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($resource->city || $resource->country): ?>
                        <div class="row-fluid">
                            <?php if ( $resource->city ) : ?>
                                <?php echo $resource->city . ' - ' . $resource->country; ?>
                            <?php else : ?>
                                <?php echo $resource->country ;?>
                            <?php endif; ?>
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
                            <?php echo nl2br($resource->contact_info); ?>
                        </p>
                    <?php endif; ?>

                    <?php if ($resource->official_language_display): ?>
                        <div id="conteudo-loop-idiomas" class="row-fluid">
                           <span class="conteudo-loop-idiomas-tit"><?php _e('Available languages','direve'); ?>:</span>
                           <?php print_lang_value($resource->official_language_display, $site_language); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($resource->contact_email): ?>
                        <div id="conteudo-loop-idiomas" class="row-fluid">
                           <span class="conteudo-loop-idiomas-tit"><?php _e('Contact','direve'); ?>:</span>
                           <?php echo $resource->contact_email; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($resource->observations): ?>
                        <div id="conteudo-loop-idiomas" class="row-fluid">
                           <span class="conteudo-loop-idiomas-tit"><?php _e('Observations','direve'); ?>:</span>
                           <?php echo nl2br($resource->observations[0]); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($resource->target_groups): ?>
                        <div id="conteudo-loop-idiomas" class="row-fluid">
                           <span class="conteudo-loop-idiomas-tit"><?php _e('Target groups','direve'); ?>:</span>
                           <?php echo nl2br($resource->target_groups[0]); ?>
                        </div>
                    <?php endif; ?> 

                    <?php if ($resource->descriptor || $resource->keyword ) : ?>
                        <div id="conteudo-loop-tags" class="row-fluid margintop10">
                            <?php
                                $descriptors = (array)$resource->descriptor;
                                $keywords = (array)$resource->keyword;
                            ?>
                            <i class="ico-tags"> </i> <?php echo implode(", ", array_merge( $descriptors, $keywords) ); ?>
                          </div>
                    <?php endif; ?>

                    <footer class="row-fluid margintop05">
                        <ul class="conteudo-loop-icons">
                            <li class="conteudo-loop-icons-li">
                                <i class="ico-compartilhar"> </i>
                                <!-- AddThis Button BEGIN -->
                                <a class="addthis_button" href="http://www.addthis.com/bookmark.php?v=300&amp;pubid=<?php echo $direve_addthis_id; ?>"><?php _e('Share','direve'); ?></a>
                                <script type="text/javascript">var addthis_config = {"data_track_addressbar":false};</script>
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
                                            <input type="hidden" name="resource_id" value="<?php echo $event_id; ?>"/>
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
                                    <div class="error-report-result">
                                        <div class="reportar-erro-close">[X]</div>
                                        <div id="result-ok">
                                            <?php _e('Thank you for your report.','direve'); ?>
                                        </div>
                                        <div id="result-problem">
                                            <?php _e('Communication problem. Please try again later.','direve'); ?>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="conteudo-loop-icons-li">
                                <!-- AddThisEvent Button BEGIN -->
                                <script type="text/javascript" src="https://addthisevent.com/libs/1.5.8/ate.min.js"></script>
                                <!-- AddThisEvent Button Settings -->
                                <script type="text/javascript">
                                    addthisevent.settings({
                                        mouse     : false,
                                        css       : true,
                                        outlook   : {show:true, text:"Outlook Calendar"},
                                        google    : {show:true, text:"Google Calendar"},
                                        yahoo     : {show:true, text:"Yahoo Calendar"},
                                        hotmail   : {show:true, text:"Hotmail Calendar"},
                                        ical      : {show:true, text:"iCal Calendar"},
                                        facebook  : {show:true, text:"Facebook Event"}
                                    });
                                </script>
                                <a href="#" title="Add to Calendar" class="addthisevent">
                                    <?php _e('Add to Calendar','direve'); ?>
                                    <span class="_start"><?php echo date("d-m-Y", strtotime($resource->start_date)); ?></span>
                                    <span class="_end"><?php echo date("d-m-Y", strtotime($resource->end_date)); ?></span>
                                    <span class="_zonecode"></span>
                                    <span class="_summary"><?php echo $resource->title; ?></span>
                                    <span class="_description"></span>
                                    <?php
                                        $location = "";
                                        if ($address) {
                                            $location = $address;
                                        }
                                        if($resource->city) {
                                            if(empty($location))
                                                $location = $resource->city;
                                            else
                                                $location .= ', ' . $resource->city;
                                        }
                                        if($resource->country){
                                            if(empty($location))
                                                $location = $resource->country;
                                            else
                                                $location .=  ', ' . $resource->country;
                                        }
                                    ?>
                                    <span class="_location"><?php echo $location; ?></span>
                                    <span class="_organizer"></span>
                                    <span class="_organizer_email"><?php echo $resource->contact_email; ?></span>
                                    <span class="_facebook_event"></span>
                                    <span class="_all_day_event">true</span>
                                    <span class="_date_format">DD/MM/YYYY</span>
                                </a>
                                <!-- AddThisEvent Button END -->
                            </li>
                        </ul>
                    </footer>

                    <?php
                        if ($address) {
                            if($resource->city)
                                $address .=  ', ' . $resource->city;
                            if($resource->country)
                                $address .=  ', ' . $resource->country;
                        }
                    ?>
                    <div class="map">
                        <iframe id="gmap_canvas" src="https://maps.google.com/maps?q=<?php echo $address; ?>&amp;t=&amp;z=13&amp;ie=UTF8&amp;iwloc=&amp;output=embed" width="595" height="385" frameborder="0" marginwidth="0" marginheight="0" scrolling="no"></iframe>
                    </div>

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
            <div class="row-fluid">
                <header class="row-fluid border-bottom marginbottom15">
                    <h1 class="h1-header"><?php _e('More related','direve'); ?></h1>
                </header>
                <div id="loader" class="loader" style="display: inline-block;"></div>
            </div>
            <div class="row-fluid">
                <div id="async" class="related-docs">

                </div>
            </div>
<?php
$sources = ( $direve_config['extra_filter_db'] ) ? $direve_config['extra_filter_db'] : '';
$url = DIREVE_PLUGIN_URL.'template/related.php?query='.$related_query.'&sources='.$sources.'&lang='.$lang_dir;
?>
<script type="text/javascript">
    show_related("<?php echo $url; ?>");
</script>
        </section>
        <aside id="sidebar">
            <section class="header-search">
                <?php if ($direve_config['show_form']) : ?>
                <form role="search" method="get" id="searchform" action="<?php echo real_site_url($direve_plugin_slug); ?>">
                    <input value="<?php echo $query ?>" name="q" class="input-search" id="s" type="text" placeholder="<?php _e('Search', 'direve'); ?>...">
                    <input id="searchsubmit" value="<?php _e('Search', 'direve'); ?>" type="submit">
                </form>
                <?php endif; ?>
            </section>
            <a href="<?php echo real_site_url($direve_plugin_slug); ?>suggest-event" class="header-colabore"><?php _e('Suggest a event','direve'); ?></a>
            <section class="row-fluid marginbottom25 widget_categories">
                <header class="row-fluid border-bottom marginbottom15">
                    <h1 class="h1-header"><?php _e('Related','direve'); ?></h1>
                </header>
            <ul id="ajax">

            </ul>
            </section>
<?php
$url = DIREVE_PLUGIN_URL.'template/similar.php?query='.$similar_query.'&lang='.$lang_dir;
?>
<script type="text/javascript">
    show_similar("<?php echo $url; ?>");
</script>
        </aside>
    </div>
</div>

<?php get_footer();?>
