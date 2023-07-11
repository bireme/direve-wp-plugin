<?php
/*
Template Name: DirEve Home
*/

require_once(DIREVE_PLUGIN_PATH . '/lib/Paginator.php');

global $direve_service_url, $direve_plugin_slug;

$direve_config = get_option('direve_config');
$direve_initial_filter = $direve_config['initial_filter'];

$site_language = strtolower(get_bloginfo('language'));
$lang_dir = substr($site_language,0,2);

$query = ( isset($_GET['s']) ? sanitize_text_field($_GET['s']) : sanitize_text_field($_GET['q']) );
$query = stripslashes($query);
$sanitize_user_filter = sanitize_text_field($_GET['filter']);
$user_filter = stripslashes($sanitize_user_filter);
$page = ( isset($_GET['page']) ? sanitize_text_field($_GET['page']) : 1 );
$total = 0;
$count = 10;
$filter = '';

$event_modality = array(
    'in-person' => __('In-person', 'direve'),
    'hybrid' => __('Hybrid', 'direve'),
    'online' => __('Online', 'direve')
);

if ($direve_initial_filter != ''){
    if ($user_filter != ''){
        $filter = $direve_initial_filter . ' AND ' . $user_filter;
    }else{
        $filter = $direve_initial_filter;
    }
}else{
    $filter = $user_filter;
}
$start = ($page * $count) - $count;

if ($query != ''){
    $direve_search = $direve_service_url . 'api/event/search/?q=' . urlencode($query) . '&fq=' . urlencode($filter) . '&start=' . $start . '&lang=' . $lang_dir;
}else{
    $direve_next_events = $direve_service_url . 'api/event/next/?fq=' . urlencode($filter) . '&lang=' . $lang_dir . '&count=20';
    $direve_search = $direve_service_url . 'api/event/search/?fq=' . urlencode($filter) . '&start=' . $start . '&lang=' . $lang_dir;
}

// echo "<pre>"; print_r($direve_search); echo "</pre>"; die();
// echo "<pre>"; print_r($direve_next_events); echo "</pre>"; die();

$response = @file_get_contents($direve_search);
if ($response){
    $response_json = json_decode($response);
    // echo "<pre>"; print_r($response_json); echo "</pre>"; die();
    $total = $response_json->diaServerResponse[0]->response->numFound;
    $start = $response_json->diaServerResponse[0]->response->start;
    if ($query != '' || $user_filter != ''){
        $event_list = $response_json->diaServerResponse[0]->response->docs;
    }else{
        $response_next_events =  @file_get_contents($direve_next_events);
        $response_next_events_json = json_decode($response_next_events);
        $event_list = $response_next_events_json->diaServerResponse[0]->response->docs;
    }

    $descriptor_list = $response_json->diaServerResponse[0]->facet_counts->facet_fields->descriptor_filter;
    $event_type_list = $response_json->diaServerResponse[0]->facet_counts->facet_fields->event_type;
    $thematic_area_list = $response_json->diaServerResponse[0]->facet_counts->facet_fields->thematic_area_display;
    $publication_year_list = $response_json->diaServerResponse[0]->facet_counts->facet_fields->publication_year;
    usort($publication_year_list, fn($a, $b) => $b[0] <=> $a[0]);

    $facet_fields = $response_json->diaServerResponse[0]->facet_counts->facet_fields;
    // echo "<pre>"; print_r($facet_fields); echo "</pre>"; die();
}

$page_url_params = real_site_url($direve_plugin_slug) . '?q=' . urlencode($query)  . '&filter=' . urlencode($filter);
$feed_url = real_site_url($direve_plugin_slug) . 'events-feed?q=' . urlencode($query) . '&filter=' . urlencode($user_filter);

$pages = new Paginator($total, $start);
$pages->paginate($page_url_params);

?>

