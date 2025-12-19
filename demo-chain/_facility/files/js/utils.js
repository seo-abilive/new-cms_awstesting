window.Utils = {
  magnificPopupConfiguration: function() {
    var movefun = function( event ){
      event.preventDefault();
    }

    return {
      beforeOpen: function() {
        window.addEventListener( 'touchmove' , movefun , { passive: false } );
      },
      close: function() {
        window.removeEventListener( 'touchmove' , movefun, { passive: false } );
      }
    }
  }
}