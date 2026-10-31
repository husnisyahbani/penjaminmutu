/* ------------------------------------------------------------------
   Menu aksi pada tabel: satu tombol (ikon titik tiga) yang membuka
   daftar pilihan aksi untuk baris tersebut - dipakai laman Akun (admin).

   Cara pakai (lihat juga assets/app/tabel-aksi.css):

       <div class="tabel-aksi">
         <button type="button" class="btn btn-sm btn-icon btn-default tabel-aksi-buka"
                 id="aksi12-tombol" data-tabel-aksi-menu="aksi12"
                 aria-haspopup="true" aria-expanded="false" aria-controls="aksi12"
                 data-info="Pilihan aksi untuk akun ini" aria-label="Pilihan aksi">
           <i class="icon md-more-vert" aria-hidden="true"></i>
         </button>
         <div class="tabel-aksi-menu" id="aksi12" role="menu" aria-labelledby="aksi12-tombol">
           <button type="button" class="tabel-aksi-item reset" id="12">
             <i class="icon md-refresh" aria-hidden="true"></i><span>Reset password</span>
           </button>
         </div>
       </div>

   Catatan:
   - Ditulis tanpa pustaka (vanilla JS) seperti assets/app/tabel-aksi.js.
   - Menu tetap berada di dalam sel tabel, tetapi saat dibuka posisinya
     diubah menjadi tetap (fixed) dengan koordinat yang dihitung dari
     tombolnya. Karena posisi tetap tidak dipotong oleh pembungkus
     tabel/DataTables, menu selalu tampil utuh dan menempel pada
     tombolnya.
   - Penanganan memakai delegasi pada document, sehingga baris yang
     disisipkan DataTables setelah halaman dimuat tetap berfungsi.
   ------------------------------------------------------------------ */
(function () {
    'use strict';

    var PENGATUR = '[data-tabel-aksi-menu]';
    var JEDA = 6;               // jarak menu dari tombolnya (px)
    var TEPI = 8;               // jarak minimum menu dari tepi layar (px)

    var menuTerbuka = null;
    var tombolTerbuka = null;

    function tombolDari(ev) {
        var el = ev.target;
        if (!el || el.nodeType !== 1) {
            return null;
        }
        return el.closest ? el.closest(PENGATUR) : null;
    }

    function tutup() {
        if (!menuTerbuka) {
            return;
        }
        menuTerbuka.classList.remove('tabel-aksi-menu--buka');
        menuTerbuka.style.left = '';
        menuTerbuka.style.top = '';
        menuTerbuka = null;

        if (tombolTerbuka) {
            tombolTerbuka.setAttribute('aria-expanded', 'false');
            tombolTerbuka = null;
        }
    }

    /* Letakkan menu di bawah tombol, rata kanan; bila ruang di bawah
       tidak cukup, menu dipindah ke atas tombol. */
    function aturPosisi(menu, tombol) {
        var t = tombol.getBoundingClientRect();
        var lebar = menu.offsetWidth;
        var tinggi = menu.offsetHeight;

        var kiri = t.right - lebar;
        kiri = Math.max(TEPI, Math.min(kiri, window.innerWidth - lebar - TEPI));

        var atas = t.bottom + JEDA;
        if (atas + tinggi > window.innerHeight - TEPI) {
            atas = t.top - tinggi - JEDA;
            if (atas < TEPI) {
                atas = Math.max(TEPI, window.innerHeight - tinggi - TEPI);
            }
        }

        menu.style.left = Math.round(kiri) + 'px';
        menu.style.top = Math.round(atas) + 'px';
    }

    function buka(tombol) {
        var id = tombol.getAttribute('data-tabel-aksi-menu');
        var menu = id ? document.getElementById(id)
                      : tombol.parentNode.querySelector('.tabel-aksi-menu');

        if (!menu) {
            return;
        }

        // Klik pada tombol yang sama: tutup kembali.
        if (menuTerbuka === menu) {
            tutup();
            return;
        }

        if (menuTerbuka) {
            tutup();
        }

        menu.classList.add('tabel-aksi-menu--buka');
        aturPosisi(menu, tombol);
        tombol.setAttribute('aria-expanded', 'true');
        menuTerbuka = menu;
        tombolTerbuka = tombol;
    }

    document.addEventListener('click', function (ev) {
        var tombol = tombolDari(ev);

        if (tombol) {
            ev.preventDefault();
            buka(tombol);
            return;
        }

        if (!menuTerbuka) {
            return;
        }

        // Klik di luar menu, atau pada salah satu pilihan: tutup menu.
        // Pilihan aksi sendiri tetap dijalankan oleh penangan di berkas JS
        // halaman yang bersangkutan.
        if (!menuTerbuka.contains(ev.target)) {
            tutup();
        } else if (ev.target.closest && ev.target.closest('.tabel-aksi-item')) {
            tutup();
        }
    });

    document.addEventListener('keydown', function (ev) {
        if (ev.key === 'Escape' || ev.key === 'Esc' || ev.keyCode === 27) {
            tutup();
        }
    });

    // Tabel digeser/diubah ukurannya: posisi menu tidak lagi tepat.
    window.addEventListener('resize', tutup);
    window.addEventListener('scroll', tutup, true);

    // Dipakai berkas JS halaman, mis. untuk menutup menu setiap kali
    // DataTables menggambar ulang tabelnya.
    window.tabelAksiMenu = {
        tutup: tutup
    };
})();
