/* Daftar tilik (halaman admin) - struktur baru.
 *
 * Baris daftar tilik dibaca dari tabel mutu_auditjawabdetail (sama dengan
 * yang diisi auditor dan yang tampil pada halaman PTK/delik auditee). Halaman admin
 * bersifat baca saja untuk daftar tilik; yang dapat diubah hanya Tujuan.
 */
$(function () {

    $('#tilik').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "searching": true,
        "order": [],
        "columnDefs": [
            {"targets": [0], "orderable": false}
        ],
        "ajax": {
            "url": base_url + "/delik/listdelik/" + jwb_id,
            "type": "POST"
        }
    });

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
