$(function() {

    $("#menu-container li, #menu-container > li > ul > li").each(function() {
        if($("> ul", this).length) {
            $(this).mouseover(function() { $("> ul", this).show(); });
            $(this).mouseout(function() { $("> ul", this).hide(); });
        }
    });

});

