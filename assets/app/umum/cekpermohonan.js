/*  To change this license header, choose License Headers in Project Properties.
 *  To change this template file, choose Tools | Templates and open the template
 *  in the editor.

 */
$(function () {

    $('#formcekpermohonan').formValidation({
        framework: "bootstrap4",
        button: {
            selector: '#validateButton',
            disabled: 'disabled'
        },
        icon: null,
        fields: {
            no_test: {
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
