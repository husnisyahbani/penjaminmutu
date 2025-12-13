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

    let selectedIDs = [];

    $('#daftaraudit').on('click', '.selesai', function () {
        let id = $(this).attr('id');
        let select = [id];

        $.ajax({
        url: base_url + "/daftaraudit/download",
        type: "POST",
        data: { ids: select },
        xhrFields: {
            responseType: 'blob'
        },
        beforeSend: function () {
            swal.fire({
                title: 'Loading...',
                allowEscapeKey: false,
                allowOutsideClick: false,
                onOpen: () => {
                    swal.showLoading();
                }
            });
        },
        success: function (data, status, xhr) {
            swal.close();

            // ============================
            // Ambil nama file dari header
            // ============================
            const cd = xhr.getResponseHeader('Content-Disposition');
            let filename = 'download.pdf';

            if (cd && cd.indexOf('filename=') !== -1) {
                const regex = /filename="?([^"]+)"?/;
                const matches = regex.exec(cd);
                if (matches && matches.length > 1) {
                    filename = matches[1];
                }
            }

            // ============================
            // Blob untuk download
            // ============================
            const contentType = xhr.getResponseHeader('Content-Type') || 'application/pdf';
            const blob = new Blob([data], { type: contentType });
            const url = URL.createObjectURL(blob);

            // ============================
            // Trigger download
            // ============================
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            a.remove();

            URL.revokeObjectURL(url);
        },
        error: function () {
            swal.fire("Oops", "No connection!", "error");
        }
    });
        
    });

    $('#daftaraudit').on('click', '.pilih', function () {
        let id = $(this).val();

        if ($(this).is(':checked')) {
            if (!selectedIDs.includes(id)) selectedIDs.push(id);
        } else {
            selectedIDs = selectedIDs.filter(item => item != id);
        }
        

        console.log(selectedIDs);
    });

    daftaraudit.on('draw', function () {
        $('.pilih').each(function () {
            let id = $(this).val();
            if (selectedIDs.includes(id)) {
                $(this).prop('checked', true);
            }
        });
    });

    $('#download').on('click', function() {

    if (selectedIDs.length === 0) {
        swal.fire("Oops", "Mohon Pilih Data Yang Ingin Didownload!", "error");
        return;
    }

    $.ajax({
        url: base_url + "/daftaraudit/download",
        type: "POST",
        data: { ids: selectedIDs },
        xhrFields: {
            responseType: 'blob'
        },
        beforeSend: function () {
            swal.fire({
                title: 'Loading...',
                allowEscapeKey: false,
                allowOutsideClick: false,
                onOpen: () => {
                    swal.showLoading();
                }
            });
        },
        success: function (data, status, xhr) {
            swal.close();

            // ============================
            // Ambil nama file dari header
            // ============================
            const cd = xhr.getResponseHeader('Content-Disposition');
            let filename = 'download.pdf';

            if (cd && cd.indexOf('filename=') !== -1) {
                const regex = /filename="?([^"]+)"?/;
                const matches = regex.exec(cd);
                if (matches && matches.length > 1) {
                    filename = matches[1];
                }
            }

            // ============================
            // Blob untuk download
            // ============================
            const contentType = xhr.getResponseHeader('Content-Type') || 'application/pdf';
            const blob = new Blob([data], { type: contentType });
            const url = URL.createObjectURL(blob);

            // ============================
            // Trigger download
            // ============================
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            a.remove();

            URL.revokeObjectURL(url);
        },
        error: function () {
            swal.fire("Oops", "No connection!", "error");
        }
    });

});


    var daftarpertanyaan = $('#daftarpertanyaan').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "searching": true,
        "order": [],
        "columnDefs": [
            {"targets": [0,1,2], "orderable": false}
        ],
        "ajax": {
            "url": base_url + "/daftaraudit/listpertanyaan/"+audit_id,
            "type": "POST"
        }
    });

    $("#daftarpertanyaan").on("click", ".delik", function () {
        var audit_id = $(this).attr('audit_id');
        var dtform_id = $(this).attr('dtform_id');
         window.location.href = base_url+"/delik?audit_id="+audit_id+"&dtform_id="+dtform_id;
    });

    $("#daftaraudit").on("click", ".detail", function () {
         var id = $(this).attr('id');
         window.location.href = base_url+'/daftaraudit/detail/'+id;
    });

    $("#daftaraudit").on("click", ".delete", function () {
        var id = $(this).attr('id');
        hapusdata(id);
    });

    

    function kembali($id)
    {
        swal.fire({
            title: "Anda Yakin?",
            text: "Anda Yakin Ingin Mengembalikan Evaluasi Ini?",
            type: "warning",
            showCancelButton: true,
            showLoaderOnConfirm: true,
            confirmButtonText: "Ya, Kembalikan!",
            cancelButtonText: 'Tidak',
            preConfirm: function () {
                $.ajax({
                    url: base_url + "/daftaraudit/kembali",
                    type: "POST",
                    data: { id: $id}
                })
                        .done(function (data) {
                            if(data.status){
                                    swal.fire({
                                        title: "Hapus",
                                        text: "Evaluasi Telah Dikembalikan!",
                                        type: "success",
                                        preConfirm: function () {
                                            daftaraudit.ajax.reload();
                                        }
                                    });
                            }else{
                                swal.fire({
                                        title: "Gagal",
                                        text: "Evaluasi Tidak dapat dikembalikan!",
                                        type: "danger",
                                        preConfirm: function () {
                                            daftaraudit.ajax.reload();
                                        }
                                    });
                            }
                            
                        })
                        .error(function (data) {
                            swal.fire("Oops", "No connection!", "error");
                        });
            }
        });
    }

    function hapusdata($id)
    {
        swal.fire({
            title: "Anda Yakin?",
            text: "Anda Yakin Ingin Menghapus Ajuan Ini?",
            type: "warning",
            showCancelButton: true,
            showLoaderOnConfirm: true,
            confirmButtonText: "Ya, Hapus!",
            cancelButtonText: 'Tidak',
            preConfirm: function () {
                $.ajax({
                    url: base_url + "/daftaraudit/hapus",
                    type: "POST",
                    data: { id: $id}
                })
                        .done(function (data) {
                            if(data.status){
                                    swal.fire({
                                        title: "Hapus",
                                        text: "Ajuan Telah Terhapus!",
                                        type: "success",
                                        preConfirm: function () {
                                            daftaraudit.ajax.reload();
                                        }
                                    });
                            }else{
                                swal.fire({
                                        title: "Gagal",
                                        text: "Ajuan Tidak dapat dihapus!",
                                        type: "danger",
                                        preConfirm: function () {
                                            daftaraudit.ajax.reload();
                                        }
                                    });
                            }
                            
                        })
                        .error(function (data) {
                            swal.fire("Oops", "No connection!", "error");
                        });
            }
        });
    }

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
        var $jwb_jawaban = $('#jwb_jawaban').summernote('code');
        //var $jwb_jawaban = tinymce.get('jwb_jawaban').getContent();

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