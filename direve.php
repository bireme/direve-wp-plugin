<?php
/*
Plugin Name: DirEve
Plugin URI: https://github.com/bireme/direve-wp-plugin/
Description: Events Directory (DirEVE) WordPress plugin
Author: BIREME/OPAS/OMS
Version: 0.2
Author URI: http://reddes.bvsalud.org/
*/

define('DIREVE_VERSION', '0.4' );

define('DIREVE_SYMBOLIC_LINK', false );
define('DIREVE_PLUGIN_DIRNAME', 'direve' );

if(DIREVE_SYMBOLIC_LINK == true) {
    define('DIREVE_PLUGIN_PATH',  ABSPATH . 'wp-content/plugins/' . DIREVE_PLUGIN_DIRNAME );
} else {
    define('DIREVE_PLUGIN_PATH',  plugin_dir_path(__FILE__) );
}

define('DIREVE_PLUGIN_DIR',   plugin_basename( DIREVE_PLUGIN_PATH ) );
define('DIREVE_PLUGIN_URL',   plugin_dir_url(__FILE__) );

require_once(DIREVE_PLUGIN_PATH . '/settings.php');
require_once(DIREVE_PLUGIN_PATH . '/template-functions.php');
require_once(DIREVE_PLUGIN_PATH . '/calendar/wp-calendar.php');

