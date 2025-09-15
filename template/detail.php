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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

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
                                <?php echo $resource->city . ' - ';
                                direve_print_lang_value($resource->country, $site_language);?>
                            <?php else : ?>
                                <?php direve_print_lang_value($resource->country, $site_language);?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div id="conteudo-loop-data" class="row-fluid margintop05">
                        <span class="conteudo-loop-data-tit"><?php _e('Date','direve'); ?>:</span>
                        <?php echo format_date($resource->start_date); $data = format_date($resource->start_date);?> -
                        <?php echo format_date($resource->end_date); $datafim = format_date($resource->end_date);?>
                    </div>

                    <?php if ($resource->link[0]): ?>
                        <p class="row-fluid margintop05 event-link">
                            <a href="<?php echo $resource->link[0]; ?>" target="_blank"><?php echo $resource->link[0]; ?></a><br/>
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
                           <?php direve_print_lang_value($resource->official_language_display, $site_language); ?>
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
                        <!--
                        <ul class="conteudo-loop-icons">
                            <li class="conteudo-loop-icons-li">
                                <i class="ico-compartilhar"> </i>
                                <a class="addthis_button" href="http://www.addthis.com/bookmark.php?v=300&amp;pubid=<?php echo $direve_addthis_id; ?>"><?php _e('Share','direve'); ?></a>
                                <script type="text/javascript">var addthis_config = {"data_track_addressbar":false};</script>
                                <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=<?php echo $direve_addthis_id; ?>"></script>
                                <!-- AddThis Button END 
                            </li>-->
                    <footer class="row-fluid margintop15">
                        <ul class="conteudo-loop-icons">                  
                            <li class="conteudo-loop-icons-li">
                                <span class="reportar-erro-open">
<i class="fa-solid fa-triangle-exclamation"></i>
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
                                                <?php
                        if ($address) {
                            if($resource->city)
                                $address .=  ', ' . $resource->city;
                            if($resource->country)
                                $address .=  ', ' . $resource->country;
                        }
                    ?>
                            <li class="conteudo-loop-icons-li">
                                <i class=""></i>
                                <!-- AddThis Button BEGIN -->
       
                                    <a class="addthis_button" href="https://www.google.com/calendar/render?action=TEMPLATE&text=<?=$resource->title;?>&dates=<?=$data;?>/<?=$datafim;?>&details=Descrição+do+evento&location=<?=$address;?>" target="_blank">
  + Adicionar ao Google Calendar
</a>
                            </li>
                                      <li class="conteudo-loop-icons-li">




<!-- Badges -->
 <!--
<span class="badge facebook"><i class="fa-brands fa-facebook-f"></i></span>
<span class="badge instagram"><i class="fa-brands fa-instagram"></i></span>
<span class="badge x"><i class="fa-brands fa-x-twitter"></i></span>
<span class="badge linkedin"><i class="fa-brands fa-linkedin-in" aria-hidden="true"></i></span>
<span class="badge whatsapp"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i></span>
----->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
.badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 20px;              /* tamanho do círculo */
  height: 20px;
  border-radius: 50%;
  color: #fff !important;
  margin: 0.3rem;
  font-size: 16px;  
  background: #ddd;  
  text-decoration: none !important;      /* tamanho do ícone */
}

