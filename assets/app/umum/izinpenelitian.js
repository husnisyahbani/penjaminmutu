/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */   



$(function () {
    $("#ip_tglsurat").datepicker({ 
        format: 'dd-mm-yyyy'
    });

    $("#ip_mulai").datepicker({ 
        format: 'dd-mm-yyyy'
    });

    $("#ip_selesai").datepicker({ 
        format: 'dd-mm-yyyy'
    });

    $('#formkonsultasi').formValidation({
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
            },
            ip_nama: {
                validators: {
                    notEmpty: {
                        message: 'Wajib Diisi'
                    }
                }
            },
            ip_email: {
                validators: {
                    notEmpty: {
                        message: 'Wajib Diisi'
                    },
                    email: {
                        message: 'Format Salah'
                    }
                }
            },
            ip_asalpt: {
                validators: {
                    notEmpty: {
                        message: 'Wajib Diisi'
                    }
                }
            },
            ip_prodi: {
                validators: {
                    notEmpty: {
                        message: 'Wajib Diisi'
                    }
                }
            },
            ip_tglsurat: {
                validators: {
                    notEmpty: {
                        message: 'Wajib Diisi'
                    }
                }
            },
            ip_nosurat: {
                validators: {
                    notEmpty: {
                        message: 'Wajib Diisi'
                    }
                }
            },
            ip_judul: {
                validators: {
                    notEmpty: {
                        message: 'Wajib Diisi'
                    }
                }
            },
            ip_lokasi: {
                validators: {
                    notEmpty: {
                        message: 'Wajib Diisi'
                    }
                }
            },
            ip_mulai: {
                validators: {
                    notEmpty: {
                        message: 'Wajib Diisi'
                    }
                }
            },
            ip_selesai: {
                validators: {
                    notEmpty: {
                        message: 'Wajib Diisi'
                    }
                }
            },
            captcha: {
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
    
    
    