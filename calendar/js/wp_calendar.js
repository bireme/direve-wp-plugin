var wp_cal_dates = new Array();
var wp_cal_posts = new Array();

function WP_Cal_convertMonth(month) {
    var m = new Array();
    m["1"] = 1;
    m["2"] = 2;
    m["3"] = 3;
    m["4"] = 4;
    m["5"] = 5;
    m["6"] = 6;
    m["7"] = 7;
    m["8"] = 8;
    m["9"] = 9;
    m["10"] = 10;
    m["11"] = 11;
    m["12"] = 12;
    return m[month];
}

function monthRange (month, year) {
    var mr = new Array();
    mr["1"] = 31;
    mr["2"] = ((year % 4 == 0) ? 29 : 28);
    mr["3"] = 31;
    mr["4"] = 30;
    mr["5"] = 31;
    mr["6"] = 30;
    mr["7"] = 31;
    mr["8"] = 31;
    mr["9"] = 30;
    mr["10"] = 31;
    mr["11"] = 30;
    mr["12"] = 31;
    return mr[month];
}
function monthFollowLink(month, year) {
    range = monthRange(month, year);
	jQuery('.ui-datepicker-title span').wrapAll('<a class="query-month" />');
	jQuery('a.query-month').attr('href', '?q=start_date:[' + year + '-' + month + '-01T00:00:00Z+TO+' + year + '-' + month + '-' + range + 'T00:00:00Z]');
	jQuery('.ui-datepicker-month').after("&nbsp;");
}

