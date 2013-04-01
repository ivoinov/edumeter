$.fn.sevenTree = function(options) {
    
    var seven_tree = {
        tree_root: false,
        init: function(root, options) {
            this.tree_root = root;
            $('li:has("ul")', root).find('a:first').prepend('<em class="marker"></em>');
            $('li span', root).click(this.onNodeClick);

        },
        onNodeClick: function() {
            $('a.current').removeClass('current'); 
            var a = $('a:first',this.parentNode);
            a.toggleClass('current');
            var li = $(this.parentNode);
            if (!li.next().length) {
                li.find('ul:first > li').addClass('last');
            } 
            var ul=$('ul:first',this.parentNode);
            if (ul.length) {
                ul.slideToggle(300);
                var em=$('em:first',this.parentNode);
                em.toggleClass('open');
            }
        }
    };
    seven_tree.init(this, options);
    
};




$(document).ready(function () {
    $(".multi-tree").sevenTree();
});


