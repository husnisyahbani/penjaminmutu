/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$(function () {
    // $.fn.dataTable.ext.errMode = 'throw';
    $('#akun').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "searching": true,
        "order": [],
        "columnDefs": [
            {"targets": [0,6], "orderable": false}
        ],
        "ajax": {
            "url": base_url + "/akun/listakun",
            "type": "POST"
        }
    });
    
    $("#tambahakun").on("click", function () {
        location.href = base_url + "/akun/tambah";
        //window.open(base_url + "/akun/tambah");
    });

    $("#akun").on("click", ".delete", function () {
        var id = $(this).attr('id');
        hapus(id);
    });
    
    $("#akun").on("click", ".reset", function () {
        var id = $(this).attr('id');
        reset(id);
    });

    $("#akun").on("click", ".edit", function () {
        var id = $(this).attr('id');
        //window.open(base_url+ "/akun/edit?id="+id);
        location.href = base_url+ "/akun/edit?id="+id;
    });
    
    

    function hapus($id)
    {
        swal.fire({
            title: "Anda Yakin?",
            text: "Anda Yakin Ingin Menghapus Akun Ini?",
            type: "warning",
            showCancelButton: true,
            showLoaderOnConfirm: true,
            confirmButtonText: "Ya, Hapus!",
            cancelButtonText: 'Tidak',
            preConfirm: function () {
                $.ajax({
                    url: base_url + "/akun/hapus",
                    type: "POST",
                    data: { id: $id}
                })
                        .done(function (data) {
                            swal.fire({
                                title: "Hapus",
                                text: "Data Akun Telah Terhapus!",
                                type: "success",
                                preConfirm: function () {
                                    location.href = base_url + "/akun";
                                }
                            });
                        })
                        .error(function (data) {
                            swal.fire("Oops", "No connection!", "error");
                        });
            }
        });
    }
    
    
    function reset($id)
    {
        swal.fire({
            title: "Anda Yakin?",
            text: "Anda Yakin Ingin Mereset Password Akun Ini?",
            type: "warning",
            showCancelButton: true,
            showLoaderOnConfirm: true,
            confirmButtonText: "Ya, Reset!",
            cancelButtonText: 'Tidak',
            preConfirm: function () {
                $.ajax({
                    url: base_url + "/akun/reset",
                    type: "POST",
                    data: { id: $id}
                })
                        .done(function (data) {
                            swal.fire({
                                title: "Hapus",
                                text: "Password Telah Direset!",
                                type: "success",
                                preConfirm: function () {
                                    location.href = base_url + "/akun";
                                }
                            });
                        })
                        .error(function (data) {
                            swal.fire("Oops", "No connection!", "error");
                        });
            }
        });
    }
    
    $('#formaddakun').formValidation({
        framework: "bootstrap4",
        button: {
            selector: '#validateButton',
            disabled: 'disabled'
        },
        icon: null,
        fields: {
            
           username: {
                validators: {
                    notEmpty: {
                        message: 'Wajib Diisi'
                    }
                }
            },
            password: {
                validators: {
                    notEmpty: {
                        message: 'Wajib Diisi'
                    }
                }
            },
            role: {
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
    
    
    $('#formeditakun').formValidation({
        framework: "bootstrap4",
        button: {
            selector: '#validateButton',
            disabled: 'disabled'
        },
        icon: null,
        fields: {
            
           username: {
                validators: {
                    notEmpty: {
                        message: 'Wajib Diisi'
                    }
                }
            },
            password: {
                validators: {
                    notEmpty: {
                        message: 'Wajib Diisi'
                    }
                }
            },
            role: {
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
    
    
    



});


