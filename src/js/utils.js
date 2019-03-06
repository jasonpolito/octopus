import lazysizes from 'lazysizes'
import Slideout from 'slideout'
import feather from 'feather-icons'

export const generateIcons = () => {
  feather.replace();
}

export const assignActiveLinks = (newUrl) => {
  let activeLinks = document.querySelectorAll('a.active');
  activeLinks.forEach(link => {
      link.classList.remove('active')
  });
  let links = document.querySelectorAll('nav a');
  let activeTarget = document.querySelector('[data-active-links]') ? document.querySelector('[data-active-links]').getAttribute('data-active-links') : 'zzzzzzz';
  links.forEach(link => {
      if (newUrl) {
      console.log(newUrl.indexOf(link.href))
      }
      if (link.href.indexOf(activeTarget) > -1 || (newUrl ? newUrl : window.location.href).indexOf(link.href) > -1) {
          link.classList.add('active')
      }
  });
}

export const attachSmoothScroll = () => {
  let scrollToBtns = document.querySelectorAll('[smooth-scroll]')
  if (scrollToBtns.length) {
      scrollToBtns.forEach(btn => {
          btn.addEventListener('click', e => {
              e.preventDefault();
              let target = document.querySelector(btn.getAttribute('href'))
              window.scrollTo(document.documentElement, target.offsetTop, 200);
          })
      })
  }
}

export const lazyLoadImages = () => {
  document.addEventListener('lazybeforeunveil', function(e) {
    var bg = e.target.getAttribute('data-bg');
    if(bg){
      e.target.style.backgroundImage = 'url(' + bg + ')';
    }
  })
}