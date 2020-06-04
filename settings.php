<?php
function direve_page_admin() {

    $config = get_option('direve_config');

    ?>
    <div class="wrap">
        <div id="icon-options-general" class="icon32"></div>
        <h2><?php _e('DirEve Plugin Options', 'direve'); ?></h2>

        <form method="post" action="options.php">

            <?php settings_fields('direve-settings-group'); ?>

            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th scope="row"><?php _e('Plugin page', 'direve'); ?>:</th>
                        <td><input type="text" name="direve_config[plugin_slug]" value="<?php echo ($config['plugin_slug'] != '' ? $config['plugin_slug'] : 'direve'); ?>" class="regular-text code"></td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php _e('Filter query', 'direve'); ?>:</th>
                        <td><input type="text" name="direve_config[initial_filter]" value='<?php echo $config['initial_filter'] ?>' class="regular-text code"></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Calendar events', 'direve'); ?>:</th>
                        <td>
                            <input type="checkbox" name="direve_config[show_calendar]" value="1" <?php if ( $config['show_calendar'] == '1' ): echo ' checked="checked"'; endif;?> >
                            <?php _e('Show events calendar', 'direve'); ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Search form', 'direve'); ?>:</th>
                        <td>
                            <input type="checkbox" name="direve_config[show_form]" value="1" <?php if ( $config['show_form'] == '1' ): echo ' checked="checked"'; endif;?> >
                            <?php _e('Show search form', 'direve'); ?>
                        </td>
                    </tr>

                    <?php
                        if ( function_exists( 'pll_the_languages' ) ) {
                            $available_languages = pll_languages_list();
                            $available_languages_name = pll_languages_list(array('fields' => 'name'));
                            $count = 0;
                            foreach ($available_languages as $lang) {
                                $key_name = 'plugin_title_' . $lang;
                                $home_url = 'home_url_' . $lang;
                                echo '<tr valign="top">';
                                echo '    <th scope="row"> ' . __("Page title", "direve") . ' (' . $available_languages_name[$count] . '):</th>';
                                echo '    <td><input type="text" name="direve_config[' . $key_name . ']" value="' . $config[$key_name] . '" class="regular-text code"></td>';
                                echo '</tr>';
                                $count++;
                            }
                        } else {
                            echo '<tr valign="top">';
                            echo '   <th scope="row">' . __("Page title", "direve") . ':</th>';
                            echo '   <td><input type="text" name="direve_config[plugin_title]" value="' . $config["plugin_title"] .'" class="regular-text code"></td>';
                            echo '</tr>';
                        }
                    ?>

                    <tr valign="top">
                        <th scope="row"><?php _e('Disqus shortname', 'direve'); ?>:</th>
                        <td><input type="text" name="direve_config[disqus_shortname]" value="<?php echo $config['disqus_shortname'] ?>" class="regular-text code"></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('AddThis profile ID', 'direve'); ?>:</th>
                        <td><input type="text" name="direve_config[addthis_profile_id]" value="<?php echo $config['addthis_profile_id'] ?>" class="regular-text code"></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Google Analytics code', 'direve'); ?>:</th>
                        <td><input type="text" name="direve_config[google_analytics_code]" value="<?php echo $config['google_analytics_code'] ?>" class="regular-text code"></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Sidebar order', 'direve');?>:</th>

                        <?php
                            if(!isset($config['available_filter'])){
                                $config['available_filter'] = 'Subjects;Event type';
                                $order = explode(';', $config['available_filter'] );
                            } else {
                                $order = array_filter(explode(';', $config['available_filter']));
                            }
                        ?>

                        <td>

                            <table border=0>
                                <tr>
                                    <td>
                                        <p align="right"><?php _e('Available', 'direve');?><br>
                                            <ul id="sortable1" class="droptrue">
                                                <?php
                                                    if(!in_array('Event type', $order) && !in_array('Event type ', $order) ){
                                                        echo '<li class="ui-state-default" id="Event type">'.translate('Event type','direve').'</li>';
                                                    }
                                                    if(!in_array('Subjects', $order) && !in_array('Subjects ', $order) ){
                                                        echo '<li class="ui-state-default" id="Subjects">'.translate('Subjects','direve').'</li>';
                                                    }
                                                ?>
                                            </ul>
                                        </p>
                                    </td>
                                    <td>
                                        <p align="left"><?php _e('Selected', 'direve');?> <br>
                                            <ul id="sortable2" class="sortable-list">
                                                <?php
                                                    foreach ($order as $index => $item) {
                                                        $item = trim($item); // Important
                                                        if($item != '') {
                                                            echo '<li class="ui-state-default" id="'.$item.'">'.translate($item ,'direve').'</li>';
                                                        }
                                                    }
                                                ?>
                                            </ul>
                                            <input type="hidden" id="order_aux" name="direve_config[available_filter]" value="<?php echo trim($config['available_filter']); ?>">
                                        </p>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>
                </tbody>
            </table>

            <p class="submit">
                <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
            </p>

        </form>
    </div>
    <script type="text/javascript">
      var $j = jQuery.noConflict();
      
      $j( function() {
        $j( "ul.droptrue" ).sortable({
          connectWith: "ul"
        });

        $j('.sortable-list').sortable({

          connectWith: 'ul',
          update: function(event, ui) {
            var changedList = this.id;
            var order = $j(this).sortable('toArray');
            var positions = order.join(';');
            $j('#order_aux').val(positions);

          }
        });
      });
    </script>

    <?php
}
?>
