(function($) {
    
    $(function () {
        $("#sitemap-tree > ul").simpleTreeMenu();
        $("#sitemap-tree > ul").simpleTreeMenu('expandAll');
        $("#sitemap-tree > ul a").click(function(e) { e.stopPropagation(); });
    });
    
})(jQuery);