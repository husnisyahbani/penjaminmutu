/* =============================================================================
   Halaman detail audit auditee: jawaban + lampiran tiap BUTIR LINGKUP.

   - Daftar pertanyaan yang dijawab berasal dari tabel mutu_lingkup: satu
     pertanyaan formulir bisa punya banyak butir lingkup dan TIAP butir
     dijawab sendiri-sendiri (mutu_auditjawab.lingkup_id terisi).
   - Pertanyaan yang belum punya butir lingkup memakai kotak jawaban tingkat
     pertanyaan (data lama) - kotaknya bertanda data-wajib="1" juga.
   - Penilaian auditor (hasil/temuan/catatan) hanya ditampilkan.
   - Lampiran opsional dan boleh lebih dari satu berkas per butir lingkup.
   - Tombol "Kirim Hasil Evaluasi" ditolak server selama masih ada butir
     lingkup yang belum dijawab, dan pesannya ditampilkan di sini.
   ============================================================================= */
$(function () {

    function pesan(judul, teks, tipe) {
        swal.fire(judul, teks, tipe || 'info');
    }

    function muat() {
        swal.fire({
            title: 'Loading',
            allowEscapeKey: false,
            allowOutsideClick: false,
            didOpen: function () { swal.showLoading(); }
        });
    }

    function auditId() {
        return $('#kirim_hasil').attr('audit_id') || $('.delik').first().attr('audit_id');
    }

    /* ------------------- indikator jumlah jawaban & lampiran -------------------

       Angka dihitung ulang dari tampilan setiap kali ada jawaban atau lampiran
       yang berubah, supaya badge ringkasan ("N sudah dijawab", "N belum
       dijawab", "N lampiran") ikut bergerak tanpa memuat ulang halaman. */

    /* Satu "item wajib" = satu butir lingkup (.lingkup-item[data-wajib="1"]),
       atau kotak jawaban pertanyaan bila pertanyaannya tanpa butir lingkup. */
    function itemWajib() {
        return $('#topik_daftar .lingkup-item[data-wajib="1"], #topik_daftar .jawaban-kotak[data-wajib="1"]');
    }

    function itemSudahDijawab(el) {
        return $(el).attr('data-sudah-dijawab') === '1';
    }

    function hitungRingkas() {
        var wajib = itemWajib();

        return {
            butir: $('#topik_daftar .lingkup-item[data-wajib="1"]').length,
            total: wajib.length,
            dijawab: wajib.filter(function () { return itemSudahDijawab(this); }).length,
            lampiran: $('#topik_daftar .lampiran[data-lampiran_id]').length
        };
    }

    function ubahAngka(pemilih, teks) {
        var el = $(pemilih).first();
        if (!el.length || el.text().trim() === teks) {
            return;
        }
        el.text(teks).addClass('ringkas-berubah');
        window.setTimeout(function () { el.removeClass('ringkas-berubah'); }, 700);
    }

    function tandai(el, sudah) {
        var kotak = $(el);
        if (kotak.attr('data-sudah-dijawab') === (sudah ? '1' : '0')) {
            return false;
        }
        kotak.attr('data-sudah-dijawab', sudah ? '1' : '0');
        return true;
    }

    function perbaruiRingkas() {
        var angka = hitungRingkas();
        var belum = angka.total - angka.dijawab;

        ubahAngka('#jml_lingkup', angka.butir + ' butir lingkup');
        ubahAngka('#jml_dijawab', angka.dijawab + ' sudah dijawab');
        ubahAngka('#sisa_belum', belum + ' belum dijawab');
        ubahAngka('#jml_lampiran', angka.lampiran + ' lampiran');

        // Badge status pada tiap pertanyaan.
        $('#topik_daftar .topik').each(function () {
            var topik = $(this);
            var badge = topik.find('.topik-dijawab').first();
            var punya = topik.attr('data-punya-lingkup') === '1';

            if (punya) {
                var semua = topik.find('.lingkup-item[data-wajib="1"]');
                var selesai = semua.filter(function () { return itemSudahDijawab(this); }).length;
                tandai(topik, semua.length > 0 && selesai === semua.length);

                if (badge.length) {
                    var teks = selesai + '/' + semua.length + ' dijawab';
                    if (badge.text().trim() !== teks) {
                        badge.text(teks).addClass('ringkas-berubah');
                        window.setTimeout(function () { badge.removeClass('ringkas-berubah'); }, 700);
                    }
                }
            } else {
                var kotak = topik.find('.jawaban-kotak[data-wajib="1"]').first();
                var sudah = kotak.length ? itemSudahDijawab(kotak) : topik.attr('data-sudah-dijawab') === '1';
                tandai(topik, sudah);

                if (badge.length) {
                    var teksTopik = sudah ? 'sudah dijawab' : 'belum dijawab';
                    if (badge.text().trim() !== teksTopik) {
                        badge.text(teksTopik).addClass('ringkas-berubah');
                        window.setTimeout(function () { badge.removeClass('ringkas-berubah'); }, 700);
                    }
                }
            }

            if (badge.length) {
                badge.removeClass('badge-success badge-warning')
                    .addClass(topik.attr('data-sudah-dijawab') === '1' ? 'badge-success' : 'badge-warning');
            }
        });

        // Badge "sudah/belum dijawab" pada tiap butir lingkup.
        $('#topik_daftar .lingkup-item[data-wajib="1"]').each(function () {
            var item = $(this);
            var badge = item.find('.lingkup-status').first();
            if (!badge.length) {
                return;
            }
            var sudah = itemSudahDijawab(item);
            var teks = sudah ? 'sudah dijawab' : 'belum dijawab';
            if (badge.text().trim() !== teks) {
                badge.text(teks).addClass('ringkas-berubah');
                window.setTimeout(function () { badge.removeClass('ringkas-berubah'); }, 700);
            }
            badge.removeClass('badge-success badge-warning')
                .addClass(sudah ? 'badge-success' : 'badge-warning');
        });

        // Peringatan "wajib dijawab" hilang sendiri setelah semuanya terjawab.
        if (belum === 0) {
            $('#peringatan_belum').hide();
        }

        return belum;
    }

    /* ---------------------- simpan jawaban satu butir lingkup ------------------ */

    $('#topik_daftar').on('click', '.jawaban-simpan', function () {
        var tombol = $(this);
        var kotak = tombol.closest('.jawaban-kotak');
        var topik = tombol.closest('.topik');
        var item = tombol.closest('.lingkup-item');
        var lingkup_id = tombol.attr('lingkup_id') || (item.length ? item.data('lingkup_id') : 0);
        var dtform_id = tombol.attr('dtform_id') || topik.data('dtform_id');
        var teks = String(kotak.find('.jawaban-isi').val() || '').trim();

        if (teks === '') {
            pesan('Oops', item.length
                ? 'Jawaban wajib diisi untuk setiap butir lingkup.'
                : 'Jawaban wajib diisi untuk setiap pertanyaan.', 'error');
            kotak.find('.jawaban-isi').focus();
            return;
        }

        $.ajax({
            url: base_url + "/dashboard/jawablingkup",
            type: "POST",
            data: {
                audit_id: auditId(),
                lingkup_id: lingkup_id,
                dtform_id: dtform_id,
                jwb_jawaban: teks
            },
            dataType: "json",
            success: function (hasil) {
                if (!hasil || !hasil.status) {
                    pesan('Oops', hasil && hasil.pesan ? hasil.pesan : 'Jawaban gagal disimpan.', 'error');
                    return;
                }

                kotak.find('.jawaban-pesan').text('tersimpan ' + waktuSekarang());
                if (item.length) {
                    tandai(item, true);
                } else {
                    tandai(kotak, true);
                }
                perbaruiRingkas();
                pesan('Tersimpan', hasil.pesan, 'success');
            },
            error: function () {
                pesan('Oops', 'Tidak dapat menghubungi server.', 'error');
            }
        });
    });

    function waktuSekarang() {
        var d = new Date();
        var dua = function (n) { return (n < 10 ? '0' : '') + n; };
        return dua(d.getHours()) + ':' + dua(d.getMinutes());
    }

    /* ---------------------- unggah lampiran (boleh banyak) ---------------------- */

    $('#topik_daftar').on('click', '.lampiran-unggah-btn', function () {
        var tombol = $(this);
        var kotak = tombol.closest('.lampiran-kotak');
        var topik = tombol.closest('.topik');
        var masukan = kotak.find('.lampiran-berkas');
        var lingkup_id = tombol.attr('lingkup_id') || 0;
        var dtform_id = tombol.attr('dtform_id') || topik.data('dtform_id');

        if (!masukan.length || !masukan[0].files.length) {
            pesan('Oops', 'Pilih berkas yang akan diunggah terlebih dahulu.', 'error');
            return;
        }

        var data = new FormData();
        data.append('audit_id', auditId());
        data.append('lingkup_id', lingkup_id);
        data.append('dtform_id', dtform_id);
        $.each(masukan[0].files, function (i, berkas) {
            data.append('lampiran[]', berkas);
        });

        tombol.prop('disabled', true).text('Mengunggah...');
        muat();

        $.ajax({
            url: base_url + "/dashboard/unggahlampiran",
            type: "POST",
            data: data,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function (hasil) {
                swal.close();
                tombol.prop('disabled', false).html('<i class="icon md-cloud-upload" aria-hidden="true"></i>Unggah');

                if (!hasil || !hasil.status) {
                    pesan('Oops', hasil && hasil.pesan ? hasil.pesan : 'Lampiran gagal diunggah.', 'error');
                    return;
                }

                var daftar = kotak.find('.lampiran-daftar').first();
                $.each(hasil.lampiran, function (i, l) {
                    daftar.append(barisLampiran(l));
                });
                masukan.val('');
                perbaruiRingkas();
                pesan('Tersimpan', hasil.pesan, hasil.gagal && hasil.gagal.length ? 'warning' : 'success');
            },
            error: function () {
                swal.close();
                tombol.prop('disabled', false).html('<i class="icon md-cloud-upload" aria-hidden="true"></i>Unggah');
                pesan('Oops', 'Tidak dapat menghubungi server.', 'error');
            }
        });
    });

    function barisLampiran(l) {
        return $('<div class="lampiran"></div>')
            .attr('data-lampiran_id', l.lampiran_id)
            .append('<i class="icon md-file lampiran-ikon" aria-hidden="true"></i>')
            .append($('<a class="lampiran-nama"></a>').attr('href', l.url).attr('target', '_blank')
                .attr('rel', 'noopener').text(l.nama))
            .append($('<span class="lampiran-ukuran text-muted"></span>').text(l.ukuran))
            .append($('<button type="button" class="btn btn-sm btn-icon btn-danger lampiran-hapus"></button>')
                .attr('id', l.lampiran_id)
                .attr('data-info', 'Hapus lampiran ini')
                .append('<i class="icon md-delete" aria-hidden="true"></i>'));
    }

    /* ------------------------------ hapus lampiran ------------------------------ */

    $('#topik_daftar').on('click', '.lampiran-hapus', function () {
        var tombol = $(this);
        var id = tombol.attr('id');

        swal.fire({
            title: 'Hapus lampiran ini?',
            text: 'Berkas akan dihapus dari server.',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Tidak'
        }).then(function (konfirmasi) {
            if (!konfirmasi.value) {
                return;
            }

            $.ajax({
                url: base_url + "/dashboard/hapuslampiran",
                type: "POST",
                data: { audit_id: auditId(), lampiran_id: id },
                dataType: "json",
                success: function (hasil) {
                    if (!hasil || !hasil.status) {
                        pesan('Oops', hasil && hasil.pesan ? hasil.pesan : 'Lampiran gagal dihapus.', 'error');
                        return;
                    }
                    tombol.closest('.lampiran').remove();
                    perbaruiRingkas();
                    pesan('Terhapus', hasil.pesan, 'success');
                },
                error: function () {
                    pesan('Oops', 'Tidak dapat menghubungi server.', 'error');
                }
            });
        });
    });

    /* --------------------------- kirim hasil evaluasi --------------------------- */

    $('#kirim_hasil').on('click', function () {
        var id = $(this).attr('audit_id');
        var belum = perbaruiRingkas();

        if (belum > 0) {
            pesan('Belum lengkap', 'Masih ada ' + belum + ' butir lingkup yang belum dijawab. '
                + 'Setiap butir lingkup wajib dijawab sebelum hasil dikirim.', 'warning');
            return;
        }

        swal.fire({
            title: "Anda Yakin?",
            text: "Anda Yakin Ingin Mengirim Hasil Evaluasi Ini?",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, Kirim!",
            cancelButtonText: 'Tidak'
        }).then(function (konfirmasi) {
            if (!konfirmasi.value) {
                return;
            }
            muat();
            $.ajax({
                url: base_url + "/dashboard/update",
                type: "POST",
                data: { id: id },
                dataType: "json",
                success: function (jawab) {
                    swal.close();
                    if (!jawab || !jawab.status) {
                        pesan('Belum lengkap', jawab && jawab.pesan ? jawab.pesan : 'Hasil evaluasi belum dapat dikirim.', 'error');
                        return;
                    }
                    swal.fire({
                        title: "Terkirim",
                        text: "Hasil Evaluasi Telah Terkirim!",
                        type: "success"
                    }).then(function () {
                        window.location.reload();
                    });
                },
                error: function () {
                    swal.close();
                    pesan('Oops', 'Tidak dapat menghubungi server.', 'error');
                }
            });
        });
    });

    /* Hitung ulang sekali saat halaman dibuka (berjaga-jaga bila hitungan
       server dan tampilan berbeda, mis. setelah butir lingkup ditambah PPM). */
    perbaruiRingkas();

});
