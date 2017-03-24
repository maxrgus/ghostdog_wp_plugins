jQuery(document).ready(function($){

  $('#header-slideshow > div:gt(0)').hide();

  setInterval(function() {
    $('#header-slideshow > :first-child')
      .fadeOut(3000)
      .next()
      .fadeIn(4000)
      .end()
      .appendTo('#header-slideshow');
  }, 6000);

});
