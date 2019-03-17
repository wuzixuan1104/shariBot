$(function () {
  $('.tabs').each(function() {
    var $panels = $(this).next();
    
    var $active = $(this).find('>a').click(function() {
      $(this).addClass('active').siblings().removeClass('active');
      $panels.attr('class', 'n' + $(this).index());
    }).filter('.active');

    $panels.attr('class', 'n' + $active.index());
  });
});