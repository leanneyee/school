/**
 * Drives the top and bottom fade overlays for the events listing scroll panel.
 *
 * - Bottom fade: visible when there is content below the fold, hidden at end.
 * - Top fade:    hidden when at the top, revealed once the user scrolls down.
 *
 * Re-evaluates via ResizeObserver so it responds to container resizes and
 * to new items being appended (e.g. Views Infinite Scroll).
 */
(function (Drupal, once) {
  Drupal.behaviors.eventsScroll = {
    attach(context) {
      once('events-scroll', '[data-events-scroll]', context).forEach((section) => {
        const topFade = section.querySelector('[data-events-scroll-top-fade]');
        const botFade = section.querySelector('[data-events-scroll-fade]');

        const update = () => {
          const scrollable = section.scrollHeight > section.clientHeight + 2;
          const atTop      = section.scrollTop < 2;
          const atBottom   = section.scrollHeight - section.scrollTop <= section.clientHeight + 2;

          if (topFade) topFade.style.opacity = (!scrollable || atTop)    ? '0' : '1';
          if (botFade) botFade.style.opacity = (!scrollable || atBottom) ? '0' : '1';
        };

        update();
        section.addEventListener('scroll', update, { passive: true });
        new ResizeObserver(update).observe(section);
      });
    },
  };
})(Drupal, once);
