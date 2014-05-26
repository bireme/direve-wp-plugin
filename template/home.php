<?php
/*
Template Name: DirEve Home
*/

require_once(DIREVE_PLUGIN_PATH . '/lib/Paginator.php');

$direve_config = get_option('direve_config');
$direve_service_url = $direve_config['service_url'];
$direve_initial_filter = $direve_config['initial_filter'];

$site_language = strtolower(get_bloginfo('language'));
$lang_dir = substr($site_language,0,2);

$query = ( isset($_GET['s']) ? $_GET['s'] : $_GET['q'] );
$user_filter = stripslashes($_GET['filter']);
$page = ( isset($_GET['page']) ? $_GET['page'] : 1 );
$total = 0;
$count = 10;
$filter = '';

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
    $direve_search = $direve_service_url . 'api/event/search/?q=' . urlencode($query) . '&fq=' . urlencode($filter) . '&start=' . $start;
}else{
    $direve_next_events = $direve_service_url . 'api/event/next/?fq=' . urlencode($filter);
    $direve_search = $direve_service_url . 'api/event/search/?fq=' . urlencode($filter) . '&start=' . $start;
}

#print $direve_service_request;

$response = @file_get_contents($direve_search);
if ($response){
    $response_json = json_decode($response);
    //var_dump($response_json);
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
}

$page_url_params = real_site_url($eve_plugin_slug) . '?q=' . urlencode($query)  . '&filter=' . urlencode($filter);

$pages = new Paginator($total, $start);
$pages->paginate($page_url_params);

?>

<?php get_header('direve');?>
	<div id="content" class="row-fluid">
		<div class="ajusta2">
            <div class="row-fluid breadcrumb">                
                <a href="<?php echo real_site_url(); ?>"><?php _e('Home','direve'); ?></a> >
                <?php if ($query != '' || $user_filter != ''): ?>
                    <a href="<?php echo real_site_url($eve_plugin_slug); ?>"><?php _e('Events Directory', 'direve') ?> </a> >
                    <?php _e('Search result', 'direve') ?>                    
                <?php else: ?> 
                    <?php _e('Events Directory', 'direve') ?>
                <?php endif; ?>
            </div>
			<div class="row-fluid">
                <section class="header-search">
                    <?php if ($direve_config['show_form']) : ?>
                        <form role="search" method="get" id="searchform" action="<?php echo real_site_url($eve_plugin_slug); ?>">
                            <input value="<?php echo $query ?>" name="q" class="input-search" id="s" type="text" placeholder="<?php _e('Search', 'direve'); ?>...">
                            <input id="searchsubmit" value="<?php _e('Search', 'direve'); ?>" type="submit">
                        </form>
                    <?php endif; ?>
                </section>
                <div class="pull-right">
                    <a href="<?php echo real_site_url($eve_plugin_slug . '/suggest-event'); ?>">
                        <img class="header-colabore" src="<?php echo DIREVE_PLUGIN_URL . 'template/images/' . $lang_dir .'/indique.png' ?>" title="<?php _e('Suggest a site','direve'); ?>"/>
                        <a href="<?php echo real_site_url($eve_plugin_slug) ?>events-feed" target="blank"><img src="<?php echo LIS_PLUGIN_URL ?>template/images/icon_rss.png"></a>
                    </a>
                </div>   
            </div>
				
			<section id="conteudo">
                <?php if ( isset($total) && strval($total) == 0) :?>
                    <h1 class="h1-header"><?php _e('No results found','direve'); ?></h1>
                <?php else :?>
    				<header class="row-fluid border-bottom">
                        <?php if ( ( $query != '' || $user_filter != '' ) && strval($total) > 0) :?>
    					   <h1 class="h1-header"><?php _e('Resources found','direve'); ?>: <?php echo $total; ?></h1>
                        <?php else: ?>
                           <h1 class="h1-header"><?php _e('Next events','direve'); ?></h1>
                        <?php endif; ?>
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
                        <?php if ($query != '' || $user_filter != ''){ echo $pages->display_pages(); } ?>
    				</header>
    				<div class="row-fluid">
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
                                        <?php echo $resource->city . ' - ' . $resource->country ;?>
                                    </div>
                                <?php endif; ?>

                                <div id="conteudo-loop-data" class="row-fluid margintop05">
                                    <span class="conteudo-loop-data-tit"><?php _e('Date','direve'); ?>:</span>
                                    <?php echo format_date($resource->start_date); ?> - 
                                    <?php echo format_date($resource->end_date); ?>
                                </div>

        						<p class="row-fluid">
        							<?php echo ( strlen($resource->abstract) > 200 ? substr($resource->abstract,0,200) . '...' : $resource->abstract); ?><br/>
        							<span class="more"><a href="<?php echo real_site_url($eve_plugin_slug); ?>resource/<?php echo $resource->django_id; ?>"><?php _e('See more details','direve'); ?></a></span>
        						</p>


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

        					</article>
                        <?php } ?>
                        <?php if ($query == '' && $user_filter == ''): ?>
                            <div class="row-fluid">
                                <h3><a href="?q=*"><?php _e('See all events','direve'); ?></a></h3>
                            </div>
                        <?php endif; ?>
    				</div>
                    <div class="row-fluid">
                        <?php if ($query != '' || $user_filter != ''){ echo $pages->display_pages(); } ?>
                    </div>
                <?php endif; ?>
			</section>
			<aside id="sidebar">
                <?php if (strval($total) > 0) :?>
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
                                <li class="cat-item">
                                    <a href='<?php echo $filter_link; ?>'><?php echo $descriptor[0] ?></a>
                                    <span class="cat-item-count"><?php echo $descriptor[1] ?></span>
                                </li>
                            <?php } ?>
    					</ul>
    				</section>
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
                                    <a href='<?php echo $filter_link; ?>'><?php print_lang_value($type[0], $site_language); ?></a>
                                    <span class="cat-item-count"><?php echo $type[1] ?></span>
                                </li>
                            <?php } ?>
                        </ul>
                    </section>



                <?php endif; ?>
				<?php dynamic_sidebar('direve-home');?>
			</aside>
			<div class="spacer"></div>
		</div>
	</div>
<?php get_footer();?>
