$(function () {

    
    var daftaraudit = $('#daftaraudit').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "searching": true,
        "order": [],
        "columnDefs": [
            {"targets": [0,5,6], "orderable": false}
        ],
        "ajax": {
            "url": base_url + "/daftaraudit/listmutu/",
            "type": "POST"
        }
    });

    var daftarpertanyaan = $('#daftarpertanyaan').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "searching": true,
        "order": [],
        "columnDefs": [
            {"targets": [0,1,2,3,4,5,6], "orderable": false}
        ],
        "ajax": {
            "url": base_url + "/daftaraudit/listpertanyaan",
            "type": "POST"
        }
    });

    var $dtform_id;
    var $audit_id;

    $("#daftarpertanyaan").on("click", ".edit", function () {
        $dtform_id = $(this).attr('dtform_id');
        $audit_id = $(this).attr('audit_id');
        // $('#pertanyaanModal').modal('hide');
        // $("#editModal").modal('show');
        // $('#editModal').on('hidden.bs.modal', function () {
        //     $('#pertanyaanModal').modal('show');
        //     $('body').addClass('modal-open');
        // });

        $('#pertanyaanModal').modal('hide');

        $('#pertanyaanModal').one('hidden.bs.modal', function () {
        $('#editModal').modal('show');
        });

        $('#editModal').one('hidden.bs.modal', function () {
        setTimeout(() => {
            $('#pertanyaanModal').modal('show');
            $('body').addClass('modal-open');
        }, 300);
        });


        var tr = $(this).closest('tr');
        var rowData = daftarpertanyaan.row(tr).data();
        var pertanyaan = rowData[1];
        tinymce.get('pertanyaan').setContent(pertanyaan);

    });

    $("#daftaraudit").on("click", ".detail", function () {
        $("#pertanyaanModal").modal('show');
        var id = $(this).attr('id');
        daftarpertanyaan.ajax.url(base_url + "/daftaraudit/listpertanyaan/"+id).load();
    });

     $("#tambah").on("click", function () {
        $("#addModal").modal('show');
    });

    $("#formadd").formValidation({
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
            url: base_url + "/daftaraudit/tambah",
            type: "POST",
            data: formData,
            processData: false,        // ✅ wajib
            contentType: false,        // ✅ wajib
            beforeSend: function () {
                $("#addModal").modal('hide');
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
                        daftaraudit.ajax.reload();
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
        var $jwb_jawaban = tinymce.get('jwb_jawaban').getContent();

        $.ajax({
            url: base_url + "/daftaraudit/jawab",
            type: "POST",
            data: {
                dtform_id:$dtform_id,
                audit_id:$audit_id,
                jwb_jawaban:$jwb_jawaban
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
                        daftarpertanyaan.ajax.reload();
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