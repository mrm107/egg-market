// Achar JavaScript Document
// Hassan Zanjani - +98 919 151 5145 - hassanzanjani1374@gmail.com

//config js
var fileplace = "http://localhost/achar/code/";

//current time
function showtime() {
    var hours = currentTime.getHours();
    var minutes = currentTime.getMinutes();
    var seconds = currentTime.getSeconds();
    if (hours < 10) {
        hours = "0" + hours;
    }
    if (minutes < 10) {
        minutes = "0" + minutes;
    }
    if (seconds < 10) {
        seconds = "0" + seconds;
    }
    $("#clock-place").html(seconds + " <span style='color:#c5cbca;'>:</span> " + minutes + " <span style='color:#c5cbca;'>:</span> " + hours);
    currentTime.setSeconds(currentTime.getSeconds() + 1);
    t = setTimeout(function () {
        showtime()
    }, 1000);
}

//disable cashe
$.ajaxSetup({cache: false});

//run scripts
$(document).ready(function () {

    if ($(window).width() > 1140) {
        $('.sidebar').sidebar('show');
    }
    $('#sidebar-togller').html('<i class="column layout icon"></i>');

    $('#sidebar-togller , #sidebar-togller-sex').on('click', function () {
        $('.sidebar').sidebar('toggle');
    });

    //logo transition
    $('#logo-place').on('mouseenter', function () {
        $('#achar-logo').transition('tada');
    });
    $('#logo-place').on('click', function () {
        $('#achar-logo').transition('shake');
    });

    //accordion
    (function ($) {
        $.each(['show', 'hide'], function (i, ev) {
            var el = $.fn[ev];
            $.fn[ev] = function () {
                // this.trigger(ev);
                return el.apply(this, arguments);
            };
        });
    })(jQuery);
    var last = -5;
    $('aside .accordion .content').on('show', function () {
        h = $(this).outerHeight();
        t = $(this).prev().position().top;
        //console.log(t);
        th = $(this).prev().outerHeight();
        //console.log(last);
        wh = $(window).innerHeight();
        rt = $('aside').scrollTop();
        //console.log( h );
        if (t + h + th > wh) {
            $('aside').animate({scrollTop: rt + t - last - 5}, 300);
        } else {
            if (t < 5) {
                $('aside').animate({scrollTop: rt + t + h - last}, 300);
            }
        }
        last = h;
    });
    $('.ui.accordion').accordion();

    //checkboxes
    $('.ui.checkbox').checkbox();

    //page-naver fix height
    var boxheight = $('#pages-nav').height();
    var spanheight = Math.floor((boxheight - 15) / 2) - 1;
    $('#pages-nav a.first-child').css({'height': boxheight});
    $('#pages-nav a.last-child').css({'height': boxheight});
    $('#pages-nav a span').css({'margin-top': spanheight});

    //count of notices and notice reloader
    if ($("#all-count-sidebar").text() != 0) {
        $("#count-first-sidebar").append("<span class='custome-number-label'>" + $("#all-count-sidebar").html() + "</span>");
        var noticehistory = $("#all-count-sidebar").html();
    } else {
        var noticehistory = 0;
    }

    setInterval(function () {
        $.get("../../Controller/admin.ajax.php?notice-reloader=start", function (dashboard) {
            $dashboard = dashboard;
            $("#count-first-sidebar").empty().append($dashboard['title']);
            $("#messages-count").empty().append($dashboard['messages-count']);
            if (noticehistory < $dashboard['all-count']) {
                $("audio#notice-alert")[0].play();
            }
            noticehistory = $dashboard['all-count'];
        }, 'json');
    }, 10000);

});

//responsive fixer 
$(window).resize(function () {
    if ($(window).width() > 1140)
        $('.sidebar').sidebar('show'), $('#sidebar-togller').html('<i class="column layout icon turn-90deg"></i>');
    else
        $('.sidebar').sidebar('hide'), $('#sidebar-togller').html('<i class="column layout icon"></i>');
});

//checker
function delete_checker() {
    if (!confirm("آیا اطمینان دارید که می خواهید این مورد (ها) را حذف کنید ؟")) return false;
    return true;
}

function custome_checker(message) {
    if (!confirm(message)) return false;
    return true;
}

$(document).ready(function () {
    //close alert box
    $('.good-alert').delay(300).animate({width: 200, marginLeft: 5}, 300).delay(10000).animate({
        width: 0,
        marginLeft: 0
    }, 300);
    $('.bad-alert').delay(300).animate({width: 250, marginLeft: 5}, 300).delay(10000).animate({
        width: 0,
        marginLeft: 0
    }, 300);

    //auto form submit
    var autosender = function (element) {
        $(element).change(function () {
            this.form.submit();
        });
    }
    autosender('.header-autosender');
});

//checking all checkbox
$(document).ready(function () {
    var checker = function () {
        if ($('#buttselectall input[type=checkbox]').is(':checked')) {
            $('#boxselectall input[type=checkbox]').prop('checked', false);
        } else {
            $('#boxselectall input[type=checkbox]').prop('checked', true);
        }
    }
    $("#buttselectall").on("click", function () {
        checker();
    });
    $("#buttselectall input[type=checkbox]").on("click", function () {
        checker();
    });
});
