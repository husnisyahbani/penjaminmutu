/* =============================================================================
   Halaman detail audit auditee: jawaban + lampiran tiap lingkup.

   - Setiap lingkup wajib dijawab (tombol Simpan Jawaban).
   - Lampiran opsional dan boleh lebih dari satu berkas per lingkup.
   - Tombol "Kirim Hasil Evaluasi" ditolak server selama masih ada lingkup
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

    function perbaruiSisa() {
        var belum = $('#topik_daftar .status-jawab').filter(function () {
            return /wajib/i.test($(this).text());
        }).length;
        $('#sisa_belum').text(belum + ' belum dijawab');
        return belum;
    }

    /* ---------------------- simpan jawaban satu lingkup ---------------------- */

    $('#topik_daftar').on('click', '.jawaban-simpan', function () {
        var baris = $(this).closest('.aktivitas');
        var lingkup_id = baris.attr('data-lingkup_id');
        var audit_id = $('#kirim_hasil').attr('audit_id') || $('.delik').first().attr('audit_id');
        var teks = String(baris.find('.jawaban-isi').val() || '').trim();

        if (teks === '') {
            pesan('Oops', 'Jawaban wajib diisi untuk setiap lingkup.', 'error');
            baris.find('.jawaban-isi').focus();
            return;
        }

        $.ajax({
            url: base_url + "/dashboard/jawablingkup",
            type: "POST",
            data: { audit_id: audit_id, lingkup_id: lingkup_id, jwb_jawaban: teks },
            dataType: "json",
            success: function (hasil) {
                if (!hasil || !hasil.status) {
                    pesan('Oops', hasil && hasil.pesan ? hasil.pesan : 'Jawaban gagal disimpan.', 'error');
                    return;
                }

                baris.find('.jawaban-pesan').text('tersimpan ' + waktuSekarang());
                baris.find('.status-jawab').removeClass('badge-danger').addClass('badge-success').text('sudah dijawab');
                perbaruiSisa();
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
        var kotak = $(this).closest('.lampiran-kotak');
        var baris = $(this).closest('.aktivitas');
        var masukan = kotak.find('.lampiran-berkas');
        var audit_id = $('#kirim_hasil').attr('audit_id') || $('.delik').first().attr('audit_id');

        if (!masukan.length || !masukan[0].files.length) {
            pesan('Oops', 'Pilih berkas yang akan diunggah terlebih dahulu.', 'error');
            return;
        }

        var data = new FormData();
        data.append('audit_id', audit_id);
        data.append('lingkup_id', baris.attr('data-lingkup_id'));
        $.each(masukan[0].files, function (i, berkas) {
            data.append('lampiran[]', berkas);
        });

        var tombol = $(this);
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
                    kotak.find('.lampiran-daftar').append(barisLampiran(l));
                });
                masukan.val('');
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

    /* ---------------------- hapus lampiran ---------------------- */

    $('#topik_daftar').on('click', '.lampiran-hapus', function () {
        var baris = $(this).closest('.lampiran');
        var audit_id = $('#kirim_hasil').attr('audit_id') || $('.delik').first().attr('audit_id');

        swal.fire({
            title: 'Hapus lampiran ini?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal'
        }).then(function (hasil) {
            if (!hasil.value) {
                return;
            }
            $.ajax({
                url: base_url + "/dashboard/hapuslampiran",
                type: "POST",
                data: { audit_id: audit_id, lampiran_id: baris.attr('data-lampiran_id') },
                dataType: "json",
                success: function (jawab) {
                    if (!jawab || !jawab.status) {
                        pesan('Oops', jawab && jawab.pesan ? jawab.pesan : 'Lampiran gagal dihapus.', 'error');
                        return;
                    }
                    baris.remove();
                },
                error: function () {
                    pesan('Oops', 'Tidak dapat menghubungi server.', 'error');
                }
            });
        });
    });

    /* ---------------------- kirim hasil evaluasi ---------------------- */

    $('#kirim_hasil').on('click', function () {
        var id = $(this).attr('audit_id');
        var belum = perbaruiSisa();

        if (belum > 0) {
            pesan('Belum lengkap', 'Masih ada ' + belum + ' lingkup yang belum dijawab. Setiap lingkup wajib dijawab sebelum hasil dikirim.', 'warning');
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
