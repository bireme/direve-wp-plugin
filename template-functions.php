<?php

if ( !function_exists('print_lang_value') ) {
    function print_lang_value($value, $lang_code){
        if ( is_array($value) ){
            foreach($value as $current_value){
                $print_values[] = get_lang_value($current_value, $lang_code);
            }
            echo implode(', ', $print_values);
        }else{
            echo get_lang_value($current_value, $lang_code);
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
            $lang = $lv[0];
            $value = $lv[1];        
            $lang_value[$lang] = $value;        
        }
        return $lang_value[$lang_code];
    }
}

if ( !function_exists('print_formated_date') ) {
    function print_formated_date($string){
        if (strpos($string,'-') !== false) {
            echo substr($string,8,2)  . '/' . substr($string,5,2) . '/' . substr($string,0,4);
        }else{
            echo substr($string,6,2)  . '/' . substr($string,4,2) . '/' . substr($string,0,4);
        }
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
?>