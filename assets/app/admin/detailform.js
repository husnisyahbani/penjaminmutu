$(function () {

    /* ---------------- butir lingkup (boleh banyak per pertanyaan) ---------------- */

    var templat = $('#lingkup_templat').html();

    function tambahBaris(target, id, isi) {
        var $baris = $(templat);
        $baris.find('input[name="lingkup_id[]"]').val(id || 0);
        $baris.find('textarea[name="lingkup_isi[]"]').val(isi || '');
        $('#' + target).append($baris);
    }

    function kosongkan(target) {
        $('#' + target).empty();
    }

    /* Satu baris kosong siap diisi saat modal dibuka. */
    function siapkanFormTambah() {
        kosongkan('lingkup_add_daftar');
        tambahBaris('lingkup_add_daftar', 0, '');
    }

    $('.lingkup-tambah').on('click', function () {
        tambahBaris($(this).data('target'), 0, '');
    });

    $(document).on('click', '.lingkup-hapus', function () {
        $(this).closest('.lingkup-baris').remove();
    });

    /* Buang baris yang benar-benar kosong sebelum dikirim. */
    function rapikan(target) {
        $('#' + target).find('.lingkup-baris').each(function () {
            if ($.trim($(this).find('textarea').val()) === '' && $(this).find('input').val() === '0') {
                $(this).remove();
            }
        });
    }

    var dtform = $('#dtform').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "searching": true,
        "order": [],
        "columnDefs": [
            {"targets": [0,3], "orderable": false}
        ],
        "ajax": {
            "url": base_url + "/detailform/listdtform/"+form_id,
            "type": "POST"
        }
    });

   

    $("#dtform").on("click", ".edit", function () {
        var id = $(this).attr('id');

        $.ajax({
            url: base_url + "/detailform/getdtformById/"+id,
            type: "GET",
            dataType: "json",
            beforeSend: function () {
                swal.fire({
                    title: 'Loading',
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    onOpen: () => {
                        swal.showLoading();
                    }
                });
            },
            success: function (list) {
                swal.close();
                if (list.status) {
                    $('#dtformEditModal').modal('show');
                    $("#edit_dtform_tujuan").val(list.dtform_tujuan);
                    $("#dtform_id").val(list.dtform_id);
                    $('#edit_dtform_pertanyaan').summernote('code',list.dtform_pertanyaan);

                    /* Butir lingkup: satu baris per butir (boleh ditambah/dihapus). */
                    kosongkan('lingkup_edit_daftar');
                    if (list.lingkup && list.lingkup.length) {
                        $.each(list.lingkup, function (i, b) {
                            tambahBaris('lingkup_edit_daftar', b.lingkup_id, b.lingkup_isi);
                        });
                    } else {
                        tambahBaris('lingkup_edit_daftar', 0, '');
                    }
                    // tinymce.get('edit_dtform_pertanyaan').setContent(list.dtform_pertanyaan);
                    // tinymce.get('edit_dtform_lingkup').setContent(list.dtform_lingkup);
                    
                } else {
                    swal.fire("Oops", "Gagal!", "error");
                        $("#formeditdtform")
                    .formValidation('disableSubmitButtons', false)
                    .formValidation('resetForm', true);
                }   
            },
            error: function () {
                swal.fire("Oops", "No connection!", "error");
                
                 $("#formeditdtform")
                .formValidation('disableSubmitButtons', false)
                .formValidation('resetForm', true);
            }
        });
    });


    $("#dtform").on("click", ".delete", function () {
        var id = $(this).attr('id');
        hapus(id);
    });

    $("#dtform").on("click", ".detail", function () {
        var id = $(this).attr('id');
        location.href = base_url + "/detailform?id="+id;
    });

    function hapus($id)
    {
        swal.fire({
            title: "Anda Yakin?",
            text: "Anda Yakin Ingin Hapus dtform Ini?",
            type: "warning",
            showCancelButton: true,
            showLoaderOnConfirm: true,
            confirmButtonText: "Ya, Hapus!",
            cancelButtonText: 'Tidak',
            preConfirm: function () {
                $.ajax({
                    url: base_url + "/detailform/hapus",
                    type: "POST",
                    data: { id: $id}
                })
                        .done(function (data) {
                            swal.fire({
                                title: "Hapus",
                                text: "dtform Telah Terhapus!",
                                type: "success",
                                preConfirm: function () {
                                    dtform.ajax.reload();
                                }
                            });
                        })
                        .error(function (data) {
                            swal.fire("Oops", "No connection!", "error");
                        });
            }
        });
    }

    $("#tambah").on("click", function () {
        siapkanFormTambah();
        $("#dtformAddModal").modal('show');
    });


    $("#formadddtform").formValidation({
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
        rapikan('lingkup_add_daftar');
        var formData = new FormData(e.target);

        $.ajax({
            url: base_url + "/detailform/tambah",
            type: "POST",
            data: formData,
            processData: false,        // ✅ wajib
            contentType: false,        // ✅ wajib
            beforeSend: function () {
                $("#dtformAddModal").modal('hide');
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
                        dtform.ajax.reload();
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


$("#formeditdtform").formValidation({
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
        rapikan('lingkup_edit_daftar');
        var formData = new FormData(e.target);

        $.ajax({
            url: base_url + "/detailform/edit",
            type: "POST",
            data: formData,
            processData: false,        // ✅ wajib
            contentType: false,        // ✅ wajib
            beforeSend: function () {
                $("#dtformEditModal").modal('hide');
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
                        dtform.ajax.reload();
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


  
})