jQuery(document).ready(function(){
    jQuery('#wp-calendar').datepicker();
    jQuery('#wp-calendar a.ui-state-active').addClass('wp-calendar-current-date').parent('td').addClass('wp-calendar-today-active');
    
    function WP_Cal_get_posts_by_month(month, year){
        var check_date = month+'-'+year;
        var found_at = jQuery.inArray( check_date , wp_cal_dates );
        if( found_at == -1 ){
            
            //jQuery(".wp_calendar .calendar_wrap_loading").removeClass('calendar_wrap_loading_hide').addClass('calendar_wrap_loading_show');
            jQuery(".wp_calendar .circle_loading").removeClass('hide_circle').addClass('show_circle');
            jQuery('#wp-calendar .ui-datepicker-prev').toggle();
            jQuery('#wp-calendar .ui-datepicker-next').toggle();
            jQuery('#calendar_wrap .calendar-pagi a').each(function(){
                jQuery(this).removeClass('pagi_state_show').toggle();
            });
            jQuery('a.ui-state-default').removeAttr('href').css('text-decoration', 'none');
            
            wp_cal_dates.push(check_date);
            jQuery.ajax({
                type    :   'POST',
                url     :   wpCalendarObj.ajaxurl,
                data    :   {
                    action          :   'wp_calendar_get_events',
                    ajax            :   'true',
                    month           :   month,
                    year            :   year
                },
                success :   function( data ){
                    data = jQuery.parseJSON( data );
                    var posts = data.posts;
                    var this_dates = data.classes;
                    var curr_date = new Array();
                    var d = '';
                    jQuery.each( this_dates, function (key , value){
                        curr_date = key.split('-');
                        d = curr_date[2];
                        var element = jQuery('#wp-calendar a.ui-state-default:contains("'+d+'")');
                        if( element.length > 1 ){
                            jQuery(element).each( function(){
                                if( jQuery( this ).text() == d ){
                                    jQuery( this ).parent('td').addClass('WP-Cal-popup');
                                }
                            });
                            
                        }
                        else{
                            jQuery( element ).parent('td').addClass('WP-Cal-popup');
                        }
                    });
                    jQuery('#wp-calendar .WP-Cal-popup').each( function (){
                        var year = jQuery(this).attr('data-year');
                        var month = jQuery(this).attr('data-month');
                        month = parseInt( month ) + 1;
                        var day = jQuery(this).find('a.ui-state-default').text();
                        var this_date = year + '-' + month + '-' + day;
                        var innerContent = posts[this_date];
                        if (/undefined/.test(innerContent)) {
                            jQuery(this).removeClass('WP-Cal-popup');
                        } else {
                            jQuery(this).append( '<div class="wp-cal-tooltip">'+innerContent+'</div>' );
                            jQuery(this).find('a.ui-state-default').attr('href', '?q=start_date:"'+year+'-'+month+'-'+day+'T00:00:00Z"');
                        }
                    });
                    var new_data_element = {
                        month : check_date, 
                        classes : this_dates, 
                        posts : posts
                    };
                    wp_cal_posts.push( new_data_element );
                },
                complete: function() {
                    jQuery('.ui-datepicker-next, .ui-datepicker-prev, .wp-cal-prev, .wp-cal-next').bind('click');
                    //jQuery(".wp_calendar .calendar_wrap_loading").removeClass('calendar_wrap_loading_show').addClass('calendar_wrap_loading_hide');
                    jQuery(".wp_calendar .circle_loading").removeClass('show_circle').addClass('hide_circle');
                    jQuery('#wp-calendar .ui-datepicker-prev').toggle();
                    jQuery('#wp-calendar .ui-datepicker-next').toggle();
                    jQuery('#calendar_wrap .calendar-pagi a').each(function(){
                        jQuery(this).removeClass("pagi_state_hide").toggle();
                    });
                }
            });
        }
        else{
            var this_dates = {};
            var this_posts = {};
            
            jQuery( wp_cal_posts ).each( function (){
                if( this.month == check_date ){
                    this_dates = this.classes;
                    this_posts = this.posts;
                    var curr_date = new Array();
                    var d = '';
                    jQuery.each( this_dates, function (key , value){
                        curr_date = key.split('-');
                        d = curr_date[2];
                        var element = jQuery('#wp-calendar a.ui-state-default:contains("'+d+'")');
                        if( element.length > 1 ){
                            jQuery(element).each( function(){
                                if( jQuery( this ).text() == d ){
                                    jQuery( this ).parent('td').addClass('WP-Cal-popup');
                                }
                            });

                        }
                        else{
                            jQuery( element ).parent('td').addClass('WP-Cal-popup');
                        }
                    });
                    jQuery('#wp-calendar .WP-Cal-popup').each( function (){
                        var year = jQuery(this).attr('data-year');
                        var month = jQuery(this).attr('data-month');
                        month = parseInt( month ) + 1;
                        var day = jQuery(this).find('a.ui-state-default').text();
                        var this_date = year + '-' + month + '-' + day;
                        var innerContent = this_posts[this_date];
                        if (/undefined/.test(innerContent)) {
                            jQuery(this).removeClass('WP-Cal-popup');
                        } else {
                            jQuery(this).append( '<div class="wp-cal-tooltip">'+innerContent+'</div>' );
                            jQuery(this).find('a.ui-state-default').attr('href', '?q=start_date:"'+year+'-'+month+'-'+day+'T00:00:00Z"');
                        }
                    });
                }
            });
        }
        jQuery('a.ui-state-default').click( function (e){
            e.stopPropagation();
        });
    }

    jQuery('.ui-datepicker-next, .ui-datepicker-prev, .wp-cal-prev, .wp-cal-next').live('click', function (){
        var Month = jQuery('#wp-calendar .ui-datepicker-month #monthnum').val();
        var Year = jQuery('#wp-calendar .ui-datepicker-year').text();
        Month = WP_Cal_convertMonth(Month);
        jQuery('a.query-month').attr('href', '?q=start_date:[' + Year + '-' + Month + '-01T00:00:00Z+TO+' + Year + '-' + Month + '-31T00:00:00Z]');
        WP_Cal_get_posts_by_month( Month, Year );
        monthFollowLink(Month, Year);
    });
    var c_date = new Date(), c_month = (parseInt(c_date.getMonth()) + 1), c_year = c_date.getFullYear();

    WP_Cal_get_posts_by_month(c_month, c_year);

    jQuery('.WP-Cal-popup').live({
        mouseenter: function() {
            jQuery(this).find('div').show();
        },
        mouseleave: function() {
            jQuery(this).find('div').hide();
        }
    });

    monthFollowLink(c_month, c_year);
});
