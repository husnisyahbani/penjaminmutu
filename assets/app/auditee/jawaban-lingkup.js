/* =============================================================================
   Halaman detail audit auditee: jawaban + lampiran tiap PERTANYAAN.

   - Butir tilik (mutu_auditjawabdetail) hanya ditampilkan sebagai penilaian
     auditor - auditee tidak menjawabnya.
   - Setiap pertanyaan wajib dijawab (tombol Simpan Jawaban).
   - Lampiran opsional dan boleh lebih dari satu berkas per pertanyaan.
   - Tombol "Kirim Hasil Evaluasi" ditolak server selama masih ada pertanyaan
     yang belum dijawab, dan pesannya ditampilkan di sini.
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

    function topikSudahDijawab(topik) {
        return topik.attr('data-sudah-dijawab') === '1';
    }

    function hitungRingkas() {
        var semua = $('#topik_daftar .topik');

        return {
            pertanyaan: semua.length,
            dijawab: semua.filter(function () { return topikSudahDijawab($(this)); }).length,
            lampiran: $('#topik_daftar .lampiran[data-lampiran_id]').length,
            butir: $('#topik_daftar .aktivitas').length
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

    function perbaruiRingkas() {
        var angka = hitungRingkas();
        var belum = angka.pertanyaan - angka.dijawab;

        ubahAngka('#jml_lingkup', angka.butir + ' butir tilik');
        ubahAngka('#jml_dijawab', angka.dijawab + ' sudah dijawab');
        ubahAngka('#sisa_belum', belum + ' belum dijawab');
        ubahAngka('#jml_lampiran', angka.lampiran + ' lampiran');

        // Badge status pada tiap pertanyaan.
        $('#topik_daftar .topik').each(function () {
            var topik = $(this);
            var badge = topik.find('.topik-dijawab').first();
            if (!badge.length) {
                return;
            }

            var sudah = topikSudahDijawab(topik);
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

    /* ---------------------- simpan jawaban satu pertanyaan ---------------------- */

    $('#topik_daftar').on('click', '.jawaban-simpan', function () {
        var tombol = $(this);
        var kotak = tombol.closest('.jawaban-kotak');
        var topik = tombol.closest('.topik');
        var dtform_id = tombol.attr('dtform_id') || topik.data('dtform_id');
        var teks = String(kotak.find('.jawaban-isi').val() || '').trim();

        if (teks === '') {
            pesan('Oops', 'Jawaban wajib diisi untuk setiap pertanyaan.', 'error');
            kotak.find('.jawaban-isi').focus();
            return;
        }

        $.ajax({
            url: base_url + "/dashboard/jawablingkup",
            type: "POST",
            data: { audit_id: auditId(), dtform_id: dtform_id, jwb_jawaban: teks },
            dataType: "json",
            success: function (hasil) {
                if (!hasil || !hasil.status) {
                    pesan('Oops', hasil && hasil.pesan ? hasil.pesan : 'Jawaban gagal disimpan.', 'error');
                    return;
                }

                kotak.find('.jawaban-pesan').text('tersimpan ' + waktuSekarang());
                topik.attr('data-sudah-dijawab', '1');
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
        var dtform_id = tombol.attr('dtform_id') || topik.data('dtform_id');

        if (!masukan.length || !masukan[0].files.length) {
            pesan('Oops', 'Pilih berkas yang akan diunggah terlebih dahulu.', 'error');
            return;
        }

        var data = new FormData();
        data.append('audit_id', auditId());
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

                $.each(hasil.lampiran, function (i, l) {
                    kotak.find('.lampiran-daftar').first().append(barisLampiran(l));
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
            pesan('Belum lengkap', 'Masih ada ' + belum + ' pertanyaan yang belum dijawab. Setiap pertanyaan wajib dijawab sebelum hasil dikirim.', 'warning');
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

});
