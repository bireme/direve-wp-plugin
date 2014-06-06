<?php
/*
Plugin Name: DirEve
Plugin URI: https://github.com/bireme/direve-wp-plugin/
Description: Events Directory (DirEVE) WordPress plugin
Author: BIREME/OPAS/OMS
Version: 0.1
Author URI: http://reddes.bvsalud.org/
*/

define('DIREVE_VERSION', '0.1' );

define('DIREVE_SYMBOLIC_LINK', false );
define('DIREVE_PLUGIN_DIRNAME', 'direve' );

if(DIREVE_SYMBOLIC_LINK == true) {
    define('DIREVE_PLUGIN_PATH',  ABSPATH . 'wp-content/plugins/' . DIREVE_PLUGIN_DIRNAME );
} else {
    define('DIREVE_PLUGIN_PATH',  plugin_dir_path(__FILE__) );
}

define('DIREVE_PLUGIN_DIR',   plugin_basename( DIREVE_PLUGIN_PATH ) );
define('DIREVE_PLUGIN_URL',   plugin_dir_url(__FILE__) );

$eve_plugin_slug = 'direve';

require_once(DIREVE_PLUGIN_PATH . '/settings.php');
require_once(DIREVE_PLUGIN_PATH . '/template-functions.php');

function direve_theme_redirect() {
    global $wp, $eve_plugin_slug;
    $pagename = $wp->query_vars["pagename"];

    if ($pagename == $eve_plugin_slug || $pagename == $eve_plugin_slug . '/resource' 
        || $pagename == $eve_plugin_slug . '/suggest-event' 
        || $pagename == $eve_plugin_slug . '/events-feed') {

        add_action( 'wp_enqueue_scripts', 'direve_template_styles_scripts' );

        if ($pagename == $eve_plugin_slug){
            $template = DIREVE_PLUGIN_PATH . '/template/home.php';
        }elseif ($pagename == $eve_plugin_slug . '/suggest-event'){
            $template = DIREVE_PLUGIN_PATH . '/template/suggest-event.php';
        }elseif ($pagename == $eve_plugin_slug . '/events-feed'){
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

function direve_template_styles_scripts(){
    wp_enqueue_script('direve-page',  DIREVE_PLUGIN_URL . 'template/js/functions.js', array( 'jquery' ));
    wp_enqueue_script('jquery-raty',  DIREVE_PLUGIN_URL . 'template/js/jquery.raty.min.js', array( 'jquery' ));
    wp_enqueue_style ('direve-page',  DIREVE_PLUGIN_URL . 'template/css/style.css');
}

function direve_init() {
    global $eve_plugin_slug;

    $direve_config = get_option('direve_config');

    if ($direve_config['eve_plugin_slug'] != ''){
        $eve_plugin_slug = $direve_config['eve_plugin_slug'];
    }

}

function direve_load_translation(){
    // Translations
    load_plugin_textdomain( 'direve', false,  DIREVE_PLUGIN_DIR . '/languages' );
}

function direve_add_admin_menu() {

    add_submenu_page( 'options-general.php', __('DirEVE Settings', 'direve'), __('DirEVE', 'direve'), 'manage_options', 'direve',
                      'direve_page_admin');

    //call register settings function
    add_action( 'admin_init', 'direve_register_settings' );

}

function direve_register_settings(){

    register_setting('direve-settings-group', 'direve_config');

}

function direve_google_analytics_code(){
    $direve_config = get_option('direve_config');
    if ($direve_config['google_analytics_code'] != ''){
?>

<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', '<?php echo $direve_config['google_analytics_code'] ?>']);
  _gaq.push(['_setCookiePath', '/<?php echo $direve_config['eve_plugin_slug'] ?>']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

<?php
    } //endif
}

function direve_search_form( $form ) {
    global $wp, $eve_plugin_slug;
    $pagename = $wp->query_vars["pagename"];


    if ($pagename == $eve_plugin_slug || $pagename == $eve_plugin_slug .'/resource') {
        $form = preg_replace('/action="([^"]*)"(.*)/','action="' . home_url($eve_plugin_slug) . '"',$form);
    }

    return $form;
}

function direve_register_sidebars(){
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

function direve_page_title($title){
    global $wp, $eve_plugin_slug;
    $pagename = $wp->query_vars["pagename"];

    if ( strpos($pagename, $eve_plugin_slug) === 0 ) { //pagename starts with plugin slug
        return 'DirEVE | ';        
    }
}    


add_action( 'init', 'direve_load_translation' );
add_action( 'admin_menu', 'direve_add_admin_menu');
add_action( 'plugins_loaded','direve_init' );
add_action( 'wp_head', 'direve_google_analytics_code');
add_action( 'template_redirect', 'direve_theme_redirect');
add_action( 'widgets_init', 'direve_register_sidebars' );

add_filter( 'wp_title', 'direve_page_title', 10, 2 );
add_filter( 'get_search_form', 'direve_search_form' );

?>
