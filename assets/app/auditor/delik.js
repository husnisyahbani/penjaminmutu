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
        excluded: [':disabled'],
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
    }).on('success.form.fv', function(e) {
        e.preventDefault();

        var $form = $(e.target);       // ✅ perbaikan
        var formData = new FormData(e.target);

        $.ajax({
            url: base_url + "/delik/tujuan",
            type: "POST",
            data: formData,
            processData: false,        // ✅ wajib
            contentType: false,        // ✅ wajib
            beforeSend: function () {
                $("#tujuanModal").modal('hide');
                swal.fire({
                    title: 'Loading',
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    onOpen: () => {
                        swal.showLoading();
                    }
                });
            },
            success: function (data) {
                swal.close();
                var list = data == null ? [] : (data instanceof Array ? data : [data]);
                $.each(list, function (index, org_types) {
                    if (org_types.status) {

                        var newTujuan = $('#jwb_tujuan').summernote('code');
                        // Set kembali ke elemen <p id="tujuan">
                        $("#tujuan").html(newTujuan);
                        
                        Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Data telah tersimpan.',
                        showConfirmButton: false,
                        timer: 1000
                    });
                    } else {
                        swal.fire("Oops", org_types.pesan, "error");
                    }
                });
                $form.formValidation('disableSubmitButtons', false)
                    .formValidation('resetForm', true);
            },
            error: function () {
                swal.fire("Oops", "No connection!", "error");
                $form.formValidation('disableSubmitButtons', false)
                    .formValidation('resetForm', true);
            }
        });

    return false;
});

});