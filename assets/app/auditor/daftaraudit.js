$(function () {
    $("#edittujuan").on("click", function () {
    // Ambil isi dari elemen <p id="tujuan">
    var isiTujuan = $("#tujuan").html();

    // Tampilkan modal
    $('#tujuanModal').modal('show');

    // Setelah modal tampil, masukkan isi ke Summernote
    $('#tujuanModal').on('shown.bs.modal', function () {
        $('#jwb_tujuan').summernote('code', isiTujuan);
    });
});


    $("#editreferensi").on("click", function () {
       $('#referensiModal').modal('show');
    });

    $("#editpertanyaan").on("click", function () {
       $('#pertanyaanModal').modal('show');
    });


    $("#edithasil").on("click", function () {
       $('#hasilModal').modal('show');
    });

    $("#edittemuan").on("click", function () {
       $('#temuanModal').modal('show');
    });

    $("#editcatatan").on("click", function () {
       $('#catatanModal').modal('show');
    });


   $("#formtujuan").formValidation({
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

    // ðŸ”§ Ambil konten dari Summernote, lalu tambahkan ke formData
    var isiTujuan = $('#jwb_tujuan').summernote('code');
    formData.set('jwb_tujuan', isiTujuan);

    $.ajax({
        url: base_url + "/delik/tujuan",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function () {
            $("#tujuanModal").modal('hide');
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
                    // ðŸ”§ Update isi <p id="tujuan"> dari summernote
                    $("#tujuan").html(isiTujuan);

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