import SmoothScroll from 'smooth-scroll'
import {
    generateIcons,
    assignActiveLinks,
    lazyLoadImages,
    attachSmoothScroll,
} from '../utils'

export const SetupPage = (newUrl) => {
    assignActiveLinks(newUrl)
    generateIcons()
    lazyLoadImages()
    var scroll = new SmoothScroll('a[href*="#"]', {
        speed: 300,
        offset(a,b) {
            let paddingTop = parseInt(window.getComputedStyle(a, null).getPropertyValue('padding-top').replace('px', ''), 10)
            return 112 - paddingTop
        }
    });
}
