<?php

define('WP_CALENDAR_PLUGIN_URL', DIREVE_PLUGIN_URL . 'calendar');
define('WP_CALENDAR_PLUGIN_DIR', DIREVE_PLUGIN_PATH . 'calendar');
define('WP_CALENDAR_PLUGIN_CSS_PATH', DIREVE_PLUGIN_PATH . 'calendar/css/wp_calendar.css');
define('WP_CALENDAR_PLUGIN_CSS_URL', DIREVE_PLUGIN_URL . 'calendar/css/wp_calendar.css');

$direve_config = get_option('direve_config');

if (!session_id()) {
    session_start();
}

/**
 * @name $WP_Calendar_get_regional_match
 * @return null
 */
if ( !function_exists('WP_Calendar_get_regional_match') ) {
    function WP_Calendar_get_regional_match() {
        $locale = get_locale();

        $regionals = array(
            'ar' => 'Arabic',
            'fr' => 'French',
            'he' => 'Hebrew',
            'pt' => 'Portuguese'
        );

        $key_match = array(
            substr($locale, 0, 2),
            str_replace('_', '-', $locale),
        );

        if ($key_match[1] != 'en') {
            foreach ($key_match as $key) {
                if (array_key_exists($key, $regionals)) {
                    return $key;
                }
            }
        }

        return null;
    }
}

/**
 * function register and enqueue scripts in front end
 */
if ( !function_exists('wp_calendar_enqueue_scripts') ) {
    function wp_calendar_enqueue_scripts() {
        wp_enqueue_script('jquery');
        $regional = WP_Calendar_get_regional_match();
        if (!empty($regional)) {
            wp_register_script('wp_calendar_datepicker-' . $regional, WP_CALENDAR_PLUGIN_URL . '/js/jquery.ui.datepicker-' . $regional . '.js');
        }

        wp_register_script('wp_calendar_datepicker', WP_CALENDAR_PLUGIN_URL . '/js/jquery.ui.datepicker.js');
        wp_register_script('wp_calendar_js', WP_CALENDAR_PLUGIN_URL . '/js/wp_calendar.js', array('jquery'));
        wp_register_style('wp_calendar_css', WP_CALENDAR_PLUGIN_CSS_URL);

        wp_enqueue_script('wp_calendar_datepicker-' . $regional);
        wp_enqueue_script('wp_calendar_datepicker');
        wp_enqueue_script('wp_calendar_js');
        wp_enqueue_style('wp_calendar_css');

        wp_localize_script('wp_calendar_js', 'wpCalendarObj', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'plugin_url' => WP_CALENDAR_PLUGIN_URL
        ));
    }
}

if ($direve_config['show_calendar']){
    add_action('wp_enqueue_scripts', 'wp_calendar_enqueue_scripts');
}

/**
 * function load custom css in site header
 */
if ( !function_exists('wp_calendar_load_custom_style_cb') ) {
    function wp_calendar_load_custom_style_cb() {
        ?>
        <style>
            #calendar_wrap .ui-datepicker-prev span {background: url("<?php echo WP_CALENDAR_PLUGIN_URL; ?>/images/arrow-new.png") no-repeat scroll 0 0 transparent;}
            #calendar_wrap .ui-datepicker-prev span:hover {background: url("<?php echo WP_CALENDAR_PLUGIN_URL; ?>/images/arrow-prev-hover.png") no-repeat scroll 0 0 transparent;}
            #calendar_wrap .ui-datepicker-next span {background: url("<?php echo WP_CALENDAR_PLUGIN_URL; ?>/images/arrow-new2.png") no-repeat scroll 0 0 transparent;}
            #calendar_wrap .ui-datepicker-next span:hover {background: url("<?php echo WP_CALENDAR_PLUGIN_URL; ?>/images/arrow-next-hover.png") no-repeat scroll 0 0 transparent;}
        </style>
        <?php
    }
}

add_action('wp_head', 'wp_calendar_load_custom_style_cb');

/**
 * Action to register widget
 */
add_action('wp_ajax_wp_calendar_get_events', 'wp_calendar_get_events');
add_action('wp_ajax_nopriv_wp_calendar_get_events', 'wp_calendar_get_events');

/**
 * function to get events for calendar grid
 */
if ( !function_exists('wp_calendar_get_events') ) {
    function wp_calendar_get_events() {

        $wp_cal_content = array();
        $ajax = $_POST['ajax'];
        $selected_month = $_POST['month'];
        $selected_year = $_POST['year'];

        $direve_config = get_option('direve_config');
        $direve_service_url = 'https://fi-admin-api.bvsalud.org/';
        $direve_initial_filter = $direve_config['initial_filter'];
        $site_language = strtolower(get_bloginfo('language'));
        $lang_dir = substr($site_language,0,2);
        $count = 100;
        $total = 0;

        $query = 'start_date:[' . $selected_year . '-' . $selected_month . '-01T00:00:00Z+TO+' . $selected_year . '-' . $selected_month . '-31T00:00:00Z]';
        $direve_search = $direve_service_url . 'api/event/search/?q=' . $query . '&fq=' . urlencode($direve_initial_filter) . '&lang=' . $lang_dir . '&count=' . $count;

        if ($ajax == 'true') {
            $classes = array();
            $fpost = array();
            $links = array();
            $permalink = array();
            $datel = array();
            $date_day = array();
            $date_month = array();
            $date_year = array();

            $response = @file_get_contents($direve_search);
            if ($response){
                $response_json = json_decode($response);
                $event_list = $response_json->diaServerResponse[0]->response->docs;
                foreach ($event_list as $resource) {
                    $start_date = substr($resource->start_date,0,10);
                    $da = date("Y-n-j", strtotime($start_date));
                    $classes[$da]++;
                }
            }
            
            $wp_cal_content['classes'] = $classes;
        }

        die(json_encode($wp_cal_content));
    }
}

?>