.badge.facebook:hover  { background: #1877F2; }
.badge.instagram:hover { background: #E1306C; }
.badge.whatsapp:hover { background: #4FCE5D; }
.badge.copy:hover { background: #1877F2; }
.badge.x:hover         { background: #000; }
</style>


<?php
$urlcompartilhamento = real_site_url($direve_plugin_slug) . 'resource/?id=' . $resource->django_id;
//$urlcompartilhamento = 'https://economia.saude.bvs.br/direve/' . 'resource/?id=' . $resource->django_id;
//$urlcompartilhamento = rawurlencode($urlcompartilhamento);
?>

<meta property='og:url' content='//www.example.com/URL of the article'/>

<?php

$url = $urlcompartilhamento ?? (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

// Encode seguro: remove encode anterior (se houver) e aplica rawurlencode uma vez
function li_safe_encode($u){ return rawurlencode(rawurldecode($u)); }
$enc = li_safe_encode($url);

// Endpoints
$li_primary = "https://www.linkedin.com/sharing/share-offsite/?url={$enc}";
$li_fallback = "https://www.linkedin.com/shareArticle?mini=true&url={$enc}";

?>
<!-- Facebook -->
<a class="badge facebook"
   href="https://www.facebook.com/sharer/sharer.php?u=<?=urlencode($urlcompartilhamento)?>&quote=<?=urlencode('Confira isso!')?>"
   target="_blank" rel="noopener noreferrer">
  <i class="fa-brands fa-facebook-f"></i>
</a>

<!-- Instagram (não há compartilhamento de link) --
<a class="badge instagram"
   href="https://www.instagram.com/"
   target="_blank" rel="noopener noreferrer">
  <i class="fa-brands fa-instagram"></i>
</a>-->

<!-- X (Twitter) -->
<a class="badge x"
   href="https://twitter.com/intent/tweet?url=<?=urlencode($urlcompartilhamento)?>&text=<?=urlencode('Confira isso!')?>"
   target="_blank" rel="noopener noreferrer">
  <i class="fa-brands fa-x-twitter"></i>
</a>

<a class="badge linkedin"
   href="javascript:void(0);"
   onclick="navigator.clipboard.writeText(window.location.href).then(()=>alert('Link da página copiado!'))">
  <i class="fa-brands fa-linkedin-in"></i>
</a>

<!-- WhatsApp (funciona no mobile e desktop web) -->
<a class="badge whatsapp"
   href="https://wa.me/?text=<?=urlencode('Confira isso: '.$urlcompartilhamento)?>"
   target="_blank" rel="noopener noreferrer">
  <i class="fa-brands fa-whatsapp" aria-hidden="true"></i>
</a>

<a class="badge copy" 
   href="javascript:void(0);"
   onclick="navigator.clipboard.writeText(window.location.href).then(()=>alert('Link da página copiado!'))">
  <i class="fa-regular fa-copy"></i>
</a>

<!--
<a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo rawurlencode( get_permalink() ); ?>" target="_blank" rel="noopener">Compartilhar no LinkedIn</a>

<a href="https://www.linkedin.com/sharing/share-offsite/?url=" onclick="this.href = this.href + encodeURIComponent(location.href)" target="_blank" rel="noopener">Compartilhar no LinkedIn</a>

                                <i class="ico-compartilhar"> </i>
                                <a class="addthis_button" href="http://www.addthis.com/bookmark.php?v=300&amp;pubid=<?php echo $direve_addthis_id; ?>"><?php _e('Share','direve'); ?></a>
                                <script type="text/javascript">var addthis_config = {"data_track_addressbar":false};</script>
                                <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=<?php echo $direve_addthis_id; ?>"></script>
                                <!-- AddThis Button END -->
                            </li>
                            <!--------
                            <li class="conteudo-loop-icons-li">
                                <script type="text/javascript" src="https://addthisevent.com/libs/1.5.8/ate.min.js"></script>
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

                                <!---------
                                <a href="https://www.google.com/calendar/render?action=TEMPLATE&text=Nome+do+Evento&dates=20250620T140000Z/20250620T150000Z&details=Descrição+do+evento&location=Local+do+evento" target="_blank">
  <button>Adicionar ao Google Calendar</button>
</a>

<a href="https://outlook.live.com/calendar/0/deeplink/compose?path=/calendar/action/compose&subject=Reunião%20de%20teste&body=Descrição%20do%20evento&location=Sala%20A&startdt=2025-06-20T14:00:00&enddt=2025-06-20T15:00:00" target="_blank">
  <button>Adicionar ao Outlook</button>
</a>---

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
                                    <?php
                                $data =  date("Ymd", strtotime($resource->start_date));
                                $datafim = date("Ymd", strtotime($resource->start_date));
                                ?>
<!---
    <a href="https://www.google.com/calendar/render?action=TEMPLATE&text=<?=$resource->title;?>&dates=<?=$data;?>T140000Z/<?=$datafim;?>T150000Z&details=Descrição+do+evento&location=<?=$location;?>" target="_blank">
  <button>Adicionar ao Google Calendar</button>
</a>----->



                            </li>
                        </ul>
                    </footer>


                    <!---------if para verificar se o evenot é online ou não ---------->
                    <?php if($event_modality[$resource->event_modality[0]] == 'Online'){

                    }else{ ?>
                    <div class="map">
                        <iframe id="gmap_canvas" src="https://maps.google.com/maps?q=<?php echo $address; ?>&amp;t=&amp;z=13&amp;ie=UTF8&amp;iwloc=&amp;output=embed" width="595" height="385" frameborder="0" marginwidth="0" marginheight="0" scrolling="no"></iframe>
                    </div>
                    <?php } ?>

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
<script>
function copiarLink() {
  // pega a URL atual
  const link = window.location.href;
  
  // copia para a área de transferência
  navigator.clipboard.writeText(link).then(() => {
    alert("Link copiado: " + link);
  }).catch(err => {
    console.error("Erro ao copiar: ", err);
  });
}
</script>
        </aside>
    </div>
</div>

<?php get_footer();?>
