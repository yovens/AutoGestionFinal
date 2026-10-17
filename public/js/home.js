/*
 * Page d’accueil en scrollytelling.
 * GSAP + ScrollTrigger (animations liées au scroll), SplitText (titres), Lenis (scroll fluide).
 * Si les bibliothèques manquent ou si l’utilisateur demande moins de mouvement, rien ne s’exécute :
 * la page reste complète et statique (voir CSS : tout l’état final est l’état par défaut).
 */
(function () {
  'use strict';

  var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (reduce || !window.gsap || !window.ScrollTrigger) return;

  var gsap = window.gsap;
  var ST = window.ScrollTrigger;
  gsap.registerPlugin(ST);
  if (window.SplitText) gsap.registerPlugin(window.SplitText);

  var root = document.documentElement;
  root.classList.add('motion');

  /* ---------- Scroll fluide (Lenis), synchronisé avec ScrollTrigger ---------- */
  var lenis = null;
  if (window.Lenis) {
    lenis = new window.Lenis({ duration: 1.15, smoothWheel: true, anchors: { offset: -64 } });
    lenis.on('scroll', ST.update);
    gsap.ticker.add(function (t) { lenis.raf(t * 1000); });
    gsap.ticker.lagSmoothing(0);
  }

  var head = document.querySelector('[data-home-head]');

  /* ---------- 1. Hero : la plaque s’ouvre sur la photo ---------- */
  var hero = document.querySelector('[data-hero]');
  if (hero) {
    var stage = hero.querySelector('[data-hero-stage]');
    var frame = hero.querySelector('[data-hero-frame]');
    var win = hero.querySelector('[data-hero-window]');
    var img = hero.querySelector('[data-hero-img]');
    var title = hero.querySelector('[data-hero-title]');
    var shade = hero.querySelector('[data-hero-shade]');
    var caption = hero.querySelector('[data-hero-caption]');
    var hint = hero.querySelector('[data-hero-hint]');
    var end = hero.querySelector('[data-hero-end]');

    // Rectangle de la fenêtre-plaque dans la scène → valeurs de clip-path
    function insetFromWindow() {
      var s = stage.getBoundingClientRect(), w = win.getBoundingClientRect();
      return 'inset(' + (w.top - s.top) + 'px ' + (s.right - w.right) + 'px ' + (s.bottom - w.bottom) + 'px ' + (w.left - s.left) + 'px round 18px)';
    }

    // Entrée au chargement : les lignes du titre montent, la plaque se pose
    var lines = title.querySelectorAll('.hx__line');
    gsap.set(frame, { clipPath: insetFromWindow() });
    var intro = gsap.timeline({ defaults: { ease: 'power3.out' } });
    intro.from(lines, { yPercent: 110, opacity: 0, duration: 1, stagger: .12 })
         .from([win, frame], { scale: .92, opacity: 0, duration: 1 }, .15)
         .from([caption, hint].filter(Boolean), { opacity: 0, y: 12, duration: .8, stagger: .1 }, .5);
    if (head) intro.from(head, { opacity: 0, y: -12, duration: .6 }, .2);

    var tl = gsap.timeline({
      defaults: { ease: 'none' },
      scrollTrigger: { trigger: hero, start: 'top top', end: '+=170%', scrub: .6, pin: stage, anticipatePin: 1, invalidateOnRefresh: true }
    });
    tl.fromTo(frame, { clipPath: function () { return insetFromWindow(); } }, { clipPath: 'inset(0px 0px 0px 0px round 0px)', duration: .6 }, 0)
      .fromTo(img, { scale: 1.18 }, { scale: 1, duration: .6 }, 0)
      .to(win, { opacity: 0, scale: 1.08, duration: .18 }, 0)
      .to(title, { yPercent: -60, opacity: 0, duration: .32 }, 0)
      .to([caption, hint].filter(Boolean), { opacity: 0, duration: .15 }, 0)
      .to(shade, { opacity: 1, duration: .3 }, .45)
      .fromTo(end, { opacity: 0, y: 40 }, { opacity: 1, y: 0, duration: .25 }, .62)
      .to({}, { duration: .2 }); // petite pause en fin de pin

    // Header : transparent sur le hero, plein ensuite
    if (head) {
      ST.create({ trigger: hero, start: 'bottom top+=72', onEnter: function () { head.classList.add('is-solid'); }, onLeaveBack: function () { head.classList.remove('is-solid'); } });
    }
  }

  /* ---------- 2. Le parc : défilement horizontal (grand écran) ---------- */
  var fleet = document.querySelector('[data-fleet]');
  var track = fleet && fleet.querySelector('[data-fleet-track]');
  if (track) {
    var mm = gsap.matchMedia();
    mm.add('(min-width: 800px)', function () {
      root.classList.add('motion-x');
      var pin = fleet.querySelector('[data-fleet-pin]');
      var bar = fleet.querySelector('[data-fleet-bar]');
      var distance = function () { return Math.max(0, track.scrollWidth - window.innerWidth); };

      var move = gsap.to(track, {
        x: function () { return -distance(); },
        ease: 'none',
        scrollTrigger: { trigger: fleet, start: 'top top', end: function () { return '+=' + distance(); }, scrub: .8, pin: pin, invalidateOnRefresh: true, anticipatePin: 1 }
      });
      if (bar) gsap.to(bar, { scaleX: 1, ease: 'none', scrollTrigger: { trigger: fleet, start: 'top top', end: function () { return '+=' + distance(); }, scrub: true } });

      // Parallaxe dans chaque photo pendant que la carte traverse l’écran
      track.querySelectorAll('[data-fleet-img]').forEach(function (im) {
        gsap.fromTo(im, { xPercent: -6 }, { xPercent: 6, ease: 'none', scrollTrigger: { trigger: im.closest('.fcard'), containerAnimation: move, start: 'left right', end: 'right left', scrub: true } });
      });

      return function () { root.classList.remove('motion-x'); gsap.set(track, { clearProps: 'x' }); };
    });
  }

  /* ---------- 4. Bande chiffres : parallaxe + compteurs ---------- */
  var band = document.querySelector('[data-band]');
  if (band) {
    var bg = band.querySelector('[data-band-img]');
    if (bg) gsap.fromTo(bg, { yPercent: -10 }, { yPercent: 10, ease: 'none', scrollTrigger: { trigger: band, start: 'top bottom', end: 'bottom top', scrub: true } });

    var fmt = new Intl.NumberFormat('fr-FR', { maximumFractionDigits: 0 });
    band.querySelectorAll('[data-count]').forEach(function (el) {
      var target = Number(el.dataset.count) || 0;
      var suffix = el.dataset.suffix ? el.dataset.suffix.replace('&nbsp;', ' ') : '';
      var o = { v: 0 };
      el.textContent = '0' + suffix;
      gsap.to(o, { v: target, duration: 1.4, ease: 'power2.out', scrollTrigger: { trigger: el, start: 'top 85%', once: true },
        onUpdate: function () { el.textContent = fmt.format(Math.round(o.v)) + suffix; } });
    });
  }

  /* ---------- Titres révélés ligne par ligne ---------- */
  document.querySelectorAll('[data-reveal]').forEach(function (h) {
    if (window.SplitText) {
      var split = new window.SplitText(h, { type: 'lines', linesClass: 'split-line', mask: 'lines' });
      gsap.from(split.lines, { yPercent: 105, duration: .9, ease: 'power3.out', stagger: .1, scrollTrigger: { trigger: h, start: 'top 85%', once: true } });
    } else {
      gsap.from(h, { y: 30, opacity: 0, duration: .8, scrollTrigger: { trigger: h, start: 'top 85%', once: true } });
    }
  });

  // Recalculer après chargement des images / polices (hauteurs définitives)
  window.addEventListener('load', function () { ST.refresh(); });
  if (document.fonts && document.fonts.ready) document.fonts.ready.then(function () { ST.refresh(); });
})();
