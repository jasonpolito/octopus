import WebFont from 'webfontloader'
import lazysizes from 'lazysizes'
import { SetupPage } from './utils/SetupPage'
import Barba from 'barba.js'
import { screens } from '../../tailwind-config'

const font = document.querySelector('[data-font-family]')

WebFont.load({
    google: {
      families: [font ? font.getAttribute('data-font-family') : 'Open Sans']
    }
});

window.lazySizesConfig = window.lazySizesConfig || {};
let sizes = {}
let screenKeys = Object.keys(screens)
let keyLength = screenKeys.length
for (let screen in screens) {
  let width = screens[screen];
  let index = screenKeys.indexOf(screen)
  let nextWidth = index == keyLength - 1 ? false : screens[screenKeys[index + 1]];
  sizes[`--${screen}`] = `(min-width: ${width})${ nextWidth ? 'and (max-width: ' + nextWidth + ')' : ''})`
}
window.lazySizesConfig.customMedia = sizes

function scrollTo(element, to, duration) {
    if (duration <= 0) return;
    let difference = to - element.scrollTop;
    let perTick = difference / duration * 10;

    setTimeout(function() {
        element.scrollTop = element.scrollTop + perTick;
        if (element.scrollTop === to) return;
        scrollTo(element, to, duration - 10);
    }, 10);
}

SetupPage();

let HideShowTransition = Barba.BaseTransition.extend({
  start: function() {
    this.newContainerLoading.then(this.finish.bind(this));
  },

  finish: function() {
    window.scroll(0, {});
    this.done();
  }
});

Barba.Pjax.getTransition = function() {
  return HideShowTransition;
};

Barba.Pjax.start();


var menuToggles = document.querySelectorAll('[data-toggle-menu]')
var mobileMenu = document.querySelector('.mobile-menu')

for (var i = 0; i < menuToggles.length; i++) {
  menuToggles[i].addEventListener('click', function(e){
    mobileMenu.classList[mobileMenu.classList.contains('active') ? 'remove' : 'add']('active')
  })
}

Barba.Dispatcher.on('transitionCompleted', function(currentStatus, oldStatus, container) {
  mobileMenu.classList.remove('active')
  if (window.gtag) {
    gtag('config', 'UA-80415861-3', {
      'page_title' : document.title,
      'page_path': window.location.pathname
    });
  }
  SetupPage(currentStatus.url);
});