/* ==========================================================================
   SIJAMU - perilaku halaman depan (homepage)
   Dipakai oleh application/modules/umum/views/homepage.php
   Tanpa dependency tambahan (bootstrap bundle sudah dimuat di view).
   ========================================================================== */
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    var nav = document.getElementById('hpNav');
    var toTop = document.getElementById('hpTop');
    var collapseEl = document.getElementById('hpMenu');

    /* ---------- navbar: menempel saat halaman digulir ---------- */
    function onScroll() {
      var y = window.scrollY || document.documentElement.scrollTop;

      if (nav) {
        nav.classList.toggle('is-sticky', y > 40);
      }
      if (toTop) {
        toTop.classList.toggle('is-visible', y > 520);
      }
      highlight();
    }

    /* ---------- sorot menu sesuai section yang terlihat ---------- */
    var sections = [].slice.call(document.querySelectorAll('main section[id]'));

    function highlight() {
      if (!sections.length) { return; }

      var pos = window.scrollY + 140;
      var current = sections[0].id;

      sections.forEach(function (section) {
        if (section.offsetTop <= pos) { current = section.id; }
      });

      document.querySelectorAll('.hp-nav__link[data-target]').forEach(function (link) {
        link.classList.toggle('active', link.getAttribute('data-target') === current);
      });
    }

    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', highlight);
    onScroll();

    /* ---------- tombol kembali ke atas ---------- */
    if (toTop) {
      toTop.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: 'smooth' });
      });
    }

    /* ---------- tutup menu mobile setelah memilih tautan ---------- */
    if (collapseEl && window.bootstrap) {
      collapseEl.querySelectorAll('a:not(.dropdown-toggle)').forEach(function (link) {
        link.addEventListener('click', function () {
          if (collapseEl.classList.contains('show')) {
            window.bootstrap.Collapse.getOrCreateInstance(collapseEl).hide();
          }
        });
      });
    }

    /* ---------- animasi muncul saat digulir ---------- */
    var revealEls = document.querySelectorAll('[data-reveal]');

    if ('IntersectionObserver' in window && revealEls.length) {
      var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-revealed');
            observer.unobserve(entry.target);
          }
        });
      }, { threshold: 0.12, rootMargin: '0px 0px -60px 0px' });

      revealEls.forEach(function (el) { observer.observe(el); });
    } else {
      revealEls.forEach(function (el) { el.classList.add('is-revealed'); });
    }

    /* ---------- carousel galeri profil ---------- */
    var galeri = document.getElementById('hpGaleri');
    if (galeri && window.bootstrap) {
      window.bootstrap.Carousel.getOrCreateInstance(galeri, { interval: 5200, ride: 'carousel' });
    }

    /* ---------- tahun otomatis pada footer ---------- */
    document.querySelectorAll('[data-tahun]').forEach(function (el) {
      el.textContent = new Date().getFullYear();
    });
  });
})();
