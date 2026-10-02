/* Daftar tilik auditor.
 *
 * Susunannya mengikuti halaman auditee/delik: kartu ringkasan di atas, tabel
 * butir di bawah - tanpa tab.
 *
 * - Tabel: server-side DataTables (POST /delik/listdelik/<jwb_id>).
 *   Kolom Butir Lingkup memuat pertanyaan + referensi (tebal) dan ikon ubah;
 *   kolom Hasil / Temuan / Catatan masing-masing punya ikon ubah; kolom Aksi
 *   hanya berisi tombol hapus.
 * - Tambah butir  : POST /delik/tambahtilik  {jwb_id, dtjwb_pertanyaan, dtjwb_referensi}
 * - Ubah butir    : POST /delik/pertanyaan   {pertanyaan_dtjwb_id, edit_dtjwb_*}
 * - Ubah isian    : POST /delik/simpanbutir  {jwb_id, dtjwb_id, kolom, nilai}
 * - Hapus butir   : POST /delik/hapus        {dtjwb_id}
 * - Tujuan        : POST /delik/tujuan       {audit_id, dtform_id, jwb_tujuan}
 */
$(function () {

    function pesan(judul, teks, tipe) {
        swal.fire(judul, teks, tipe || 'info');
    }

    function muat() {
        swal.fire({
            title: 'Menyimpan...',
            allowEscapeKey: false,
            allowOutsideClick: false,
            onOpen: function () { swal.showLoading(); }
        });
    }

    /* ------------------------------ tabel ------------------------------ */

    var tiliklist = $('#tilik').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "searching": true,
        "order": [],
        "columnDefs": [
            {"targets": [0, 5], "orderable": false},
            {"targets": [5], "className": "text-center tabel-aksi-sel"}
        ],
        "ajax": {
            "url": base_url + "/delik/listdelik/" + jwb_id,
            "type": "POST"
        }
    });

    function segarkanKartu() {
        $.ajax({
            url: base_url + "/delik/ringkasan/" + jwb_id,
            type: "GET",
            dataType: "json",
            success: function (data) {
                if (!data || !data.status) {
                    return;
                }
                $.each(data.kartu, function (i, k) {
                    $('.kartu-stat').eq(i).find('.kartu-stat__angka').text(k.nilai);
                    var bar = $('.kartu-stat').eq(i).find('.kartu-stat__bar-isi');
                    if (bar.length) {
                        bar.css('width', k.bar + '%');
                        $('.kartu-stat').eq(i).find('.kartu-stat__bar-ket').text(k.bar + '% dari butir dinilai');
                    }
                });
                $('#jml_butir').text(data.total + ' butir tilik');
            }
        });
    }

    function simpan(url, data, modal, $form) {
        $.ajax({
            url: base_url + url,
            type: "POST",
            data: data,
            dataType: "json",
            beforeSend: function () {
                $(modal).modal('hide');
                muat();
            },
            success: function (hasil) {
                swal.close();
                if (hasil && hasil.status) {
                    tiliklist.ajax.reload(null, false);
                    segarkanKartu();
                    swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: hasil.pesan || 'Data telah tersimpan.',
                        showConfirmButton: false,
                        timer: 1200
                    });
                } else {
                    pesan('Oops', (hasil && hasil.pesan) || 'Gagal menyimpan.', 'error');
                }
                if ($form) {
                    $form.formValidation('disableSubmitButtons', false).formValidation('resetForm', true);
                }
            },
            error: function () {
                swal.close();
                pesan('Oops', 'Tidak dapat menghubungi server.', 'error');
                if ($form) {
                    $form.formValidation('disableSubmitButtons', false).formValidation('resetForm', true);
                }
            }
        });
    }

    function pasangValidasi(pemilih) {
        return $(pemilih).formValidation({
            framework: "bootstrap4",
            excluded: [':disabled'],
            err: {clazz: 'invalid-feedback'},
            control: {valid: 'is-valid', invalid: 'is-invalid'},
            row: {invalid: 'has-danger'}
        });
    }

    /* --------------------- tambah butir tilik --------------------- */

    $('#tambahtilik').on('click', function () {
        $('#dtjwb_pertanyaan').val('');
        $('#dtjwb_referensi').val('');
        $('#tambahTilikModal').modal('show');
    });

    pasangValidasi('#formtilik').on('success.form.fv', function (e) {
        e.preventDefault();
        simpan('/delik/tambahtilik', {
            jwb_id: jwb_id,
            dtjwb_pertanyaan: $('#dtjwb_pertanyaan').val(),
            dtjwb_referensi: $('#dtjwb_referensi').val()
        }, '#tambahTilikModal', $(e.target));
        return false;
    });

    /* ------------- ubah butir (pertanyaan + referensi) ------------- */

    $('#tilik').on('click', '.editbutir', function () {
        var tombol = $(this);
        $('#pertanyaan_dtjwb_id').val(tombol.attr('dtjwb_id'));
        $('#edit_dtjwb_pertanyaan').val(tombol.attr('data-pertanyaan'));
        $('#edit_dtjwb_referensi').val(tombol.attr('data-referensi'));
        $('#editButirModal').modal('show');
    });

    pasangValidasi('#formbutir').on('success.form.fv', function (e) {
        e.preventDefault();
        simpan('/delik/pertanyaan', {
            pertanyaan_dtjwb_id: $('#pertanyaan_dtjwb_id').val(),
            edit_dtjwb_pertanyaan: $('#edit_dtjwb_pertanyaan').val(),
            edit_dtjwb_referensi: $('#edit_dtjwb_referensi').val()
        }, '#editButirModal', $(e.target));
        return false;
    });

    /* ------- ubah satu isian (hasil / temuan / catatan) ------- */

    var judulIsi = {
        dtjwb_hasil: 'Ubah Hasil',
        dtjwb_temuan: 'Ubah Temuan',
        dtjwb_catatan: 'Ubah Catatan'
    };

    $('#tilik').on('click', '.editisi', function () {
        var tombol = $(this);
        var kolom = tombol.attr('data-kolom');
        var nilai = tombol.attr('data-nilai') || '';

        $('#edit_isi_dtjwb_id').val(tombol.attr('dtjwb_id'));
        $('#edit_isi_kolom').val(kolom);
        $('#editIsiJudul').text(judulIsi[kolom] || 'Ubah Isian');

        if (kolom === 'dtjwb_temuan') {
            $('#editIsiTeks').hide();
            $('#editIsiTemuan').show();
            $('#edit_isi_temuan').val(nilai);
        } else {
            $('#editIsiTemuan').hide();
            $('#editIsiTeks').show();
            $('#edit_isi_nilai').val(nilai);
        }

        $('#editIsiModal').modal('show');
    });

    $('#formisi').on('submit', function (e) {
        e.preventDefault();
        var kolom = $('#edit_isi_kolom').val();
        var nilai = (kolom === 'dtjwb_temuan')
            ? $('#edit_isi_temuan').val()
            : $('#edit_isi_nilai').val();

        simpan('/delik/simpanbutir', {
            jwb_id: jwb_id,
            dtjwb_id: $('#edit_isi_dtjwb_id').val(),
            kolom: kolom,
            nilai: nilai
        }, '#editIsiModal', null);
        return false;
    });

    /* ------------------------- hapus butir ------------------------- */

    $('#tilik').on('click', '.hapustilik', function () {
        var id = $(this).attr('dtjwb_id');

        swal.fire({
            title: 'Hapus butir tilik ini?',
            text: 'Butir beserta hasil, temuan, dan catatannya akan dihapus.',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Tidak'
        }).then(function (hasil) {
            if (!hasil.value) {
                return;
            }
            simpan('/delik/hapus', {dtjwb_id: id}, null, null);
        });
    });

    /* --------------------------- tujuan --------------------------- */

    $('#tujuan_edit').on('click', function () {
        $('#tujuan_tampil').hide();
        $('#tujuan_ubah').show();
        $('#tujuan_isi').focus();
    });

    $('#tujuan_batal').on('click', function () {
        $('#tujuan_ubah').hide();
        $('#tujuan_tampil').show();
    });

    $('#tujuan_simpan').on('click', function () {
        var tombol = $(this);
        var teks = String($('#tujuan_isi').val() || '').trim();

        $.ajax({
            url: base_url + "/delik/tujuan",
            type: "POST",
            dataType: "json",
            data: {
                audit_id: tombol.attr('audit_id'),
                dtform_id: tombol.attr('dtform_id'),
                jwb_tujuan: teks
            },
            beforeSend: function () { muat(); },
            success: function (hasil) {
                swal.close();
                if (!hasil || !hasil.status) {
                    pesan('Oops', (hasil && hasil.pesan) || 'Gagal menyimpan tujuan.', 'error');
                    return;
                }
                $('#tujuan_teks').html(teks === ''
                    ? '<span class="text-muted">Belum ada tujuan untuk pertanyaan ini.</span>'
                    : $('<div/>').text(teks).html().replace(/\n/g, '<br>'));
                $('#tujuan_ubah').hide();
                $('#tujuan_tampil').show();
                swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Tujuan telah tersimpan.',
                    showConfirmButton: false,
                    timer: 1200
                });
            },
            error: function () {
                swal.close();
                pesan('Oops', 'Tidak dapat menghubungi server.', 'error');
            }
        });
    });

    /* --------------------------- kembali --------------------------- */

    $('#kembali').on('click', function () {
        var id = $(this).attr('audit_id');
        window.location.href = base_url + '/daftaraudit/detail/' + id;
    });
});
