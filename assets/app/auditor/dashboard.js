/*  To change this license header, choose License Headers in Project Properties.
 *  To change this template file, choose Tools | Templates and open the template
 *  in the editor.

 */
$(function () {
    // $.fn.dataTable.ext.errMode = 'throw';
    var pengajuan = $('#pengajuan').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "searching": true,
        "order": [],
        "columnDefs": [
            {"targets": [0,4,5], "orderable": false}
        ],
        "ajax": {
            "url": base_url + "/dashboard/listpengajuan",
            "type": "POST"
        }
    });

    $("#tambah").on("click", function () {
        $('#auditorModal').modal('show');

    });

    $("#formpengajuan").formValidation({
        framework: "bootstrap4",
        excluded: [':disabled'],
        err: {
            clazz: 'invalid-feedback'
        },
        control: {
            // The CSS class for valid control
            valid: 'is-valid',
            // The CSS class for invalid control
            invalid: 'is-invalid'
        },
        row: {
            invalid: 'has-danger'
        }
    })
    .on('success.form.fv', function(e) {
        e.preventDefault();

        var $form     = $(e.target);
        var $jp_id = $("#jp_id").val();
        var $submitajuan = $("#submitajuan").val();

        $.ajax({
            url: base_url + "/dashboard/tambah",
            type: "POST",
            data:{
                jp_id:$jp_id,
                mhs_id:$mhs_id,
                submitajuan:$submitajuan
            },
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
            success: function (data) {
                swal.close();
                var list = data == null ? [] : (data instanceof Array ? data : [data]);
                    $.each(list, function (index, org_types) {
                        if(org_types.status){
                            pengajuan.ajax.reload();
                        }else{
                            swal.fire("Oops", org_types.pesan, "error");
                        }
                    });
                    $("#ajuanModal").modal('hide');
                    $form.formValidation('disableSubmitButtons', false).formValidation('resetForm', true);
            },
            error: function (json) {
                swal.fire("Oops", "No connection!", "error");
                $("#ajuanModal").modal('hide');
                $form.formValidation('disableSubmitButtons', false).formValidation('resetForm', true);
            }
        });

        return false;
    });

    //transaksilist.ajax.url(base_url + "/pembayaran/listtransaksi/"+id).load();
    var syaratpengajuan = $('#syaratpengajuan').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "searching": true,
        "order": [],
        "columnDefs": [
            {"targets": [0,1,2,3], "orderable": false}
        ],
        "ajax": {
            "url": base_url + "/dashboard/listsyarat",
            "type": "POST"
        }
    });

    $("#pengajuan").on("click", ".detail", function () {
        var id = $(this).attr('id');
        syaratpengajuan.ajax.url(base_url + "/dashboard/listsyarat/"+id).load();
    });

    $("#pengajuan").on("click", ".ajukan", function () {
        var id = $(this).attr('id');
        ajukan(id);
    });

    $("#pengajuan").on("click", ".revisi", function () {
        location.href = base_url + "/tindaklanjut/";
    });

    $("#pengajuan").on("click", ".selesai", function () {
        var id = $(this).attr('id');
        
        $("#showpdfModal").modal('show');
        $('.modal-body #myframe').attr('src', base_url+"/dashboard/download?jp_no="+id);
    });

    $("#syaratpengajuan").on("click", ".format", function () {
        var $id = $(this).attr('id');

        $.ajax({
            url: base_url + "/dashboard/getdokument",
            type: "POST",
            data:{
                dok_id:$id
            },
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
            success: function (data) {
                swal.close();
                var list = data == null ? [] : (data instanceof Array ? data : [data]);
                    $.each(list, function (index, org_types) {
                        if(org_types.status){
                            window.open(org_types.dok_format);
                        }else{
                            swal.fire("Oops", "Gagal!", "error");
                        }
                    });
                    
            },
            error: function (json) {
                swal.fire("Oops", "No connection!", "error");
                
            }
        });

    });

    $("#syaratpengajuan").on("click", ".file", function () {
        var $id = $(this).attr('id');

        $.ajax({
            url: base_url + "/dashboard/getsyarat/",
            type: "POST",
            data:{
                id:$id
            },
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
            success: function (data) {
                swal.close();
                var list = data == null ? [] : (data instanceof Array ? data : [data]);
                    $.each(list, function (index, org_types) {
                        if(org_types.status){
                            window.open(org_types.da_link);
                            $('#syaratLabel').text(org_types.dok_nama);
                            //$('.modal-body #myframe').attr('src',org_types.da_link+"&amp;usp=sharing" );
                            //$("#showpdfModal").modal('show');
                        }else{
                            swal.fire("Oops", "Gagal!", "error");
                        }
                    });
                    
            },
            error: function (json) {
                swal.fire("Oops", "No connection!", "error");
                
            }
        });

        
    });

    $("#syaratpengajuan").on("click", ".upload", function () {
        var $id = $(this).attr('id');

        $.ajax({
            url: base_url + "/dashboard/getsyarat/",
            type: "POST",
            data:{
                id:$id
            },
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
            success: function (data) {
                swal.close();
                var list = data == null ? [] : (data instanceof Array ? data : [data]);
                    $.each(list, function (index, org_types) {
                        if(org_types.status){
                            
                            if(org_types.dok_jenis == "LINK"){
                                $('#da_id').val(org_types.da_id);
                                $('#da_link').val(org_types.da_link);
                                $('#syaratModal').modal('show');
                                $('#syaratLabel').text(org_types.dok_nama);
                            }else if(org_types.dok_jenis == "TEXT"){
                                $('#da_id_text').val(org_types.da_id);
                                $('#da_link_text').val(org_types.da_link);
                                $('#syaratTextModal').modal('show');
                                $('#syaratTextLabel').text(org_types.dok_nama);
                            }else{
                                $('#syaratFileModal').modal('show');
                                $('#syaratfileLabel').text(org_types.dok_nama);
                                $('#fada_id').val(org_types.da_id);
                                var filename = org_types.da_link.split('/').pop();
                                $('#upload_file').attr("data-default-file",filename);
                            }
                            
                        }else{
                            swal.fire("Oops", "Gagal!", "error");
                        }
                    });
                    
            },
            error: function (json) {
                swal.fire("Oops", "No connection!", "error");
                
            }
        });
    });


    $("#formsyaratpengajuan").formValidation({
        framework: "bootstrap4",
        excluded: [':disabled'],
        err: {
            clazz: 'invalid-feedback'
        },
        control: {
            // The CSS class for valid control
            valid: 'is-valid',
            // The CSS class for invalid control
            invalid: 'is-invalid'
        },
        row: {
            invalid: 'has-danger'
        }
    })
    .on('success.form.fv', function(e) {
        e.preventDefault();

        var $form     = $(e.target);
        var $da_id = $("#da_id").val();
        var $da_link = $("#da_link").val();
        var $submitajuan = $("#submitajuan").val();

        $.ajax({
            url: base_url + "/dashboard/tambahsyarat",
            type: "POST",
            data:{
                da_id:$da_id,
                da_link:$da_link,
                submitajuan:$submitajuan
            },
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
            success: function (data) {
                swal.close();
                var list = data == null ? [] : (data instanceof Array ? data : [data]);
                    $.each(list, function (index, org_types) {
                        if(org_types.status){
                            syaratpengajuan.ajax.reload();
                        }else{
                            swal.fire("Oops", "Gagal!", "error");
                        }
                    });
                    $("#syaratModal").modal('hide');
                    $form.formValidation('disableSubmitButtons', false).formValidation('resetForm', true);
            },
            error: function (json) {
                swal.fire("Oops", "No connection!", "error");
                $("#syaratModal").modal('hide');
                $form.formValidation('disableSubmitButtons', false).formValidation('resetForm', true);
            }
        });

        return false;
    });

    $("#formsyarattextpengajuan").formValidation({
        framework: "bootstrap4",
        excluded: [':disabled'],
        err: {
            clazz: 'invalid-feedback'
        },
        control: {
            // The CSS class for valid control
            valid: 'is-valid',
            // The CSS class for invalid control
            invalid: 'is-invalid'
        },
        row: {
            invalid: 'has-danger'
        }
    })
    .on('success.form.fv', function(e) {
        e.preventDefault();

        var $form     = $(e.target);
        var $da_id = $("#da_id_text").val();
        var $da_link = $("#da_link_text").val();
        var $submitajuan = $("#submitajuan").val();

        $.ajax({
            url: base_url + "/dashboard/tambahsyarat",
            type: "POST",
            data:{
                da_id:$da_id,
                da_link:$da_link,
                submitajuan:$submitajuan
            },
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
            success: function (data) {
                swal.close();
                var list = data == null ? [] : (data instanceof Array ? data : [data]);
                    $.each(list, function (index, org_types) {
                        if(org_types.status){
                            syaratpengajuan.ajax.reload();
                        }else{
                            swal.fire("Oops", "Gagal!", "error");
                        }
                    });
                    $("#syaratTextModal").modal('hide');
                    $form.formValidation('disableSubmitButtons', false).formValidation('resetForm', true);
            },
            error: function (json) {
                swal.fire("Oops", "No connection!", "error");
                $("#syaratTextModal").modal('hide');
                $form.formValidation('disableSubmitButtons', false).formValidation('resetForm', true);
            }
        });

        return false;
    });



    $("#formsyaratfilepengajuan").formValidation({
        framework: "bootstrap4",
        excluded: [':disabled'],
        err: {
            clazz: 'invalid-feedback'
        },
        control: {
            // The CSS class for valid control
            valid: 'is-valid',
            // The CSS class for invalid control
            invalid: 'is-invalid'
        },
        row: {
            invalid: 'has-danger'
        }
    })
    .end();


    function ajukan($id)
    {
        $.ajax({
            url: base_url + "/dashboard/checksyarat",
            type: "POST",
            data:{
                aj_id:$id
            },
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
            success: function (data) {
                swal.close();
                var list = data == null ? [] : (data instanceof Array ? data : [data]);
                    $.each(list, function (index, org_types) {
                        if(org_types.status){
                            //syaratpengajuan.ajax.reload();

                            swal.fire({
                                title: "Anda Yakin?",
                                text: "Anda Yakin Ingin Mengajukan Ini?",
                                type: "warning",
                                showCancelButton: true,
                                showLoaderOnConfirm: true,
                                confirmButtonText: "Ya, Ajukan!",
                                cancelButtonText: 'Tidak',
                                preConfirm: function () {
                                    $.ajax({
                                        url: base_url + "/dashboard/ajukan",
                                        type: "POST",
                                        data: { id: $id}
                                    })
                                            .done(function (data) {
												
												var list = data == null ? [] : (data instanceof Array ? data : [data]);
                                                $.each(list, function (index, org_types) {
                                                    if(org_types.status){
                                                        swal.close();
                                                        pengajuan.ajax.reload();
                                                        syaratpengajuan.ajax.url(base_url + "/dashboard/listsyarat/").load();
                                                        toastr.options.timeOut = '1000';
                                                        toastr.options.closeButton = true;
                                                        toastr.options.positionClass = 'toast-top-right';
                                                        toastr['success']('Berhasil Diajukan!');
                                                    }else{
                                                        swal.fire("Oops", org_types.pesan, "error");
                                                    }
                                                });
												
                                                
                                            })
                                            .error(function (data) {
                                                swal.fire("Oops", "No connection!", "error");
                                            });
                                }
                            });
                        }else{
                            swal.fire("Oops", "Lengkapi Dokument Syarat", "error");
                        }
                    });
                    
            },
            error: function (json) {
                swal.fire("Oops", "No connection!", "error");
                
            }
        });

        
    }


});
