/* =============================================================================
   Perilaku bersama tampilan topik & activity.

   - Klik kepala topik untuk membuka/menutup (disimpan di localStorage agar
     tidak hilang saat halaman dimuat ulang setelah menyimpan topik).
   - Kotak pencarian menyaring topik dan activity sekaligus.
   - Pembantu penomoran ulang topik setelah aktivitas dipindah/dihapus.

   Tanpa dependensi jQuery: status buka/tutup disimpan sebagai kelas CSS.
   ============================================================================= */
(function () {
    'use strict';

    var KUNCI = 'topik_terlipat';

    function bacaTerlipat() {
        try {
            return JSON.parse(window.localStorage.getItem(KUNCI) || '{}') || {};
        } catch (e) {
            return {};
        }
    }

    function simpanTerlipat(peta) {
        try {
            window.localStorage.setItem(KUNCI, JSON.stringify(peta));
        } catch (e) {
            /* localStorage tidak tersedia: abaikan. */
        }
    }

    /* Tombol naik/turun tiap activity dimatikan di ujung urutan. */
    function aturTombolActivity(topik) {
        var $ = window.jQuery;
        if (!$ || !topik) {
            return;
        }
        $(topik).find('.aktivitas').each(function () {
            var baris = $(this);
            baris.find('.aktivitas-naik').prop('disabled', baris.prevAll('.aktivitas').length === 0);
            baris.find('.aktivitas-turun').prop('disabled', baris.nextAll('.aktivitas').length === 0);
        });
    }

    function aturUrutanTopik() {
        var $ = window.jQuery;
        if (!$) {
            return;
        }
        $('#topik_daftar .topik').each(function () {
            var topik = $(this);
            topik.find('.topik-nomor').first().text(topik.index() + 1);
            var sebelumnya = topik.prevAll('.topik').first();
            var sesudahnya = topik.nextAll('.topik').first();
            topik.find('.topik-naik').prop('disabled', sebelumnya.length === 0);
            topik.find('.topik-turun').prop('disabled', sesudahnya.length === 0);
        });
    }

    function saring() {
        var kotak = document.getElementById('cari_topik');
        if (!kotak) {
            return;
        }
        var q = (kotak.value || '').trim().toLowerCase();

        var topikList = document.querySelectorAll('#topik_daftar .topik');
        var tampil = 0;
        Array.prototype.forEach.call(topikList, function (topik) {
            var topikCocok = !q || (topik.getAttribute('data-cari') || '').indexOf(q) !== -1;
            var activity = topik.querySelectorAll('.aktivitas');
            var activityTampil = 0;

            Array.prototype.forEach.call(activity, function (baris) {
                var cocok = topikCocok || (baris.getAttribute('data-cari') || '').indexOf(q) !== -1;
                baris.classList.toggle('topik-disembunyikan', !cocok);
                if (cocok) {
                    activityTampil++;
                }
            });

            var pakai = topikCocok || activityTampil > 0;
            topik.classList.toggle('topik-disembunyikan', !pakai);
            if (pakai) {
                tampil++;
                if (q) {
                    topik.classList.remove('topik-terlipat');
                }
            }
        });

        var kosong = document.getElementById('topik_cari_kosong');
        if (kosong) {
            kosong.style.display = (q && tampil === 0) ? '' : 'none';
        }
    }

    function pasang() {
        var daftar = document.getElementById('topik_daftar');
        if (!daftar) {
            return;
        }

        var terlipat = bacaTerlipat();

        Array.prototype.forEach.call(daftar.querySelectorAll('.topik'), function (topik) {
            var id = topik.getAttribute('data-dtform_id');
            if (terlipat[id]) {
                topik.classList.add('topik-terlipat');
            }

            var kepala = topik.querySelector('.topik-kepala');
            if (kepala) {
                kepala.addEventListener('click', function (ev) {
                    if (ev.target.closest('.topik-aksi') || ev.target.closest('button')) {
                        return;
                    }
                    topik.classList.toggle('topik-terlipat');
                    terlipat[id] = topik.classList.contains('topik-terlipat');
                    simpanTerlipat(terlipat);
                });
            }
        });

        var kotak = document.getElementById('cari_topik');
        if (kotak) {
            kotak.addEventListener('input', saring);
        }

        aturUrutanTopik();
    }

    window.TopikAktivitas = {
        pasang: pasang,
        saring: saring,
        aturUrutanTopik: aturUrutanTopik,
        aturTombolActivity: aturTombolActivity
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', pasang);
    } else {
        pasang();
    }
})();
