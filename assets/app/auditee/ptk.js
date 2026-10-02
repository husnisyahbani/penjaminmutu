/* PTK (Permintaan Tindakan Koreksi) - struktur baru.
 *
 * Satu baris = satu butir lingkup yang punya temuan (OB / TS MINOR / TS MAYOR).
 * Rencana koreksi disimpan pada baris auditjawab butir tersebut (lingkup_id)
 * sebagai teks biasa dari textarea (bukan editor kaya).
 */
$(function () {

    var daftarptk = $('#ptk').DataTable({
        "responsive": true,
        /* Lebar kolom diatur CSS (persentase + table-layout fixed pada
           assets/app/ptk.css), bukan dihitung DataTables dari isi sel. */
        "autoWidth": false,
        "processing": true,
        "serverSide": true,
        "searching": true,
        "order": [],
        "columnDefs": [
            /* Kolom No & Rencana Koreksi tidak dapat diurutkan. */
            {"targets": [0, 6], "orderable": false},
            /* Kelas kolom diambil dari header (lihat assets/app/ptk.css) supaya
               lebarnya proporsional dan tidak diatur inline oleh DataTables. */
            {"targets": 0, "className": "ptk-kolom-no"},
            {"targets": 1, "className": "ptk-kolom-formulir"},
            {"targets": 2, "className": "ptk-kolom-butir"},
            {"targets": 3, "className": "ptk-kolom-hasil"},
            {"targets": 4, "className": "ptk-kolom-temuan"},
            {"targets": 5, "className": "ptk-kolom-catatan"},
            {"targets": 6, "className": "ptk-koreksi-sel"}
        ],
        "ajax": {
            "url": base_url + "/ptk/listptk/",
            "type": "POST"
        },
        "createdRow": function (baris) {
            $(baris).find('.ptk-klamp').each(function () {
                var teks = $(this).text();
                if (teks.length > 140) {
                    $(this).attr('title', teks);
                }
            });
        }
    });

    /* Kotak cari pada kepala panel (menggantikan kotak bawaan DataTables,
       lihat aturan .ptk-tabel-kotak .dataTables_filter pada ptk.css). */
    $('#cari_ptk').on('input', function () {
        daftarptk.search(String(this.value || '').trim()).draw();
    });

    var dtjwb_id = 0;
    var audit_id = 0;

    /* Rencana koreksi lama mungkin tersimpan sebagai HTML (versi editor);
       ubah menjadi teks biasa agar nyaman disunting di textarea. */
    function keTeksBiasa(teks) {
        if (!teks) {
            return '';
        }
        teks = String(teks);
        if (teks.indexOf('<') === -1) {
            return teks;
        }

        teks = teks.replace(/<br\s*\/?>/gi, '\n')
                   .replace(/<li[^>]*>/gi, '- ')
                   .replace(/<\/(p|div|li|ul|ol|h[1-6]|tr)>/gi, '\n')
                   .replace(/<[^>]*>/g, '');

        var entitas = {
            '&nbsp;': ' ', '&amp;': '&', '&lt;': '<', '&gt;': '>',
            '&quot;': '"', '&#39;': "'", '&apos;': "'"
        };
        teks = teks.replace(/&[a-z#0-9]+;/gi, function (kode) {
            var kunci = kode.toLowerCase();
            return entitas[kunci] !== undefined ? entitas[kunci] : kode;
        });

        return teks.replace(/[ \t]+\n/g, '\n').replace(/\n{3,}/g, '\n\n').trim();
    }

    $('#ptk').on('click', '.edit', function () {
        var $btn = $(this);
        dtjwb_id = $btn.attr('dtjwb_id');
        audit_id = $btn.attr('audit_id');

        $.ajax({
            url: base_url + "/ptk/getbutir/" + audit_id + "/" + dtjwb_id,
            type: "GET",
            dataType: "json",
            success: function (data) {
                if (!data.status) {
                    swal.fire("Oops", data.pesan || "Data tidak ditemukan", "error");
                    return;
                }

                $('#ptk_dtjwb_id').val(dtjwb_id);
                $('#ptk_audit_id').val(audit_id);
                $('#ptk_butir').html(data.lingkup_isi || '');
                $('#ptk_hasil').html(data.jwb_hasil || '-');
                $('#ptk_catatan').html(data.jwb_catatan || '-');
                $('#ptk_koreksi').val(keTeksBiasa(data.jwb_koreksi));
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
        var koreksi = $('#ptk_koreksi').val() || '';

        $.ajax({
            url: base_url + "/ptk/koreksi",
            type: "POST",
            dataType: "json",
            data: {
                audit_id: audit_id,
                dtjwb_id: $('#ptk_dtjwb_id').val(),
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
