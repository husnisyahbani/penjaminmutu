/* PTK (Permintaan Tindakan Koreksi) - struktur baru.
 *
 * Satu baris = satu butir lingkup yang punya temuan (OB / TS MINOR / TS MAYOR).
 * Rencana koreksi disimpan pada baris auditjawab butir tersebut (lingkup_id).
 */
$(function () {

    var daftarptk = $('#ptk').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "searching": true,
        "order": [],
        "columnDefs": [
            {"targets": [0, 6], "orderable": false}
        ],
        "ajax": {
            "url": base_url + "/ptk/listptk/",
            "type": "POST"
        }
    });

    var lingkup_id = 0;
    var dtform_id = 0;
    var audit_id = 0;

    function isiEditor(teks) {
        var $ed = $('#ptk_koreksi');
        try {
            $ed.summernote('code', teks || '');
        } catch (e) {
            $ed.val(teks || '');
        }
    }

    $('#ptk').on('click', '.edit', function () {
        var $btn = $(this);
        lingkup_id = $btn.attr('lingkup_id');
        dtform_id = $btn.attr('dtform_id');
        audit_id = $btn.attr('audit_id');

        $.ajax({
            url: base_url + "/ptk/getbutir/" + $btn.attr('audit_id') + "/" + lingkup_id,
            type: "GET",
            dataType: "json",
            success: function (data) {
                if (!data.status) {
                    swal.fire("Oops", data.pesan || "Data tidak ditemukan", "error");
                    return;
                }

                $('#ptk_lingkup_id').val(lingkup_id);
                $('#ptk_dtform_id').val(dtform_id);
                $('#ptk_butir').html(data.lingkup_isi || '');
                $('#ptk_hasil').html(data.jwb_hasil || '-');
                $('#ptk_catatan').html(data.jwb_catatan || '-');
                isiEditor(data.jwb_koreksi || '');
                $('#editModal').modal('show');
            },
            error: function () {
                swal.fire("Oops", "No connection!", "error");
            }
        });
    });

    $("#formedit").formValidation({
        framework: "bootstrap4",
        excluded: [':disabled', ':hidden'],
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
    }).on('success.form.fv', function (e) {
        e.preventDefault();

        var $form = $(e.target);
        var koreksi = '';
        try {
            koreksi = $('#ptk_koreksi').summernote('code');
        } catch (err) {
            koreksi = $('#ptk_koreksi').val();
        }

        $.ajax({
            url: base_url + "/ptk/koreksi",
            type: "POST",
            dataType: "json",
            data: {
                audit_id: audit_id,
                dtform_id: $('#ptk_dtform_id').val(),
                lingkup_id: $('#ptk_lingkup_id').val(),
                ptk_koreksi: koreksi
            },
            beforeSend: function () {
                $('#editModal').modal('hide');
                swal.fire({
                    title: 'Menyimpan...',
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    onOpen: function () {
                        swal.showLoading();
                    }
                });
            },
            success: function (data) {
                swal.close();
                if (data.status) {
                    daftarptk.ajax.reload();
                    swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Rencana koreksi telah tersimpan.',
                        showConfirmButton: false,
                        timer: 1400
                    });
                } else {
                    swal.fire("Oops", data.pesan || "Gagal menyimpan", "error");
                }
                $form.formValidation('disableSubmitButtons', false).formValidation('resetForm', true);
            },
            error: function () {
                swal.close();
                swal.fire("Oops", "No connection!", "error");
                $form.formValidation('disableSubmitButtons', false).formValidation('resetForm', true);
            }
        });

        return false;
    });
});
