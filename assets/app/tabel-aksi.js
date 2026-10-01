/* ------------------------------------------------------------------
   Informasi tombol aksi saat kursor diarahkan (hover) atau saat tombol
   mendapat fokus keyboard.

   Cara pakai: tambahkan data-info="..." pada tombol ikon di dalam tabel
   (lihat assets/app/tabel-aksi.css), mis.:

       <button class="detail btn btn-sm btn-icon btn-primary"
               data-info="Lihat rincian penilaian" aria-label="Detail">...</button>

   Catatan:
   - Ditulis tanpa pustaka (vanilla JS) karena variabel global jQuery
     dilepas oleh tema (noConflict) sebelum skrip halaman dijalankan.
   - Penanganan memakai delegasi pada document, sehingga baris tabel yang
     disisipkan DataTables setelah halaman dimuat tetap mendapat informasi
     tanpa perlu diinisialisasi ulang.
   ------------------------------------------------------------------ */
(function () {
    'use strict';

    var PILIH = '.tabel-aksi [data-info]';
    var tip = null;

    function cariTombol(el) {
        while (el && el.nodeType === 1) {
            if (el.matches ? el.matches(PILIH) : false) {
                return el;
            }
            el = el.parentElement;
        }
        return null;
    }

    function kotakTip() {
        if (!tip) {
            tip = document.createElement('div');
            tip.className = 'tabel-info';
            tip.setAttribute('role', 'tooltip');
            tip.setAttribute('aria-hidden', 'true');
            document.body.appendChild(tip);
        }
        return tip;
    }

    function tampilkan(tombol) {
        var isi = tombol.getAttribute('data-info');
        if (!isi) {
            return;
        }

        var kotak = kotakTip();
        kotak.textContent = isi;
        kotak.setAttribute('aria-hidden', 'false');
        kotak.classList.add('tabel-info--tampil');

        // Letakkan di atas tombol, dijaga agar tetap berada di dalam layar.
        var t = tombol.getBoundingClientRect();
        var lebar = kotak.offsetWidth;
        var tinggi = kotak.offsetHeight;
        var lebarLayar = window.innerWidth;

        var kiri = t.left + (t.width / 2) - (lebar / 2);
        kiri = Math.max(8, Math.min(kiri, lebarLayar - lebar - 8));

        var atas = t.top - tinggi - 8;
        if (atas < 8) {
            // Ruang di atas tidak cukup: tampilkan di bawah tombol.
            atas = t.bottom + 8;
        }

        kotak.style.left = Math.round(kiri) + 'px';
        kotak.style.top = Math.round(atas) + 'px';
    }

    function sembunyikan() {
        if (tip) {
            tip.classList.remove('tabel-info--tampil');
            tip.setAttribute('aria-hidden', 'true');
        }
    }

    // Kursor masuk / keluar. mouseover-mouseout dipakai karena keduanya
    // menggelembung (bubble), jadi bisa ditangani lewat delegasi.
    document.addEventListener('mouseover', function (ev) {
        var tombol = cariTombol(ev.target);
        if (!tombol || (ev.relatedTarget && tombol.contains(ev.relatedTarget))) {
            return;
        }
        tampilkan(tombol);
    });

    document.addEventListener('mouseout', function (ev) {
        var tombol = cariTombol(ev.target);
        if (!tombol || (ev.relatedTarget && tombol.contains(ev.relatedTarget))) {
            return;
        }
        sembunyikan();
    });

    // Fokus keyboard (Tab).
    document.addEventListener('focusin', function (ev) {
        var tombol = cariTombol(ev.target);
        if (tombol) {
            tampilkan(tombol);
        }
    });

    document.addEventListener('focusout', function (ev) {
        if (cariTombol(ev.target)) {
            sembunyikan();
        }
    });

    // Saat halaman digeser/diubah ukurannya, posisi tooltip tidak lagi tepat.
    window.addEventListener('scroll', sembunyikan, true);
    window.addEventListener('resize', sembunyikan);
})();
