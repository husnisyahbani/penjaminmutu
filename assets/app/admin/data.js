$(function () {

    $('#data').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "searching": true,
        "order": [],
        "columnDefs": [
            {"targets": [0,3,5], "orderable": false}
        ],
        "ajax": {
            "url": base_url + "/data/listdata",
            "type": "POST"
        }
    });

    $('#info').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "searching": true,
        "order": [],
        "columnDefs": [
            {"targets": [0,3,5], "orderable": false}
        ],
        "ajax": {
            "url": base_url + "/info/listdata",
            "type": "POST"
        }
    });

    $("#data").on("click", ".edit", function () {
        var id = $(this).attr('id');
        location.href = base_url+ "/data/editdata?id="+id;
    });


    $("#data").on("click", ".delete", function () {
        var id = $(this).attr('id');
        hapusdata(id);
    });

    $("#info").on("click", ".edit", function () {
        var id = $(this).attr('id');
        location.href = base_url+ "/info/editdata?id="+id;
    });


    $("#info").on("click", ".delete", function () {
        var id = $(this).attr('id');
        hapusinfo(id);
    });

    $("#downloadcrekap").on("click", function () {
        location.href = base_url + "/data/downloaddata";
    });

    $("#tambahdata").on("click", function () {
        //window.open(base_url + "/pegawai/tambah");
        location.href = base_url + "/data/tambahdata";
    });

    $("#tambahinfo").on("click", function () {
        //window.open(base_url + "/pegawai/tambah");
        location.href = base_url + "/info/tambahdata";
    });


    function hapusdata($id)
    {
        swal.fire({
            title: "Anda Yakin?",
            text: "Anda Yakin Ingin Data Ini?",
            type: "warning",
            showCancelButton: true,
            showLoaderOnConfirm: true,
            confirmButtonText: "Ya, Hapus!",
            cancelButtonText: 'Tidak',
            preConfirm: function () {
                $.ajax({
                    url: base_url + "/data/hapusdata",
                    type: "POST",
                    data: { id: $id}
                })
                        .done(function (data) {
                            swal.fire({
                                title: "Hapus",
                                text: "Data Telah Terhapus!",
                                type: "success",
                                preConfirm: function () {
                                    location.href = base_url + "/data";
                                }
                            });
                        })
                        .error(function (data) {
                            swal.fire("Oops", "No connection!", "error");
                        });
            }
        });
    }

    function hapusinfo($id)
    {
        swal.fire({
            title: "Anda Yakin?",
            text: "Anda Yakin Ingin Info Ini?",
            type: "warning",
            showCancelButton: true,
            showLoaderOnConfirm: true,
            confirmButtonText: "Ya, Hapus!",
            cancelButtonText: 'Tidak',
            preConfirm: function () {
                $.ajax({
                    url: base_url + "/info/hapusdata",
                    type: "POST",
                    data: { id: $id}
                })
                        .done(function (data) {
                            swal.fire({
                                title: "Hapus",
                                text: "Data Telah Terhapus!",
                                type: "success",
                                preConfirm: function () {
                                    location.href = base_url + "/info";
                                }
                            });
                        })
                        .error(function (data) {
                            swal.fire("Oops", "No connection!", "error");
                        });
            }
        });
    }

   

    $('#formadddata').formValidation({
        framework: "bootstrap4",
        button: {
            selector: '#validateButton',
            disabled: 'disabled'
        },
        icon: null,
        fields: {
            
           
            upload_file: {
                validators: {
                    notEmpty: {
                        message: 'Wajib Diisi'
                    }
                }
            },data_keterangan: {
                validators: {
                    notEmpty: {
                        message: 'Wajib Diisi'
                    }
                }
            },
            data_uraian: {
                validators: {
                    notEmpty: {
                        message: 'Wajib Diisi'
                    }
                }
            }
        },
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
    });


    $('#formeditdata').formValidation({
        framework: "bootstrap4",
        button: {
            selector: '#validateButton',
            disabled: 'disabled'
        },
        icon: null,
        fields: {
            
           
            data_keterangan: {
                validators: {
                    notEmpty: {
                        message: 'Wajib Diisi'
                    }
                }
            },
            data_uraian: {
                validators: {
                    notEmpty: {
                        message: 'Wajib Diisi'
                    }
                }
            }
        },
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
    });

  
})