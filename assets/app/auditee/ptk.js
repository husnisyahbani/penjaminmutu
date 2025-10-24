$(function () {
    var daftarptk = $('#ptk').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "searching": true,
        "order": [],
        "columnDefs": [
            {"targets": [0,5,6], "orderable": false}
        ],
        "ajax": {
            "url": base_url + "/ptk/listptk/",
            "type": "POST"
        }
    });

    var $dtform_id;
    var $audit_id;

    $("#ptk").on("click", ".edit", function () {
        $('#editModal').modal('show');
        $dtform_id = $(this).attr('dtform_id');
        $audit_id = $(this).attr('audit_id');

        var tr = $(this).closest('tr');
        var rowData = daftarptk.row(tr).data();
        var pertanyaan = rowData[2];
        var koreksi = rowData[3];
        var cleanKoreksi = koreksi.replace(/<button[\s\S]*$/i, '');
        $('#pertanyaan').summernote('code', pertanyaan);
        $('#jwb_koreksi').summernote('code', cleanKoreksi);
    });

    

$("#formedit").formValidation({
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
        //var formData = new FormData(e.target);
        var $jwb_koreksi = $('#jwb_koreksi').summernote('code');

        $.ajax({
            url: base_url + "/ptk/koreksi",
            type: "POST",
            data: {
                dtform_id:$dtform_id,
                audit_id:$audit_id,
                jwb_koreksi:$jwb_koreksi
            },
            beforeSend: function () {
                $("#editModal").modal('hide');
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
                        daftarptk.ajax.reload();
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