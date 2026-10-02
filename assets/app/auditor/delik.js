/* Daftar tilik (delik).
 *
 * Baris daftar tilik dibaca dari tabel mutu_auditjawabdetail (lewat jwb_id
 * pertanyaan), dan nilai yang diisi auditor disimpan kembali ke tabel yang
 * sama - sehingga langsung tampil pada halaman PTK/delik auditee.
 *
 * Butir dapat ditambah / diubah / dihapus dari halaman ini:
 *   POST /delik/tambahtilik, /delik/pertanyaan, /delik/hapus
 * Simpan nilai: POST /delik/simpantilik {jwb_id, dtjwb_id, kolom, nilai}
 */
$(function () {

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

    /* Butir yang sedang dibuka pada modal. */
    var dtjwb_id = 0;

    /* ---------------- buka modal edit ---------------- */

    function buka(modal, tombol) {
        dtjwb_id = tombol.attr('dtjwb_id');

        $.ajax({
            url: base_url + "/delik/gettilik/" + jwb_id + "/" + dtjwb_id,
            type: "GET",
            dataType: "json",
            success: function (data) {
                if (!data.status) {
                    swal.fire("Oops", data.pesan || "Gagal!", "error");
                    return;
                }
                $('#nilai_lingkup').text(data.lingkup_isi || '');
                $('#edit_dtjwb_hasil').val(data.jwb_hasil);
                $('#edit_dtjwb_temuan').val(data.jwb_temuan);
                $('#edit_dtjwb_catatan').val(data.jwb_catatan);
                $(modal).modal('show');
            },
            error: function () {
                swal.fire("Oops", "No connection!", "error");
            }
        });
    }

    $('#tilik').on('click', '.edithasil', function () {
        buka('#editHasilModal', $(this));
    });

    $('#tilik').on('click', '.edittemuan', function () {
        buka('#editTemuanModal', $(this));
    });

    $('#tilik').on('click', '.editcatatan', function () {
        buka('#editCatatanModal', $(this));
    });

    $('#tilik').on('click', '.editpertanyaan', function () {
        dtjwb_id = $(this).attr('dtjwb_id');

        $.ajax({
            url: base_url + "/delik/gettilik/" + jwb_id + "/" + dtjwb_id,
            type: "GET",
            dataType: "json",
            success: function (data) {
                if (!data.status) {
                    swal.fire("Oops", data.pesan || "Gagal!", "error");
                    return;
                }
                $('#pertanyaan_dtjwb_id').val(data.dtjwb_id);
                $('#edit_dtjwb_referensi').val(data.dtjwb_referensi || '');
                $('#edit_dtjwb_pertanyaan').val(data.dtjwb_pertanyaan || '');
                $('#editPertanyaanModal').modal('show');
            },
            error: function () {
                swal.fire("Oops", "No connection!", "error");
            }
        });
    });

    /* ---------------- tambah butir tilik ---------------- */

    $('#tambahtilik').on('click', function () {
        $('#dtjwb_referensi').val('');
        $('#dtjwb_pertanyaan').val('');
        $('#tambahTilikModal').modal('show');
    });

    function simpanButir(url, formData, modal, $form) {
        $.ajax({
            url: base_url + url,
            type: "POST",
            data: formData,
            dataType: "json",
            beforeSend: function () {
                $(modal).modal('hide');
                swal.fire({
                    title: 'Menyimpan...',
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    onOpen: function () {
                        swal.showLoading();
                    }
                });
            },
            success: function (data) {
                swal.close();
                if (data.status) {
                    tiliklist.ajax.reload();
                    swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.pesan || 'Data telah tersimpan.',
                        showConfirmButton: false,
                        timer: 1200
                    });
                } else {
                    swal.fire("Oops", data.pesan || "Gagal!", "error");
                }
                if ($form) {
                    $form.formValidation('disableSubmitButtons', false).formValidation('resetForm', true);
                }
            },
            error: function () {
                swal.close();
                swal.fire("Oops", "No connection!", "error");
                if ($form) {
                    $form.formValidation('disableSubmitButtons', false).formValidation('resetForm', true);
                }
            }
        });
    }

    $("#formtilik").formValidation({
        framework: "bootstrap4",
        excluded: [':disabled'],
        err: {clazz: 'invalid-feedback'},
        control: {valid: 'is-valid', invalid: 'is-invalid'},
        row: {invalid: 'has-danger'}
    }).on('success.form.fv', function (e) {
        e.preventDefault();
        simpanButir('/delik/tambahtilik', {
            jwb_id: jwb_id,
            dtjwb_referensi: $('#dtjwb_referensi').val(),
            dtjwb_pertanyaan: $('#dtjwb_pertanyaan').val()
        }, '#tambahTilikModal', $(e.target));
        return false;
    });

    $("#formpertanyaan").formValidation({
        framework: "bootstrap4",
        excluded: [':disabled'],
        err: {clazz: 'invalid-feedback'},
        control: {valid: 'is-valid', invalid: 'is-invalid'},
        row: {invalid: 'has-danger'}
    }).on('success.form.fv', function (e) {
        e.preventDefault();
        simpanButir('/delik/pertanyaan', {
            pertanyaan_dtjwb_id: $('#pertanyaan_dtjwb_id').val(),
            edit_dtjwb_referensi: $('#edit_dtjwb_referensi').val(),
            edit_dtjwb_pertanyaan: $('#edit_dtjwb_pertanyaan').val()
        }, '#editPertanyaanModal', $(e.target));
        return false;
    });

    /* ---------------- hapus butir tilik ---------------- */

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
            $.ajax({
                url: base_url + "/delik/hapus",
                type: "POST",
                dataType: "json",
                data: {dtjwb_id: id},
                success: function (data) {
                    if (data.status) {
                        tiliklist.ajax.reload();
                        swal.fire({
                            icon: 'success',
                            title: 'Terhapus!',
                            text: 'Butir tilik telah dihapus.',
                            showConfirmButton: false,
                            timer: 1200
                        });
                    } else {
                        swal.fire("Oops", "Gagal menghapus.", "error");
                    }
                },
                error: function () {
                    swal.fire("Oops", "No connection!", "error");
                }
            });
        });
    });

    /* ---------------- simpan satu kolom ---------------- */

    function simpan(kolom, nilai, modal, $form) {
        $.ajax({
            url: base_url + "/delik/simpantilik",
            type: "POST",
            dataType: "json",
            data: {
                jwb_id: jwb_id,
                dtjwb_id: dtjwb_id,
                kolom: kolom,
                nilai: nilai
            },
            beforeSend: function () {
                $(modal).modal('hide');
                swal.fire({
                    title: 'Menyimpan...',
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    onOpen: function () {
                        swal.showLoading();
                    }
                });
            },
            success: function (data) {
                swal.close();
                if (data.status) {
                    tiliklist.ajax.reload();
                    swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Data telah tersimpan.',
                        showConfirmButton: false,
                        timer: 1200
                    });
                } else {
                    swal.fire("Oops", data.pesan || "Gagal!", "error");
                }
                if ($form) {
                    $form.formValidation('disableSubmitButtons', false).formValidation('resetForm', true);
                }
            },
            error: function () {
                swal.close();
                swal.fire("Oops", "No connection!", "error");
                if ($form) {
                    $form.formValidation('disableSubmitButtons', false).formValidation('resetForm', true);
                }
            }
        });
    }

    function pasangForm(id, kolom, modal, input) {
        $(id).formValidation({
            framework: "bootstrap4",
            excluded: [':disabled'],
            err: {clazz: 'invalid-feedback'},
            control: {valid: 'is-valid', invalid: 'is-invalid'},
            row: {invalid: 'has-danger'}
        }).on('success.form.fv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            simpan(kolom, $(input).val(), modal, $form);
            return false;
        });
    }

    pasangForm('#formhasil', 'jwb_hasil', '#editHasilModal', '#edit_dtjwb_hasil');
    pasangForm('#formtemuan', 'jwb_temuan', '#editTemuanModal', '#edit_dtjwb_temuan');
    pasangForm('#formcatatan', 'jwb_catatan', '#editCatatanModal', '#edit_dtjwb_catatan');

    /* ---------------- tujuan (tingkat pertanyaan) ---------------- */

    $('#edittujuan').on('click', function () {
        $('#tujuanModal').modal('show');
    });

    $("#formtujuan").formValidation({
        framework: "bootstrap4",
        excluded: [':disabled'],
        err: {clazz: 'invalid-feedback'},
        control: {valid: 'is-valid', invalid: 'is-invalid'},
        row: {invalid: 'has-danger'}
    }).on('success.form.fv', function (e) {
        e.preventDefault();

        var $form = $(e.target);
        var formData = new FormData(e.target);

        $.ajax({
            url: base_url + "/delik/tujuan",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                $("#tujuanModal").modal('hide');
                swal.fire({
                    title: 'Menyimpan...',
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    onOpen: function () {
                        swal.showLoading();
                    }
                });
            },
            success: function (data) {
                swal.close();
                var list = data == null ? [] : (data instanceof Array ? data : [data]);
                $.each(list, function (index, res) {
                    if (res.status) {
                        swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Tujuan telah tersimpan.',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(function () {
                            window.location.reload();
                        });
                    } else {
                        swal.fire("Oops", res.pesan, "error");
                    }
                });
                $form.formValidation('disableSubmitButtons', false).formValidation('resetForm', true);
            },
            error: function () {
                swal.close();
                swal.fire("Oops", "No connection!", "error");
                $form.formValidation('disableSubmitButtons', false).formValidation('resetForm', true);
            }
        });

        return false;
    });

    /* ---------------- kembali ---------------- */

    $('#kembali').on('click', function () {
        var id = $(this).attr('audit_id');
        window.location.href = base_url + '/daftaraudit/detail/' + id;
    });
});
