/**
 * Created by kaituo on 2017/12/18.
 */
$(function () {
  $('.listbtn').click(function () {
    $(this).find('+.lists').slideToggle();
    if($(this).attr('src')=="../img/rightarr.png"){
      $(this).attr('src',"../img/xiajiantou.png")
    }else {
      $(this).attr('src',"../img/rightarr.png")
    }
  });
  $('#send-chat').click(function() {

    $('#send-content').focus();

  });


  // var scroll = document.getElementById("scroll");
  // var bar = scroll.children[0];
  // var mask = scroll.children[1];
  // var demo = document.getElementById("demo");
  //
  // touch.on( bar, 'touchstart', function (event) {
  //   console.log(111)
  //   var that = this;
  //   var event = event || window.event;
  //   var top = event.clientY - this.offsetTop;
  //
  //
  //
  //   touch.on(bar,'touchmove',function (event) {
  //     var event = event || window.event;
  //     that.style.top = event.clientY - top + "px";
  //
  //
  //     var val = parseInt(that.style.top);
  //     if(val < 0){
  //       that.style.top = 0;
  //     } else if(val > 390){
  //       that.style.top = "390px";
  //     }
  //     mask.style.height = that.style.top;
  //
  //     // demo.innerHTML = parseInt(parseInt(that.style.top)/390*100)+"%";
  //
  //     window.getSelection ? window.getSelection().removeAllRanges() : document.selection.empty();       //防止选择拖动，前者正常写法，后者document.selection.empty()兼容IE678写法
  //
  //   })
  //   touch.on(document,'touchend',function (e) {
  //     mask.style.height = that.style.top;
  //     e.preventDefault();
  //     return false;
  //   })
  // } );


})