<?php get_header('direve');?>
    <div id="content" class="row-fluid">
        <div class="ajusta2">
            <div class="row-fluid breadcrumb">
                <a href="<?php echo real_site_url(); ?>"><?php _e('Home','direve'); ?></a> >
                <?php if ($query != '' || $user_filter != ''): ?>
                    <a href="<?php echo real_site_url($direve_plugin_slug); ?>"><?php _e('Events Directory', 'direve') ?> </a> >
                    <?php _e('Search result', 'direve') ?>
                <?php else: ?>
                    <?php _e('Events Directory', 'direve') ?>
                <?php endif; ?>
            </div>

<?php if ($direve_config['page_layout'] != 'whole_page' || $_GET['q'] != '' || $_GET['filter'] != '' ) : ?>

            <section id="conteudo">
                <?php if ( isset($total) && strval($total) == 0) :?>
                    <h1 class="h1-header"><?php _e('No results found','direve'); ?></h1>
                <?php else :?>
                    <header class="row-fluid border-bottom">
                        <?php if ( ( $query != '' || $user_filter != '' ) && strval($total) > 0) :?>
                            <h1 class="h1-header"><?php _e('Resources found','direve'); ?>: <?php echo $total; ?></h1>
                        <?php else: ?>
                            <div class="list-header">
                                <h1 class="h1-header"><?php _e('Next events','direve'); ?></h1>
                                <small class="small-header"><?php _e('Resources found','direve'); ?>: <?php echo $total; ?></small>
                            </div>
                        <?php endif; ?>
                        <div class="pull-right">
                            <a href="<?php echo $feed_url; ?>" target="blank"><img src="<?php echo DIREVE_PLUGIN_URL; ?>template/images/icon_rss.png" class="rss_feed" ></a>
                        </div>
                        <!-- Not implemented yet
                        <div class="pull-right">
                            <a href="#" class="ico-feeds"></a>
                            <form action="">
                                <select name="txtRegistros" id="txtRegistros" class="select-input-home">
                                    <option value="10 Registros">10 <?php _e('resources', 'direve'); ?></option>`
                                    <option value="20 Registros">20 <?php _e('resources', 'direve'); ?></option>
                                    <option value="50 Registros">50 <?php _e('resources', 'direve'); ?></option>
                                </select>

                                <select name="txtOrder" id="txtOrder" class="select-input-home">
                                    <option value=""><?php _e('Order by', 'direve'); ?></option>
                                    <option value="Mais Recentes"><?php _e('More relevant','direve'); ?></option>
                                    <option value="Mais Lidas"><?php _e('Most recent','direve'); ?></option>
                                </select>
                            </form>
                        </div>
                        -->
                        <?php // if ($query != '' || $user_filter != ''){ echo $pages->display_pages(); } ?>
                    </header>
                    <div class="row-fluid">
                        <?php if ($query == '' && $user_filter == ''): ?>
                            <div class="row-fluid">
                                <h3 class="h3-footer pull-right margintop15"><a href="?q=*"><?php _e('See all events','direve'); ?></a></h3>
                            </div>
                        <?php endif; ?>
                        <?php foreach ( $event_list as $resource) { ?>
                            <article class="conteudo-loop">

                                <div class="row-fluid">
                                    <h2 class="h2-loop-tit"><?php echo $resource->title; ?></h2>
                                </div>
                                <div class="conteudo-loop-rates">
                                    <div class="star" data-score="1"></div>
                                </div>

                                <?php if ($resource->city || $resource->country): ?>
                                    <div class="row-fluid">
                                        <?php if ( $resource->city ) : ?>
                                            <?php echo $resource->city . ' - ' . $resource->country ;?>
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

                                <?php if ($resource->event_modality): ?>
                                    <div class="row-fluid margintop05">
                                        <?php echo __('Event','direve') . ' ' . $event_modality[$resource->event_modality[0]]; ?>
                                    </div>
                                <?php endif; ?>

                                <p class="row-fluid">
                                    <?php echo ( strlen($resource->abstract) > 200 ? substr($resource->abstract,0,200) . '...' : $resource->abstract); ?><br/>
                                    <span class="more"><a href="<?php echo real_site_url($direve_plugin_slug); ?>resource/?id=<?php echo $resource->django_id; ?>"><?php _e('See more details','direve'); ?></a></span>
                                </p>


                                <?php if ($resource->source_language_display): ?>
                                    <div id="conteudo-loop-idiomas" class="row-fluid">
                                        <span class="conteudo-loop-idiomas-tit"><?php _e('Available languages','direve'); ?>:</span>
                                        <?php direve_print_lang_value($resource->source_language_display, $site_language); ?>
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

                            </article>
                        <?php } ?>
                        <?php if ($query == '' && $user_filter == ''): ?>
                            <div class="row-fluid">
                                <h3 class="h3-footer margintop15"><a href="?q=*"><?php _e('See all events','direve'); ?></a></h3>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="row-fluid">
                        <?php if ($query != '' || $user_filter != ''){ echo $pages->display_pages(); } ?>
                    </div>
                <?php endif; ?>
            </section>
            <aside id="sidebar">
                <section class="row-fluid widget wp_calendar">
                    <?php if ($direve_config['show_calendar']) : ?>
                        <div class="widget widget_calendar">
                            <div class="widget_inner">
                                <div id="calendar_wrap">
                                    <div id="wp-calendar"></div>
                                    <!--div class="calendar-pagi">
                                        <ul>
                                            <li class="wp-cal-prev"><a onclick="jQuery.datepicker._adjustDate('#wp-calendar', -1, 'M');"><?php echo __("&laquo; Prev Month", 'wp_calendar'); ?></a></li>
                                            <li class="wp-cal-next"><a onclick="jQuery.datepicker._adjustDate('#wp-calendar', +1, 'M');"><?php echo __("Next Month &raquo;", 'wp_calendar'); ?></a></li>
                                        </ul>
                                    </div-->
                                </div>
                            </div>
                        </div>
                        <!--div class="calendar_wrap_loading calendar_wrap_loading_hide"><img src="<?php echo WP_CALENDAR_PLUGIN_URL; ?>/images/ajax_loader_blue_64.gif"></div-->
                        <!-- <div class="circle_loading hide_circle">
                            <div class="circle"></div>
                            <div class="circle1"></div>
                        </div> -->
                        <div class="spinner"></div>
                    <?php endif; ?>
                </section>
                <section class="header-search">
                    <?php if ($direve_config['show_form']) : ?>
                        <form role="search" method="get" id="searchform" action="<?php echo real_site_url($direve_plugin_slug); ?>">
                            <input value='<?php echo $query; ?>' name="q" class="input-search" id="s" type="text" placeholder="<?php _e('Search', 'direve'); ?>...">
                            <input id="searchsubmit" value="<?php _e('Search', 'direve'); ?>" type="submit">
                        </form>
                    <?php endif; ?>
                </section>
                <a href="<?php echo real_site_url($direve_plugin_slug . '/suggest-event'); ?>" class="header-colabore"><?php _e('Suggest a event','direve'); ?></a>
                <?php if (strval($total) > 0) :?>
                    <?php
                        $order = explode(';', $direve_config['available_filter']);
                        foreach ( $order as $key => $value ) {
                    ?>
                        <?php if ( $value == 'Subjects' ) { ?>
                            <section class="row-fluid marginbottom25 widget_categories">
                                <header class="row-fluid border-bottom marginbottom15">
                                    <h1 class="h1-header"><?php _e('Subjects','direve'); ?></h1>
                                </header>
                                <ul>
                                    <?php foreach ( $descriptor_list as $descriptor) { ?>
                                        <?php
                                            $filter_link = '?';
                                            if ($query != ''){
                                                $filter_link .= 'q=' . $query . '&';
                                            }
                                            $filter_link .= 'filter=descriptor:"' . $descriptor[0] . '"';
                                            if ($user_filter != ''){
                                                $filter_link .= ' AND ' . $user_filter ;
                                            }
                                        ?>
                                        <?php if ( filter_var($descriptor[0], FILTER_VALIDATE_INT) === false ) : ?>
                                            <li class="cat-item">
                                                <a href='<?php echo $filter_link; ?>'><?php echo $descriptor[0]; ?></a>
                                                <span class="cat-item-count"><?php echo $descriptor[1] ?></span>
                                            </li>
                                        <?php endif; ?>
                                    <?php } ?>
                                </ul>
                            </section>
                        <?php } ?>
                        <?php if ( $value == 'Event type' ) { ?>
                            <section class="row-fluid marginbottom25 widget_categories">
                                <header class="row-fluid border-bottom marginbottom15">
                                    <h1 class="h1-header"><?php _e('Event type','direve'); ?></h1>
                                </header>
                                <ul>
                                    <?php foreach ( $event_type_list as $type) { ?>
                                        <?php
                                            $filter_link = '?';
                                            if ($query != ''){
                                                $filter_link .= 'q=' . $query . '&';
                                            }
                                            $filter_link .= 'filter=event_type:"' . $type[0] . '"';
                                            if ($user_filter != ''){
                                                $filter_link .= ' AND ' . $user_filter ;
                                            }
                                        ?>
                                        <li class="cat-item">
                                            <a href='<?php echo $filter_link; ?>'><?php direve_print_lang_value($type[0], $site_language); ?></a>
                                            <span class="cat-item-count"><?php echo $type[1] ?></span>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </section>
                        <?php } ?>
                        <?php if ( $value == 'Thematic area' ) {  ?>
                            <section class="row-fluid marginbottom25 widget_categories">
                                <header class="row-fluid border-bottom marginbottom15">
                                    <h1 class="h1-header"><?php _e('Thematic area','direve'); ?></h1>
                                </header>
                                <ul>
                                    <?php foreach ( $thematic_area_list as $ta ) { ?>
                                        <?php
                                            $filter_link = '?';
                                            if ($query != ''){
                                                $filter_link .= 'q=' . $query . '&';
                                            }
                                            $filter_link .= 'filter=thematic_area_display:"' . $ta[0] . '"';
                                            if ($user_filter != ''){
                                                $filter_link .= ' AND ' . $user_filter ;
                                            }
                                        ?>
                                        <li class="cat-item">
                                            <a href='<?php echo $filter_link; ?>'><?php direve_print_lang_value($ta[0], $site_language); ?></a>
                                            <span class="cat-item-count"><?php echo $ta[1] ?></span>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </section>
                        <?php } ?>
                        <?php if ( $value == 'Publication year' ) {  ?>
                            <section class="row-fluid marginbottom25 widget_categories">
                                <header class="row-fluid border-bottom marginbottom15">
                                    <h1 class="h1-header"><?php _e('Publication year','direve'); ?></h1>
                                </header>
                                <ul>
                                    <?php foreach ( $publication_year_list as $year ) { ?>
                                        <?php
                                            $filter_link = '?';
                                            if ($query != ''){
                                                $filter_link .= 'q=' . $query . '&';
                                            }
                                            $filter_link .= 'filter=publication_year:"' . $year[0] . '"';
                                            if ($user_filter != ''){
                                                $filter_link .= ' AND ' . $user_filter ;
                                            }
                                        ?>
                                        <li class="cat-item">
                                            <a href='<?php echo $filter_link; ?>'><?php echo $year[0]; ?></a>
                                            <span class="cat-item-count"><?php echo $year[1] ?></span>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </section>
                        <?php } ?>
                    <?php
                        }
                endif; ?>
                <?php dynamic_sidebar('direve-home');?>
            </aside>

<?php else : ?>

            <section id="">
                <?php if ( isset($total) && strval($total) == 0) :?>
                    <header class="row-fluid border-bottom">
                        <div class="list-header">
                            <h1 class="h1-header"><?php _e('No results found','direve'); ?></h1>
                            <section class="header-search">
                                <?php if ($direve_config['show_form']) : ?>
                                    <form role="search" method="get" id="searchform" action="<?php echo real_site_url($direve_plugin_slug); ?>">
                                        <input value='<?php echo $query; ?>' name="q" class="input-search" id="s" type="text" placeholder="<?php _e('Search', 'direve'); ?>...">
                                        <input id="searchsubmit" value="<?php _e('Search', 'direve'); ?>" type="submit">
                                    </form>
                                <?php endif; ?>
                            </section>
                        </div>
                    </header>
                <?php else :?>
                    <header class="row-fluid border-bottom">
                        <div class="list-header">
                            <h1 class="h1-header"><?php _e('Next events','direve'); ?></h1>
                            <small class="small-header"><?php _e('Resources found','direve'); ?>: <?php echo $total; ?></small>
                            <section class="header-search">
                                <?php if ($direve_config['show_form']) : ?>
                                    <form role="search" method="get" id="searchform" action="<?php echo real_site_url($direve_plugin_slug); ?>">
                                        <input value='<?php echo $query; ?>' name="q" class="input-search" id="s" type="text" placeholder="<?php _e('Search', 'direve'); ?>...">
                                        <input id="searchsubmit" value="<?php _e('Search', 'direve'); ?>" type="submit">
                                    </form>
                                <?php endif; ?>
                            </section>
                        </div>
                        <div class="pull-right">
                            <a href="<?php echo $feed_url; ?>" target="blank"><img src="<?php echo DIREVE_PLUGIN_URL; ?>template/images/icon_rss.png" class="rss_feed" ></a>
                        </div>
                    </header>
                <?php endif; ?>
            </section>

            <aside id="">
                <a href="<?php echo real_site_url($direve_plugin_slug . '/suggest-event'); ?>" class="header-colabore pull-right"><?php _e('Suggest a event','direve'); ?></a>

                <?php if ( $event_list ) : ?>
                    <?php if ($query == '' && $user_filter == ''): ?>
                        <div class="row-fluid see-all-events">
                            <h3 class="h3-footer"><a href="?q=*"><?php _e('See all events','direve'); ?></a></h3>
                        </div>
                    <?php endif; ?>
                    <div class="row-fluid event-list marginbottom25">
                        <?php foreach ( $event_list as $resource) { ?>
                            <div class="conteudo-loop">

                                <div class="row-fluid">
                                    <h6 class="h6-loop-tit"><?php echo wp_trim_words($resource->title, 8); ?></h6>
                                </div>
                                <div class="conteudo-loop-rates">
                                    <div class="star" data-score="1"></div>
                                </div>

                                <?php if ($resource->city || $resource->country): ?>
                                    <div class="row-fluid">
                                        <?php if ( $resource->city ) : ?>
                                            <?php echo $resource->city . ' - ' . $resource->country; ?>
                                        <?php else : ?>
                                            <?php echo $resource->country ;?>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>

                                <div id="conteudo-loop-data" class="row-fluid">
                                    <span class="conteudo-loop-data-tit"><?php _e('Date','direve'); ?>:</span>
                                    <?php echo format_date($resource->start_date); ?> -
                                    <?php echo format_date($resource->end_date); ?>
                                </div>

                                <?php if ($resource->source_language_display): ?>
                                    <div id="conteudo-loop-idiomas" class="row-fluid">
                                        <span class="conteudo-loop-idiomas-tit"><?php _e('Available languages','direve'); ?>:</span>
                                        <?php direve_print_lang_value($resource->source_language_display, $site_language); ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ($resource->event_modality): ?>
                                    <div class="row-fluid">
                                        <?php echo $event_modality[$resource->event_modality[0]]; ?>
                                    </div>
                                <?php endif; ?>
<!--
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
-->
                                <div class="row-fluid margintop10">
                                    <span class="more"><a href="<?php echo real_site_url($direve_plugin_slug); ?>resource/?id=<?php echo $resource->django_id; ?>"><?php _e('See more details','direve'); ?></a></span>
                                </div>

                            </div>
                        <?php } ?>
                    </div>
                <?php endif; ?>

                <?php if (strval($total) > 0) :?>
                    <?php
                        $order = explode(';', $direve_config['available_filter']);
                        foreach ( $order as $key => $value ) {
                    ?>
                        <?php if ( $value == 'Subjects' ) { ?>
                            <section class="row-fluid widget_categories">
                                <header class="row-fluid border-bottom marginbottom15">
                                    <h1 class="h1-header"><?php _e('Subjects','direve'); ?></h1>
                                </header>
                                <ul class="col3">
                                    <?php foreach ( $descriptor_list as $descriptor) { ?>
                                        <?php
                                            $filter_link = '?';
                                            if ($query != ''){
                                                $filter_link .= 'q=' . $query . '&';
                                            }
                                            $filter_link .= 'filter=descriptor:"' . $descriptor[0] . '"';
                                            if ($user_filter != ''){
                                                $filter_link .= ' AND ' . $user_filter ;
                                            }
                                        ?>
                                        <?php if ( filter_var($descriptor[0], FILTER_VALIDATE_INT) === false ) : ?>
                                            <li class="cat-item">
                                                <a href='<?php echo $filter_link; ?>'><?php echo $descriptor[0]; ?></a>
                                                <span class="cat-item-count"><?php echo $descriptor[1] ?></span>
                                            </li>
                                        <?php endif; ?>
                                    <?php } ?>
                                </ul>
                            </section>
                        <?php } ?>
                        <?php if ( $value == 'Event type' ) {  ?>
                            <section class="row-fluid marginbottom25 widget_categories">
                                <header class="row-fluid border-bottom marginbottom15">
                                    <h1 class="h1-header"><?php _e('Event type','direve'); ?></h1>
                                </header>
                                <ul class="col3">
                                    <?php foreach ( $event_type_list as $type ) { ?>
                                        <?php
                                            $filter_link = '?';
                                            if ($query != ''){
                                                $filter_link .= 'q=' . $query . '&';
                                            }
                                            $filter_link .= 'filter=event_type:"' . $type[0] . '"';
                                            if ($user_filter != ''){
                                                $filter_link .= ' AND ' . $user_filter ;
                                            }
                                        ?>
                                        <li class="cat-item">
                                            <a href='<?php echo $filter_link; ?>'><?php direve_print_lang_value($type[0], $site_language); ?></a>
                                            <span class="cat-item-count"><?php echo $type[1] ?></span>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </section>
                        <?php } ?>
                        <?php if ( $value == 'Thematic area' ) {  ?>
                            <section class="row-fluid marginbottom25 widget_categories">
                                <header class="row-fluid border-bottom marginbottom15">
                                    <h1 class="h1-header"><?php _e('Thematic area','direve'); ?></h1>
                                </header>
                                <ul class="col3">
                                    <?php foreach ( $thematic_area_list as $ta ) { ?>
                                        <?php
                                            $filter_link = '?';
                                            if ($query != ''){
                                                $filter_link .= 'q=' . $query . '&';
                                            }
                                            $filter_link .= 'filter=thematic_area_display:"' . $ta[0] . '"';
                                            if ($user_filter != ''){
                                                $filter_link .= ' AND ' . $user_filter ;
                                            }
                                        ?>
                                        <li class="cat-item">
                                            <a href='<?php echo $filter_link; ?>'><?php direve_print_lang_value($ta[0], $site_language); ?></a>
                                            <span class="cat-item-count"><?php echo $ta[1] ?></span>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </section>
                        <?php } ?>
                        <?php if ( $value == 'Publication year' ) {  ?>
                            <section class="row-fluid marginbottom25 widget_categories">
                                <header class="row-fluid border-bottom marginbottom15">
                                    <h1 class="h1-header"><?php _e('Publication year','direve'); ?></h1>
                                </header>
                                <ul class="col3">
                                    <?php foreach ( $publication_year_list as $year ) { ?>
                                        <?php
                                            $filter_link = '?';
                                            if ($query != ''){
                                                $filter_link .= 'q=' . $query . '&';
                                            }
                                            $filter_link .= 'filter=publication_year:"' . $year[0] . '"';
                                            if ($user_filter != ''){
                                                $filter_link .= ' AND ' . $user_filter ;
                                            }
                                        ?>
                                        <li class="cat-item">
                                            <a href='<?php echo $filter_link; ?>'><?php echo $year[0]; ?></a>
                                            <span class="cat-item-count"><?php echo $year[1] ?></span>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </section>
                        <?php } ?>
                    <?php
                        }
                endif; ?>
                <?php dynamic_sidebar('direve-home');?>
            </aside>

<?php endif; ?>

            <div class="spacer"></div>
        </div>
    </div>
<?php get_footer();?>