if(!class_exists('DirEve_Plugin')) {
    class DirEve_Plugin {

        private $plugin_slug = 'direve';
        private $service_url = 'http://fi-admin.data.bvsalud.org/';

        /**
         * Construct the plugin object
         */
        public function __construct() {
            // register actions

            //add_action( 'wp_head', array(&$this, 'geolocation_head') );
            add_action( 'init', array(&$this, 'load_translation') );
            add_action( 'admin_menu', array(&$this, 'admin_menu') );
            add_action( 'plugins_loaded', array(&$this, 'plugin_init') );
            add_action( 'wp_head', array(&$this, 'google_analytics_code') );
            add_action( 'template_redirect', array(&$this, 'theme_redirect') );
            add_action( 'widgets_init', array(&$this, 'register_sidebars') );
            add_filter( 'get_search_form', array(&$this, 'search_form') );
            add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array(&$this, 'settings_link') );
            add_filter( 'document_title_parts', array(&$this, 'theme_slug_render_title'));

        } // END public function __construct

        /**
         * Activate the plugin
         */
        public static function activate()
        {
            // Do nothing
        } // END public static function activate

        /**
         * Deactivate the plugin
         */
        public static function deactivate()
        {
            // Do nothing
        } // END public static function deactivate

        function load_translation(){
		    // Translations
		    load_plugin_textdomain( 'direve', false,  DIREVE_PLUGIN_DIR . '/languages' );
		}

		function plugin_init() {
		    $direve_config = get_option('direve_config');

		    if ( $direve_config && $direve_config['plugin_slug'] != ''){
		        $this->plugin_slug = $direve_config['plugin_slug'];
		    }

		}

		function admin_menu() {
		    add_submenu_page( 'options-general.php', __('DirEVE Settings', 'direve'), __('DirEVE', 'direve'), 'manage_options', 'direve', 'direve_page_admin');

		    //call register settings function
		    add_action( 'admin_init', array(&$this, 'register_settings') );
		}

		function theme_redirect() {
		    global $wp, $direve_service_url, $direve_plugin_slug;

            // check if request contains plugin slug string
            $pos_slug = strpos($wp->request, $this->plugin_slug);
            if ( $pos_slug !== false ){
                $pagename = substr($wp->request, $pos_slug);
            }

            if ( is_404() && $pos_slug !== false ){
                $direve_service_url = $this->service_url;
                $direve_plugin_slug = $this->plugin_slug;

    		    if ($pagename == $this->plugin_slug || $pagename == $this->plugin_slug . '/resource'
    		        || $pagename == $this->plugin_slug . '/suggest-event'
    		        || $pagename == $this->plugin_slug . '/events-feed') {

    		        add_action( 'wp_enqueue_scripts', array(&$this, 'template_styles_scripts') );

    		        if ($pagename == $this->plugin_slug){
    		            $template = DIREVE_PLUGIN_PATH . '/template/home.php';
    		        }elseif ($pagename == $this->plugin_slug . '/suggest-event'){
    		            $template = DIREVE_PLUGIN_PATH . '/template/suggest-event.php';
    		        }elseif ($pagename == $this->plugin_slug . '/events-feed'){
    		            $template = DIREVE_PLUGIN_PATH . '/template/rss.php';
    		        }else{
    		            $template = DIREVE_PLUGIN_PATH . '/template/detail.php';
    		        }
    		        // force status to 200 - OK
    		        status_header(200);

    		        // redirect to page and finish execution
    		        include($template);
    		        die();
    		    }
            }
		}

		function register_sidebars(){
		    $args = array(
		        'name' => __('DirEVE sidebar', 'direve'),
		        'id'   => 'direve-home',
		        'description' => 'DirEVE Area',
		        'before_widget' => '<section id="%1$s" class="row-fluid widget %2$s">',
		        'after_widget'  => '</section>',
		        'before_title'  => '<h2 class="widgettitle">',
		        'after_title'   => '</h2>',
		    );
		    register_sidebar( $args );
		}

        function theme_slug_render_title( $title_parts ) {
            global $wp, $direve_plugin_title;

            $pagename = '';
            // check if request contains plugin slug string
            $pos_slug = strpos($wp->request, $this->plugin_slug);
            if ( $pos_slug !== false ){
                $pagename = substr($wp->request, $pos_slug);
            }
            if ( is_404() && $pos_slug !== false ){
                $config = get_option('direve_config');
                if ( function_exists( 'pll_the_languages' ) ) {
                    $current_lang = pll_current_language();
                    $direve_plugin_title = $config['plugin_title_' . $current_lang];
                }else{
                    $direve_plugin_title = $config['plugin_title'];
                }
                $title_parts['title'] = $direve_plugin_title . " | " . get_bloginfo('name');
            }

            return $title_parts;
        }


        function search_form( $form ) {
            global $wp;

            $pagename = '';
            // check if request contains plugin slug string
            $pos_slug = strpos($wp->request, $this->plugin_slug);
            if ( $pos_slug !== false ){
                $pagename = substr($wp->request, $pos_slug);
            }

            if ($pagename == $this->plugin_slug || $pagename == $this->plugin_slug .'/resource') {
                $form = preg_replace('/action="([^"]*)"(.*)/','action="' . home_url($this->plugin_slug) . '"',$form);
            }
            return $form;
        }

		function template_styles_scripts(){
		    wp_enqueue_script('direve-page', DIREVE_PLUGIN_URL . 'template/js/functions.js',       array( 'jquery' ));
		    wp_enqueue_script('jquery-raty', DIREVE_PLUGIN_URL . 'template/js/jquery.raty.min.js', array( 'jquery' ));
		    wp_enqueue_style ('direve-page', DIREVE_PLUGIN_URL . 'template/css/style.css');
		}

		function register_settings(){
		    register_setting('direve-settings-group', 'direve_config');
        wp_enqueue_style ('direve-page',  DIREVE_PLUGIN_URL . 'template/css/admin.css');
        wp_enqueue_script('direve-page',  DIREVE_PLUGIN_URL . 'template/js/jquery-ui.js');
		}

                function settings_link($links) {
                    $settings_link = '<a href="options-general.php?page=direve.php">Settings</a>';
                    array_unshift($links, $settings_link);
                    return $links;
                }

		function google_analytics_code(){
		    global $wp;

            $pos_slug = strpos($wp->request, $this->plugin_slug);
            if ( $pos_slug !== false ){
                $pagename = substr($wp->request, $pos_slug);

                $config = get_option('direve_config');
                // check if is defined GA code and pagename starts with plugin slug
                if ($config['google_analytics_code'] != ''
                    && strpos($pagename, $this->plugin_slug) === 0){
		?>

		<script type="text/javascript">
		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', '<?php echo $config['google_analytics_code'] ?>']);
		  _gaq.push(['_setCookiePath', '/<?php echo $config['$this->plugin_slug'] ?>']);
		  _gaq.push(['_trackPageview']);

		  (function() {
		    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();

		</script>

		<?php
            } //endif lis_config
          }// endif pos_slug
        } // end function google_analytics_code

		function geolocation_head() {
		    ob_start();
		?>
		    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places"></script>
		    <script type="text/javascript">
		        var map;
		        var directionDisplay;
		        var directionsService = new google.maps.DirectionsService();

		        function geolocationInitialize() {
                            lat = lat();
                            lng = lng();
		            var dirLoc = new google.maps.LatLng(lat, lng);
		            directionsDisplay = new google.maps.DirectionsRenderer();
		            var mapOptions = {
		                mapTypeControl: true,
		                streetViewControl: true,
		                overviewMapControl: true,
		                scaleControl: true,
		                panControl: true,
		                zoomControl: true,
		                mapTypeId: google.maps.MapTypeId.ROADMAP,
		                center: dirLoc,
		                zoom:15
		            }

		            map = new google.maps.Map(document.getElementById('geolocation-map-canvas'), mapOptions);
		            marker = new google.maps.Marker({map: map, position: dirLoc});

		            directionsDisplay.setMap(map);
		            directionsDisplay.setPanel(document.getElementById("directionsPanel"));
		        }
		    </script>
		<?php
		    $geolocation_res = ob_get_contents();
		    ob_end_clean();
		    echo $geolocation_res;
		}
	}
}

if(class_exists('DirEve_Plugin'))
{
    // Installation and uninstallation hooks
    register_activation_hook(__FILE__, array('DirEve_Plugin', 'activate'));
    register_deactivation_hook(__FILE__, array('DirEve_Plugin', 'deactivate'));

    // Instantiate the plugin class
    $wp_plugin_template = new DirEve_Plugin();
}

?>
