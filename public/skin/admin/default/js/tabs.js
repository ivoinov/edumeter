$(function() {
    $(".widget-tabs").each(function() {
          $(".widget-tabs-holder > div", this).hide().filter(":first").show();
          $("ul.widget-tabs-list > li", this).removeClass("selected").filter(":first").addClass("selected");
          $("ul.widget-tabs-list > li a[href^=#]", this).click(function() {
              $(this).parents(".widget-tabs").find(".widget-tabs-holder > div").hide();
              $(this).parents(".widget-tabs").find("ul.widget-tabs-list > li").removeClass("selected");
              $($(this).attr("href")).show();
              $(this).parents("li").addClass("selected");
              return false;
          });
    });


});