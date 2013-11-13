<?php
/*
Template Name: DirEve Home
*/

require_once(DIREVE_PLUGIN_PATH . '/lib/Paginator.php');

$direve_config = get_option('direve_config');
$direve_service_url = $direve_config['service_url'];
$direve_initial_filter = $direve_config['initial_filter'];

$site_language = strtolower(get_bloginfo('language'));

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

$direve_service_request = $direve_service_url . 'api/event/search/?q=' . urlencode($query) . '&fq=' .urlencode($filter) . '&start=' . $start;

//print $direve_service_request;

$response = @file_get_contents($direve_service_request);
if ($response){
    $response_json = json_decode($response);
    //var_dump($response_json);
    $total = $response_json->diaServerResponse[0]->response->numFound;
    $start = $response_json->diaServerResponse[0]->response->start;
    $event_list = $response_json->diaServerResponse[0]->response->docs;
    $descriptor_list = $response_json->diaServerResponse[0]->facet_counts->facet_fields->descriptor_filter;
}

$page_url_params = home_url($eve_plugin_slug) . '?q=' . $query . '&filter=' . $user_filter;

$pages = new Paginator($total, $start);
$pages->paginate($page_url_params);

?>

<?php get_header();?>
	<div id="content" class="row-fluid">
		<div class="ajusta2">
            <div class="row-fluid breadcrumb">                
                <a href="<?php echo home_url(); ?>"><?php _e('Home','direve'); ?></a> >
                <?php if ($query == '' && $filter == ''): ?>
                    <?php _e('Events Directory', 'direve') ?>
                <?php else: ?>                    
                    <a href="<?php echo home_url($eve_plugin_slug); ?>"><?php _e('Events Directory', 'direve') ?> </a> >
                    <?php _e('Search result', 'direve') ?>
                <?php endif; ?>
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
                    <a href="<?php echo home_url($eve_plugin_slug); ?>/suggest-event" class="header-colabore"><?php _e('Suggest a site','direve'); ?></a>
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
                           <h1 class="h1-header"><?php _e('Most recent','direve'); ?></h1>
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
                        <?php echo $pages->display_pages(); ?>
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
        						<p class="row-fluid">
        							<?php echo ( strlen($resource->abstract) > 200 ? substr($resource->abstract,0,200) . '...' : $resource->abstract); ?><br/>
        							<span class="more"><a href="<?php echo home_url($eve_plugin_slug); ?>/resource/<?php echo $resource->django_id; ?>"><?php _e('See more details','direve'); ?></a></span>
        						</p>

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

        					</article>
                        <?php } ?>
    				</div>
                    <div class="row-fluid">
                        <?php echo $pages->display_pages(); ?>
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
                                <li class="cat-item">
                                    <a href='?filter=descriptor:"<?php echo $descriptor[0]; ?>"'><?php echo $descriptor[0] ?></a>
                                    <span class="cat-item-count"><?php echo $descriptor[1] ?></span>
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
