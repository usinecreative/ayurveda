$(document).ready(function () {
        $('#horizontalTab').responsiveTabs({
            active: 0,
            startCollapsed: 'accordion',
            collapsible: 'accordion',
            rotate: false,
            animation: 'fade',
            setHash: true
        });
        $("a.reveal-link").click(function(){$('#myModal').foundation('reveal', 'open');});
        $("a.close-reveal-modal").click(function(){$('#myModal').foundation('reveal', 'close');});
        $(".up").click(function(){
            $("html").animate({ scrollTop: 0 }, "fast");
            return false;
        });
    });
$(document).foundation({orbit: { }});