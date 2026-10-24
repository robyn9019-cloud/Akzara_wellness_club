import { initHeader } from './header.js';
import { initReveal, initFadeImages, initCursor, initNavProgress, initHeroVideo } from './motion.js';
import { initRitualFilters, initGallery, initPromo } from './pages.js';
import { initDatePickers } from './datepicker.js';
import { initGift } from './gift.js';

const run = () => {
  initHeader();
  initFadeImages();
  initReveal();
  initCursor();
  initNavProgress();
  initHeroVideo();
  initDatePickers();
  initRitualFilters();
  initGallery();
  initPromo();
  initGift();
};

if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', run);
else run();
