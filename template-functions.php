<?php

if ( !function_exists('print_lang_value') ) {
    function print_lang_value($value, $lang_code){
        $lang_code = substr($lang_code,0,2);
        if ( is_array($value) ){
            foreach($value as $current_value){
                $print_values[] = get_lang_value($current_value, $lang_code);
            }
            echo implode(', ', $print_values);
        }else{
            echo get_lang_value($value, $lang_code);
        }
        return;
    }
}

if ( !function_exists('get_lang_value') ) {
    function get_lang_value($string, $lang_code){
        $lang_value = array();
        $occs = preg_split('/\|/', $string);

        foreach ($occs as $occ){
            $lv = preg_split('/\^/', $occ);
            $lang = substr($lv[0],0,2);
            $value = $lv[1];        
            $lang_value[$lang] = $value;        
        }
        return $lang_value[$lang_code];
    }
}

if ( !function_exists('format_date') ) {
    function format_date($string){
        $date_formated = '';
        if (strpos($string,'-') !== false) {
            $date_formated = substr($string,8,2)  . '/' . substr($string,5,2) . '/' . substr($string,0,4);
        }else{
            $date_formated =  substr($string,6,2)  . '/' . substr($string,4,2) . '/' . substr($string,0,4);
        }

        return $date_formated;
    }
}

if ( !function_exists('isUTF8') ) {
    function isUTF8($string){
        return (utf8_encode(utf8_decode($string)) == $string);
    }
}

if ( !function_exists('get_site_meta_tags') ) {
    function get_site_meta_tags($url){

        $site_title = array();

        $fp = @file_get_contents($url);

        if ($fp) {
            $res = preg_match("/<title>(.*)<\/title>/siU", $fp, $title_matches);
            if ($res) {
                $site_title = preg_replace('/\s+/', ' ', $title_matches[1]);
                $site_title = trim($site_title);
            }

            $site_meta_tags = get_meta_tags($url);        
            $site_meta_tags['title'] = $site_title;

            foreach ($site_meta_tags as $key => $value) {
                if (!isUTF8($value)){
                    $site_meta_tags[$key] = utf8_encode($value);
                }
            }
        }
        return $site_meta_tags;
    }
}

if ( !function_exists('real_site_url') ) {
    function real_site_url($path = ''){

        $site_url = get_site_url();

        // check for multi-language-framework plugin
        if ( function_exists('mlf_parseURL') ) {
            global $mlf_config;

            $current_language = substr( strtolower(get_bloginfo('language')),0,2 );

            if ( $mlf_config['default_language'] != $current_language ){
                $site_url .= '/' . $current_language;    
            }
        }
        // check for multi-language-framework plugin
        elseif ( defined( 'POLYLANG_VERSION' ) ) {
            $default_language = pll_default_language();
            $current_language = pll_current_language();

            if ( $default_language != $current_language ){
                $site_url .= '/' . $current_language;
            }
        }

        if ($path != ''){
            $site_url .= '/' . $path;
        }
        
        $site_url .= '/';


        return $site_url;
    }
}

if ( !function_exists('geolocation') ) {
    function geolocation($address) {
        ob_start();
    ?>

        <style>
            .geolocation-map {
                margin-top: -10px;
                position: relative;
                width: 100%;
                float: left;
                display: none;
            }
            #geolocation-map-canvas {
                width: 100%;
                height: 320px;
                margin-top:15px;
            }
        </style>
        <script type="text/javascript">
            var lat;
            var lng;
            var latlng;
            var newArray = [];
            var address = '<?php echo $address; ?>';
            var geocoder;

            $j(document).ready(function() {
                geocoder = new google.maps.Geocoder();
                geocoder.geocode( { 'address': address }, function( results, status ) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        latlng = results[0].geometry.location;
                        for (var key in latlng) {
                            newArray.push(key);
                        }
                        lat = latlng[newArray[0]];
                        lng = latlng[newArray[1]];
                        $j(".geolocation-map").show();
                    } else {
                        //alert('Geocode was not successful for the following reason: ' + status);
                    }
                });
            });            
        </script>
        <div class="geolocation-map">
            <div id="geolocation-map-canvas"></div>
            <div id="directionsPanel"></div>
        </div>

    <?php
        $geoloc_res = ob_get_contents();
        ob_end_clean();
        return $geoloc_res;
    }
}

?>
