$(function () {

    $("#editjawaban").on("click", function () {
    var isiJawaban = $("#jawaban").html();

    // Tampilkan modal
    $('#editModal').modal('show');

    // Setelah modal tampil, masukkan isi ke Summernote
    $('#tujuanModal').on('shown.bs.modal', function () {
        $('#jwb_jawaban').summernote('code', isiJawaban);
    });
});


$("#kembali").on("click", function () {
    var id = $(this).attr('audit_id');
    window.location.href = base_url+'/dashboard/detail/'+id;
});




   $("#formjawaban").formValidation({
    framework: "bootstrap4",
    excluded: [':disabled', ':hidden', ':not(:visible)'], // ðŸ”§ tambahkan agar summernote tidak dianggap kosong
    err: {
        clazz: 'invalid-feedback'
    },
    control: {
        valid: 'is-valid',
        invalid: 'is-invalid'
    },
    row: {
        invalid: 'has-danger'
    }
}).on('success.form.fv', function (e) {
    e.preventDefault();

    var $form = $(e.target);
    var formData = new FormData(e.target);

    var isiJawaban = $('#jwb_jawaban').summernote('code');
    formData.set('jwb_jawaban', isiJawaban);

    $.ajax({
        url: base_url + "/delik/jawaban",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function () {
            $("#editModal").modal('hide');
            Swal.fire({
                title: 'Loading...',
                allowEscapeKey: false,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        },
        success: function (data) {
            Swal.close();

            var list = data == null ? [] : (data instanceof Array ? data : [data]);
            $.each(list, function (index, res) {
                if (res.status) {
                    $("#jawaban").html(isiJawaban);

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Data telah tersimpan.',
                        showConfirmButton: false,
                        timer: 1200
                    });
                } else {
                    Swal.fire("Oops", res.pesan, "error");
                }
            });

            $form.formValidation('disableSubmitButtons', false)
                .formValidation('resetForm', true);
        },
        error: function () {
            Swal.fire("Oops", "No connection!", "error");
            $form.formValidation('disableSubmitButtons', false)
                .formValidation('resetForm', true);
        }
    });

    return false;
});





});