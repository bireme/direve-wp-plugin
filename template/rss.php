<?php
echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";

/*
Template Name: DirEVE RSS
*/

$direve_config = get_option('direve_config');
$direve_service_url = $direve_config['service_url'];
$initial_filter = $direve_config['initial_filter'];

$site_language = strtolower(get_bloginfo('language'));

$query = ( isset($_GET['s']) ? $_GET['s'] : $_GET['q'] );
$query = stripslashes($query);
$user_filter = stripslashes($_GET['filter']);
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

if ($query != '' || $filter != ''){
    $direve_get_url = $direve_service_url . 'api/event/search/?q=' . urlencode($query) . '&fq=' . urlencode($filter) . '&lang=' . $lang_dir;
}else{
    $direve_get_url = $direve_service_url . 'api/event/next/?fq=' . urlencode($filter) . '&lang=' . $lang_dir;
}

$response = @file_get_contents($direve_get_url);
if ($response){
    $response_json = json_decode($response);
    $total = $response_json->diaServerResponse[0]->response->numFound;
    $start = $response_json->diaServerResponse[0]->response->start;
    $event_list = $response_json->diaServerResponse[0]->response->docs;
}

?>
<rss version="2.0">
    <channel>
        <title><?php _e('Events Directory', 'direve') ?></title>
        <link><?php echo real_site_url($eve_plugin_slug) . 'events-feed' ?></link>
        <description><?php echo  ($query != '' || $filter != '') ? $query . ' ' . $filter : _e('Next events','direve');  ?></description>
        <?php 
            foreach ( $event_list as $event) {
                $rss_description = '';

                echo "<item>\n";
                echo "   <title>". htmlspecialchars($event->title) . "</title>\n";
                if ($event->author){
                    echo "   <author>". implode(", ", $event->author) . "</author>\n";
                }
                echo "   <link>" . real_site_url($eve_plugin_slug) . 'resource/'  . $event->django_id . "</link>\n";

                if ($event->city || $event->country) {
                    $rss_description .= $event->city . ' - ' . $event->country . '<br/>';
                }
                $rss_description .= __('Date','direve') . ': ' . format_date($event->start_date);
                $rss_description .= ' - '. format_date($event->end_date) . '<br/>';

                if ($event->abastract){                
                    $rss_description .= $event->abstract . '<br/>';
                }

                if ($event->source_language_display){
                    $rss_description .= __('Available languages','direve') . ': ';
                    $rss_description .= print_lang_value($event->source_language_display, $site_language);
                        
                }

                if ($event->descriptor || $event->keyword ) {
                    $descriptors = (array)$event->descriptor;
                    $keywords = (array)$event->keyword;
                    $rss_description .= __('Subjects','direve') . ': ';
                    $rss_description .= implode(", ", array_merge( $descriptors, $keywords) );
                }
                echo "   <description><![CDATA[ " . $rss_description . " ]]></description>\n";
                echo "   <guid isPermaLink=\"false\">" . $event->django_id . "</guid>\n";
                echo "</item>\n";
            }
        ?>
    </channel>
</rss>
