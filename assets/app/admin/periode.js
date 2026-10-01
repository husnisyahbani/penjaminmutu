/* Daftar Periode Audit (menu Audit > Periode).
   Tabel server-side + tambah/ubah/set aktif/batalkan aktif/hapus periode. */
$(function () {

    var periodelist = $('#periode').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "searching": true,
        "order": [],
        "columnDefs": [
            {"targets": [0, 4, 5], "orderable": false},
            {"targets": [4, 5], "className": "text-center tabel-aksi-sel"}
        ],
        "ajax": {
            "url": base_url + "/periode/listperiode/",
            "type": "POST"
        }
    });

    /* ---------------- tombol ---------------- */

    $('#tambahperiode').on('click', function () {
        $('#formaddperiode').formValidation('resetForm', true);
        $('#periodeAddModal').modal('show');
    });

    $('#periode').on('click', '.edit', function () {
        getperiode($(this).attr('id'));
    });

    $('#periode').on('click', '.aktifkan', function () {
        aktifkan($(this).attr('id'));
    });

    /* Delegasi di document: tombol "Batalkan Aktif" juga ada di kepala
       halaman (di luar tabel), jadi tidak bisa dipasang pada #periode. */
    $(document).on('click', '.batalkan', function () {
        batalkan($(this).attr('id'));
    });

    $('#periode').on('click', '.delete', function () {
        hapus($(this).attr('id'));
    });

    /* ---------------- tabel pasang ---------------- */

    $('#pasangTabel').on('click', function () {
        var $btn = $(this);
        $btn.prop('disabled', true);

        $.ajax({
            url: base_url + '/periode/pasang',
            type: 'POST',
            dataType: 'json'
        }).done(function (data) {
            if (data.status) {
                swal.fire({title: 'Berhasil', text: data.pesan, type: 'success'})
                    .then(function () { window.location.reload(); });
            } else {
                $btn.prop('disabled', false);
                swal.fire('Gagal', data.pesan, 'error');
            }
        }).fail(function () {
            $btn.prop('disabled', false);
            swal.fire('Oops', 'No connection!', 'error');
        });
    });

    /* ---------------- ambil satu periode ---------------- */

    function getperiode(id) {
        $.ajax({
            url: base_url + '/periode/get_periode/' + id,
            type: 'GET',
            dataType: 'json',
            beforeSend: function () {
                swal.fire({
                    title: 'Loading',
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    didOpen: function () { swal.showLoading(); }
                });
            },
            success: function (data) {
                swal.close();
                if (data.status) {
                    $('#periodeEditModal').modal('show');
                    $('#periode_id').val(data.periode_id);
                    $('#edit_periode_tahun').val(data.periode_tahun);
                    $('#edit_periode_mulai').val(data.periode_mulai);
                    $('#edit_periode_selesai').val(data.periode_selesai);
                } else {
                    swal.fire('Oops', data.pesan || 'Gagal!', 'error');
                }
            },
            error: function () {
                swal.close();
                swal.fire('Oops', 'No connection!', 'error');
            }
        });
    }

    /* ---------------- set aktif ---------------- */

    function aktifkan(id) {
        swal.fire({
            title: 'Jadikan Periode Aktif?',
            text: 'Periode aktif dipakai sebagai filter bawaan pada halaman Daftar Audit.',
            type: 'warning',
            showCancelButton: true,
            showLoaderOnConfirm: true,
            confirmButtonText: 'Ya, Aktifkan!',
            cancelButtonText: 'Tidak',
            preConfirm: function () {
                return $.ajax({
                    url: base_url + '/periode/aktifkan',
                    type: 'POST',
                    dataType: 'json',
                    data: {id: id}
                }).then(function (data) {
                    if (!data.status) {
                        swal.showValidationMessage(data.pesan);
                        return false;
                    }
                    return data;
                });
            }
        }).then(function (hasil) {
            if (hasil.value) {
                /* Halaman dimuat ulang setelah dialog ditutup supaya tombol
                   "Batalkan Aktif" di kepala halaman ikut menyesuaikan. */
                swal.fire({title: 'Berhasil', text: hasil.value.pesan, type: 'success'})
                    .then(muatUlang);
            }
        });
    }

    /* ---------------- batalkan status aktif ---------------- */

    function batalkan(id) {
        swal.fire({
            title: 'Batalkan Status Aktif?',
            text: 'Setelah dibatalkan tidak ada periode aktif, sehingga halaman Daftar Audit menampilkan seluruh data.',
            type: 'warning',
            showCancelButton: true,
            showLoaderOnConfirm: true,
            confirmButtonText: 'Ya, Batalkan!',
            cancelButtonText: 'Tidak',
            preConfirm: function () {
                return $.ajax({
                    url: base_url + '/periode/nonaktifkan',
                    type: 'POST',
                    dataType: 'json',
                    data: {id: id}
                }).then(function (data) {
                    if (!data.status) {
                        swal.showValidationMessage(data.pesan);
                        return false;
                    }
                    return data;
                });
            }
        }).then(function (hasil) {
            if (hasil.value) {
                swal.fire({title: 'Berhasil', text: hasil.value.pesan, type: 'success'})
                    .then(muatUlang);
            }
        });
    }

    /* ---------------- hapus ---------------- */

    function hapus(id) {
        swal.fire({
            title: 'Hapus Periode?',
            text: 'Periode yang masih dipakai audit tidak dapat dihapus.',
            type: 'warning',
            showCancelButton: true,
            showLoaderOnConfirm: true,
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Tidak',
            preConfirm: function () {
                return $.ajax({
                    url: base_url + '/periode/hapus',
                    type: 'POST',
                    dataType: 'json',
                    data: {id: id}
                }).then(function (data) {
                    if (!data.status) {
                        swal.showValidationMessage(data.pesan);
                        return false;
                    }
                    return data;
                });
            }
        }).then(function (hasil) {
            if (hasil.value) {
                swal.fire({title: 'Terhapus', text: hasil.value.pesan, type: 'success'})
                    .then(muatUlang);
            }
        });
    }

    /* Muat ulang tabel dan kepala halaman setelah data berubah. */
    function muatUlang() {
        window.location.reload();
    }

    /* ---------------- formulir tambah ---------------- */

    $("#formaddperiode").formValidation({
        framework: "bootstrap4",
        excluded: [':disabled'],
        err: {clazz: 'invalid-feedback'},
        control: {valid: 'is-valid', invalid: 'is-invalid'},
        row: {invalid: 'has-danger'}
    }).on('success.form.fv', function (e) {
        e.preventDefault();

        var formData = new FormData(e.target);

        $.ajax({
            url: base_url + "/periode/tambah",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                $("#periodeAddModal").modal('hide');
                swal.fire({
                    title: 'Loading',
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    didOpen: function () { swal.showLoading(); }
                });
            },
            success: function (data) {
                swal.close();
                if (data.status) {
                    $('#formaddperiode').formValidation('resetForm', true);
                    periodelist.ajax.reload();
                    swal.fire({title: 'Tersimpan', text: data.pesan, type: 'success'});
                } else {
                    swal.fire("Oops", data.pesan, "error");
                }
            },
            error: function () {
                swal.close();
                swal.fire("Oops", "No connection!", "error");
            }
        });
    });

    /* ---------------- formulir edit ---------------- */

    $("#formeditperiode").formValidation({
        framework: "bootstrap4",
        excluded: [':disabled'],
        err: {clazz: 'invalid-feedback'},
        control: {valid: 'is-valid', invalid: 'is-invalid'},
        row: {invalid: 'has-danger'}
    }).on('success.form.fv', function (e) {
        e.preventDefault();

        var formData = new FormData(e.target);

        $.ajax({
            url: base_url + "/periode/edit",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                $("#periodeEditModal").modal('hide');
                swal.fire({
                    title: 'Loading',
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    didOpen: function () { swal.showLoading(); }
                });
            },
            success: function (data) {
                swal.close();
                if (data.status) {
                    periodelist.ajax.reload();
                    swal.fire({title: 'Tersimpan', text: data.pesan, type: 'success'});
                } else {
                    swal.fire("Oops", data.pesan, "error");
                }
            },
            error: function () {
                swal.close();
                swal.fire("Oops", "No connection!", "error");
            }
        });
    });
});
