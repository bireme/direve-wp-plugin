(function( $ ) {
    'use strict';

    /**
     * All of the code for your public-facing JavaScript source
     * should reside in this file.
     *
     * Note: It has been assumed you will write jQuery code here, so the
     * $ function reference has been prepared for usage within the scope
     * of this function.
     *
     * This enables you to define handlers, for when the DOM is ready:
     *
     * $(function() {
     *
     * });
     *
     * When the window is loaded:
     *
     * $( window ).load(function() {
     *
     * });
     *
     * ...and/or other possibilities.
     *
     * Ideally, it is not considered best practise to attach more than a
     * single DOM-ready or window-load handler for a particular page.
     * Although scripts in the WordPress core, Plugins and Themes may be
     * practising this, we should strive to set a better example in our own work.
     */

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
        var range = monthRange(month, year);
        $('.ui-datepicker-title span').wrapAll('<a class="query-month" />');
        $('a.query-month').attr('href', '?q=start_date:[' + year + '-' + month + '-01T00:00:00Z+TO+' + year + '-' + month + '-' + range + 'T00:00:00Z]');
        $('.ui-datepicker-month').after("&nbsp;");
    }

    $(document).ready(function($){
        $('#wp-calendar').datepicker();
        $('#wp-calendar a.ui-state-active').addClass('wp-calendar-current-date').parent('td').addClass('wp-calendar-today-active');
        
        function WP_Cal_get_posts_by_month(month, year){
            var check_date = month+'-'+year;
            var found_at = $.inArray( check_date , wp_cal_dates );

            if( found_at == -1 ){
                // $(".wp_calendar .calendar_wrap_loading").removeClass('calendar_wrap_loading_hide').addClass('calendar_wrap_loading_show');
                $(".wp_calendar .circle_loading").removeClass('hide_circle').addClass('show_circle');
                $('#wp-calendar .ui-datepicker-prev').toggle();
                $('#wp-calendar .ui-datepicker-next').toggle();
                $('#calendar_wrap .calendar-pagi a').each(function(){
                    $(this).removeClass('pagi_state_show').toggle();
                });
                $('a.ui-state-default').removeAttr('href').css('text-decoration', 'none');
                
                wp_cal_dates.push(check_date);
                $.ajax({
                    type: 'POST',
                    url : wpCalendarObj.ajaxurl,
                    data: {
                        action:   'wp_calendar_get_events',
                        ajax  :   'true',
                        month :   month,
                        year  :   year
                    },
                    success :   function( data ){
                        data = $.parseJSON( data );
                        var this_dates = data.classes;
                        var curr_date = new Array();
                        var d = '';
                        $.each( this_dates, function (key , value){
                            curr_date = key.split('-');
                            d = curr_date[2];
                            var element = $('#wp-calendar a.ui-state-default:contains("'+d+'")');
                            if( element.length > 1 ){
                                $(element).each( function(){
                                    if( $( this ).text() == d ){
                                        $( this ).parent('td').addClass('WP-Cal-popup');
                                    }
                                });
                                
                            }
                            else{
                                $( element ).parent('td').addClass('WP-Cal-popup');
                            }
                        });
                        $('#wp-calendar .WP-Cal-popup').each( function (){
                            var year = $(this).attr('data-year');
                            var month = $(this).attr('data-month');
                            month = parseInt( month ) + 1;
                            var day = $(this).find('a.ui-state-default').text();
                            $(this).find('a.ui-state-default').attr('href', '?q=start_date:"'+year+'-'+month+'-'+day+'T00:00:00Z"');
                        });
                        var new_data_element = {
                            month : check_date, 
                            classes : this_dates, 
                        };
                        wp_cal_posts.push( new_data_element );
                    },
                    complete: function() {
                        $('.ui-datepicker-next, .ui-datepicker-prev, .wp-cal-prev, .wp-cal-next').bind('click');
                        // $(".wp_calendar .calendar_wrap_loading").removeClass('calendar_wrap_loading_show').addClass('calendar_wrap_loading_hide');
                        $(".wp_calendar .circle_loading").removeClass('show_circle').addClass('hide_circle');
                        $('#wp-calendar .ui-datepicker-prev').toggle();
                        $('#wp-calendar .ui-datepicker-next').toggle();
                        $('#calendar_wrap .calendar-pagi a').each(function(){
                            $(this).removeClass("pagi_state_hide").toggle();
                        });
                    }
                });
            } else {
                var this_dates = {};
                
                $( wp_cal_posts ).each( function (){
                    if( this.month == check_date ){
                        this_dates = this.classes;
                        var curr_date = new Array();
                        var d = '';
                        $.each( this_dates, function (key , value){
                            curr_date = key.split('-');
                            d = curr_date[2];
                            var element = $('#wp-calendar a.ui-state-default:contains("'+d+'")');
                            if( element.length > 1 ){
                                $(element).each( function(){
                                    if( $( this ).text() == d ){
                                        $( this ).parent('td').addClass('WP-Cal-popup');
                                    }
                                });

                            }
                            else{
                                $( element ).parent('td').addClass('WP-Cal-popup');
                            }
                        });
                        $('#wp-calendar .WP-Cal-popup').each( function (){
                            var year = $(this).attr('data-year');
                            var month = $(this).attr('data-month');
                            month = parseInt( month ) + 1;
                            var day = $(this).find('a.ui-state-default').text();
                            $(this).find('a.ui-state-default').attr('href', '?q=start_date:"'+year+'-'+month+'-'+day+'T00:00:00Z"');
                        });
                    }
                });
            }

            $('.ui-datepicker-next, .ui-datepicker-prev, .wp-cal-prev, .wp-cal-next').on('click', function (){
                var Month = $('#wp-calendar .ui-datepicker-month #monthnum').val();
                var Year = $('#wp-calendar .ui-datepicker-year').text();
                Month = WP_Cal_convertMonth(Month);
                $('a.query-month').attr('href', '?q=start_date:[' + Year + '-' + Month + '-01T00:00:00Z+TO+' + Year + '-' + Month + '-31T00:00:00Z]');
                WP_Cal_get_posts_by_month( Month, Year );
                monthFollowLink(Month, Year);
            });

            $('a.ui-state-default').click( function (e){
                e.stopPropagation();
            });
        }

        var c_date = new Date(), c_month = (parseInt(c_date.getMonth()) + 1), c_year = c_date.getFullYear();
        WP_Cal_get_posts_by_month(c_month, c_year);

        $('.WP-Cal-popup').on({
            mouseenter: function() {
                $(this).find('div').css( "display", "inline-table" );
            },
            mouseleave: function() {
                $(this).find('div').hide();
            }
        });

        monthFollowLink(c_month, c_year);
    });

})( jQuery );