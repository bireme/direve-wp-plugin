<?php
header("Content-Type: application/rss+xml; charset=UTF-8");
echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";

/*
Template Name: DirEVE RSS
*/
global $direve_service_url, $direve_plugin_slug;

$direve_config = get_option('direve_config');
$direve_initial_filter = ( $direve_config['initial_filter'] ? $direve_config['initial_filter'] : '' );

$site_language = strtolower(get_bloginfo('language'));
$lang_dir = substr($site_language,0,2);

$query = ( isset($_GET['s']) ? sanitize_text_field($_GET['s']) : sanitize_text_field($_GET['q']) );
$query = stripslashes($query);
$sanitize_user_filter = ( sanitize_text_field($_GET['filter']) ? sanitize_text_field($_GET['filter']) : '' );
$user_filter = stripslashes($sanitize_user_filter);
$mode_filter = ( isset($_GET['mode']) ? 'event_modality:'.sanitize_text_field($_GET['mode']) : '' );

$filter = '';
$filter = implode(' AND ', array_filter(array($direve_initial_filter, $mode_filter, $user_filter)));

if ($query != '' || $user_filter != ''){
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

$rss_channel_url = real_site_url($direve_plugin_slug) . '?q=' . urlencode($query) . '&filter=' . urlencode($user_filter);

?>

<?php if ( $event_list ) : ?>
<rss version="2.0">
    <channel>
        <title><?php _e('Events Directory', 'direve') ?></title>
        <link><?php echo htmlspecialchars($rss_channel_url); ?></link>
        <description><?php echo  ($query != '' || $user_filter != '') ? $query . ' ' . $user_filter : _e('Next events','direve');  ?></description>
        <?php
            foreach ( $event_list as $event) {
                $rss_description = '';

                echo "<item>\n";
                echo "   <title>". htmlspecialchars($event->title) . "</title>\n";
                if ($event->author){
                    echo "   <author>". implode(", ", $event->author) . "</author>\n";
                }
                echo "   <link>" . real_site_url($direve_plugin_slug) . 'resource/?id='  . $event->django_id . "</link>\n";

                $rss_description .= format_date($event->start_date);
                $rss_description .= ' - ' . format_date($event->end_date) . '. ';

                if ($event->city || $event->country) {
                    $rss_description .= trim($event->city) . ' - ' . trim($event->country) . '.';
                }

                /*
                if ($event->abstract){
                    $rss_description .= $event->abstract . '&nbsp;<br />';
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
                */

                echo "   <description><![CDATA[" . $rss_description . "]]></description>\n";
                echo "   <guid isPermaLink=\"false\">" . $event->django_id . "</guid>\n";
                echo "</item>\n";
            }
        ?>
    </channel>
</rss>
<?php else : ?>
<rss version="2.0">
    <channel>
        <title><?php _e('Events Directory', 'direve') ?></title>
        <link><?php echo htmlspecialchars($rss_channel_url); ?></link>
        <description><?php echo  ($query != '' || $user_filter != '') ? $query . ' ' . $user_filter : _e('Next events','direve');  ?></description>
        <item>
            <title><?php _e('No upcoming events.', 'direve'); ?></title>
        </item>
    </channel>
</rss>
<?php endif; ?>