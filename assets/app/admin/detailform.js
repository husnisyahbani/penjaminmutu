/* =============================================================================
   Kelola formulir: pertanyaan (topik) + lingkup (butir).

   - Pertanyaan: tambah/edit/hapus/naik-turun. Setelah berubah halaman dimuat ulang
     supaya nomor topik dan ringkasan di panel kiri selalu sinkron.
   - Lingkup: tambah/edit/hapus/naik-turun langsung di tempat (tanpa muat ulang).
   ============================================================================= */
$(function () {

    /* ------------------------- pembantu ------------------------- */

    function pesanGagal(teks) {
        swal.fire("Oops", teks || "Gagal menyimpan data.", "error");
    }

    function kirim(url, data, sukses, gagal) {
        $.ajax({
            url: base_url + url,
            type: "POST",
            data: data,
            dataType: "json",
            success: function (hasil) {
                if (hasil && hasil.status) {
                    sukses(hasil);
                } else {
                    (gagal || pesanGagal)(hasil ? hasil.pesan : null);
                }
            },
            error: function () {
                (gagal || pesanGagal)("Tidak dapat menghubungi server.");
            }
        });
    }

    function segarkanAngka(topik) {
        if (!topik) {
            return;
        }
        var jumlah = topik.find('.aktivitas').not('.aktivitas-tambah-baris').length;
        topik.find('.badge-activity').text(jumlah + ' activity');
        topik.find('.aktivitas-kosong').toggle(jumlah === 0);
        if (window.TopikAktivitas) {
            window.TopikAktivitas.aturTombolActivity(topik);
        }
    }

    function bangunBarisActivity(lingkup_id, isi) {
        var baris = $(
            '<div class="aktivitas">' +
                '<i class="icon md-assignment aktivitas-ikon" aria-hidden="true"></i>' +
                '<div class="aktivitas-isi">' +
                    '<span class="aktivitas-teks"></span>' +
                    '<div class="aktivitas-editor" style="display:none;">' +
                        '<textarea class="form-control" rows="2" placeholder="Tulis activity, mis. dokumen/bukti yang diminta"></textarea>' +
                        '<div class="aktivitas-editor-aksi">' +
                            '<button type="button" class="btn btn-sm btn-primary aktivitas-simpan">Simpan</button>' +
                            '<button type="button" class="btn btn-sm btn-default aktivitas-batal">Batal</button>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
                '<div class="aktivitas-aksi">' +
                    '<button type="button" class="btn btn-sm btn-icon btn-default aktivitas-naik" data-info="Naikkan activity">' +
                        '<i class="icon md-chevron-up" aria-hidden="true"></i></button>' +
                    '<button type="button" class="btn btn-sm btn-icon btn-default aktivitas-turun" data-info="Turunkan activity">' +
                        '<i class="icon md-chevron-down" aria-hidden="true"></i></button>' +
                    '<button type="button" class="btn btn-sm btn-icon btn-success aktivitas-edit" data-info="Ubah isi activity">' +
                        '<i class="icon md-edit" aria-hidden="true"></i></button>' +
                    '<button type="button" class="btn btn-sm btn-icon btn-danger aktivitas-hapus" data-info="Hapus activity ini">' +
                        '<i class="icon md-delete" aria-hidden="true"></i></button>' +
                '</div>' +
            '</div>'
        );
        baris.attr('data-lingkup_id', lingkup_id);
        baris.attr('data-cari', (isi || '').toLowerCase());
        baris.find('.aktivitas-teks').text(isi);
        baris.find('.aktivitas-editor textarea').val(isi);
        return baris;
    }

    /* ------------------------- topik ------------------------- */

    $('#tambah_topik').on('click', function () {
        $('#formaddtopik')[0].reset();
        $('#topikAddModal').modal('show');
    });

    $("#formaddtopik").formValidation({
        framework: "bootstrap4",
        excluded: [':disabled'],
        err: { clazz: 'invalid-feedback' },
        control: { valid: 'is-valid', invalid: 'is-invalid' },
        row: { invalid: 'has-danger' }
    }).on('success.form.fv', function (e) {
        e.preventDefault();
        var $form = $(e.target);
        var formData = new FormData(e.target);

        $.ajax({
            url: base_url + "/detailform/tambah",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                $("#topikAddModal").modal('hide');
                swal.fire({
                    title: 'Loading',
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    didOpen: function () { swal.showLoading(); }
                });
            },
            success: function (data) {
                swal.close();
                if (data && data.status) {
                    window.location.reload();
                    return;
                }
                pesanGagal(data ? data.pesan : null);
                $form.formValidation('disableSubmitButtons', false).formValidation('resetForm', true);
            },
            error: function () {
                swal.close();
                pesanGagal("Tidak dapat menghubungi server.");
                $form.formValidation('disableSubmitButtons', false).formValidation('resetForm', true);
            }
        });
        return false;
    });

    $('#topik_daftar').on('click', '.topik-edit', function () {
        var id = $(this).attr('id');
        $.ajax({
            url: base_url + "/detailform/getdtformById/" + id,
            type: "GET",
            dataType: "json",
            beforeSend: function () {
                swal.fire({
                    title: 'Loading',
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    didOpen: function () { swal.showLoading(); }
                });
            },
            success: function (list) {
                swal.close();
                if (list && list.status) {
                    $('#dtform_id').val(list.dtform_id);
                    $('#edit_dtform_pertanyaan').val(list.dtform_pertanyaan);
                    $('#formbedittopik').formValidation('resetForm', true);
                    $('#topikEditModal').modal('show');
                } else {
                    pesanGagal();
                }
            },
            error: function () {
                swal.close();
                pesanGagal("Tidak dapat menghubungi server.");
            }
        });
    });

    $("#formbedittopik").formValidation({
        framework: "bootstrap4",
        excluded: [':disabled'],
        err: { clazz: 'invalid-feedback' },
        control: { valid: 'is-valid', invalid: 'is-invalid' },
        row: { invalid: 'has-danger' }
    }).on('success.form.fv', function (e) {
        e.preventDefault();
        var $form = $(e.target);
        var formData = new FormData(e.target);

        $.ajax({
            url: base_url + "/detailform/edit",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                $("#topikEditModal").modal('hide');
                swal.fire({
                    title: 'Loading',
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    didOpen: function () { swal.showLoading(); }
                });
            },
            success: function (data) {
                swal.close();
                if (data && data.status) {
                    window.location.reload();
                    return;
                }
                pesanGagal(data ? data.pesan : null);
                $form.formValidation('disableSubmitButtons', false).formValidation('resetForm', true);
            },
            error: function () {
                swal.close();
                pesanGagal("Tidak dapat menghubungi server.");
                $form.formValidation('disableSubmitButtons', false).formValidation('resetForm', true);
            }
        });
        return false;
    });

    $('#topik_daftar').on('click', '.topik-hapus', function () {
        var id = $(this).attr('id');
        swal.fire({
            title: "Hapus topik ini?",
            text: "Seluruh activity pada topik ini ikut terhapus.",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, hapus",
            cancelButtonText: "Batal"
        }).then(function (hasil) {
            if (!hasil.value) {
                return;
            }
            kirim("/detailform/hapus", { id: id }, function () {
                window.location.reload();
            });
        });
    });

    $('#topik_daftar').on('click', '.topik-naik, .topik-turun', function () {
        var topik = $(this).closest('.topik');
        var arah = $(this).hasClass('topik-naik') ? 'naik' : 'turun';
        kirim("/detailform/pindah", { dtform_id: topik.attr('data-dtform_id'), arah: arah }, function () {
            window.location.reload();
        });
    });

    /* ------------------------- activity ------------------------- */

    $('#topik_daftar').on('click', '.topik-tambah-activity', function () {
        var topik = $(this).closest('.topik');
        topik.find('.aktivitas-tambah-baris').show().find('textarea').val('').focus();
        $(this).hide();
    });

    $('#topik_daftar').on('click', '.aktivitas-batal', function () {
        var baris = $(this).closest('.aktivitas');
        if (baris.hasClass('aktivitas-tambah-baris')) {
            baris.hide();
            baris.closest('.topik').find('.topik-tambah-activity').show();
            return;
        }
        baris.find('.aktivitas-editor').hide();
        baris.find('.aktivitas-teks').show();
    });

    $('#topik_daftar').on('click', '.aktivitas-simpan', function () {
        var baris = $(this).closest('.aktivitas');
        var topik = baris.closest('.topik');
        var isi = String(baris.find('textarea').val() || '').trim();

        if (isi === '') {
            pesanGagal("Isi activity tidak boleh kosong.");
            return;
        }

        kirim("/detailform/simpanbutir", {
            dtform_id: topik.attr('data-dtform_id'),
            lingkup_id: baris.attr('data-lingkup_id') || 0,
            lingkup_isi: isi
        }, function (hasil) {
            if (baris.hasClass('aktivitas-tambah-baris')) {
                var baru = bangunBarisActivity(hasil.lingkup_id, hasil.lingkup_isi);
                baris.before(baru);
                baris.hide().find('textarea').val('');
                topik.find('.topik-tambah-activity').show();
                $('#topik_kosong').hide();
            } else {
                baris.attr('data-lingkup_id', hasil.lingkup_id);
                baris.attr('data-cari', (hasil.lingkup_isi || '').toLowerCase());
                baris.find('.aktivitas-teks').text(hasil.lingkup_isi);
                baris.find('.aktivitas-editor').hide();
                baris.find('.aktivitas-teks').show();
            }
            segarkanAngka(topik);
        });
    });

    $('#topik_daftar').on('click', '.aktivitas-edit', function () {
        var baris = $(this).closest('.aktivitas');
        baris.find('.aktivitas-teks').hide();
        baris.find('.aktivitas-editor').show();
        baris.find('.aktivitas-editor textarea').focus();
    });

    $('#topik_daftar').on('click', '.aktivitas-hapus', function () {
        var baris = $(this).closest('.aktivitas');
        var topik = baris.closest('.topik');

        swal.fire({
            title: "Hapus activity ini?",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, hapus",
            cancelButtonText: "Batal"
        }).then(function (hasil) {
            if (!hasil.value) {
                return;
            }
            kirim("/detailform/hapusbutir", { lingkup_id: baris.attr('data-lingkup_id') }, function () {
                baris.remove();
                segarkanAngka(topik);
            });
        });
    });

    $('#topik_daftar').on('click', '.aktivitas-naik, .aktivitas-turun', function () {
        var baris = $(this).closest('.aktivitas');
        var topik = baris.closest('.topik');
        var arah = $(this).hasClass('aktivitas-naik') ? 'naik' : 'turun';

        kirim("/detailform/pindahbutir", { lingkup_id: baris.attr('data-lingkup_id'), arah: arah }, function () {
            if (arah === 'naik') {
                baris.prevAll('.aktivitas').not('.aktivitas-tambah-baris').first().before(baris);
            } else {
                baris.nextAll('.aktivitas').not('.aktivitas-tambah-baris').first().after(baris);
            }
            segarkanAngka(topik);
        });
    });

    /* ------------------------- urutan topik ------------------------- */

    $('#pasang_urut').on('click', function () {
        var tombol = $(this);
        tombol.prop('disabled', true).text('Menyiapkan...');
        kirim("/detailform/pasangurut", {}, function () {
            window.location.reload();
        }, function (pesan) {
            tombol.prop('disabled', false).text('Aktifkan Urutan Pertanyaan');
            pesanGagal(pesan);
        });
    });

});
