$(function () {
    var daftaraudit = $('#daftaraudit').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "searching": true,
        "order": [],
        "columnDefs": [
            {"targets": [0,5,6], "orderable": false},
            /* Kolom Aksi & Status: rata tengah; lebarnya mengikuti isi
               (th width 1%) supaya tombol tidak pernah pindah baris. */
            {"targets": [5,6], "className": "text-center tabel-aksi-sel"}
        ],
        "ajax": {
            "url": base_url + "/dashboard/listmutu/",
            "type": "POST"
        }
    });

    /* Halaman detail audit kini berbentuk topik/activity yang dirender server
       (lihat assets/app/topik-aktivitas.js), jadi tidak ada DataTable
       #daftarpertanyaan lagi di halaman itu. */

    /* Tombol "Jawaban & Delik" pada tiap topik (halaman detail). */
    $(document).on("click", "#topik_daftar .delik", function () {
        var audit_id = $(this).attr('audit_id');
        var dtform_id = $(this).attr('dtform_id');
         window.location.href = base_url+"/delik?audit_id="+audit_id+"&dtform_id="+dtform_id;
    });

    $("#daftaraudit").on("click", ".detail", function () {
        var id = $(this).attr('id');
        window.location.href = base_url+'/dashboard/detail/'+id;
        // $('#pertanyaanModal').modal('show');
        // var id = $(this).attr('id');
        // daftarpertanyaan.ajax.url(base_url + "/dashboard/listpertanyaan/"+id).load();
    });

    $("#daftaraudit").on("click", ".kirim", function () {
        var id = $(this).attr('id');
        kirim(id);
    });

    function kirim($id)
    {
        swal.fire({
            title: "Anda Yakin?",
            text: "Anda Yakin Ingin Mengirim Hasil Evaluasi Ini?",
            type: "warning",
            showCancelButton: true,
            showLoaderOnConfirm: true,
            confirmButtonText: "Ya, Kirim!",
            cancelButtonText: 'Tidak',
            preConfirm: function () {
                /* Server menolak pengiriman bila masih ada lingkup yang belum
                   dijawab; dalam hal itu tampilkan pesan dan buka halaman
                   detail supaya auditee bisa melengkapi jawabannya. */
                $.ajax({
                    url: base_url + "/dashboard/update",
                    type: "POST",
                    data: { id: $id },
                    dataType: "json"
                })
                        .done(function (jawab) {
                            if (!jawab || !jawab.status) {
                                swal.fire({
                                    title: "Belum lengkap",
                                    text: jawab && jawab.pesan
                                            ? jawab.pesan
                                            : "Hasil evaluasi belum dapat dikirim.",
                                    type: "warning"
                                }).then(function () {
                                    window.location.href = base_url + "/dashboard/detail/" + $id;
                                });
                                return;
                            }
                            swal.fire({
                                title: "Terkirim",
                                text: "Hasil Evaluasi Telah Terkirim!",
                                type: "success",
                                preConfirm: function () {
                                    daftaraudit.ajax.reload();
                                }
                            });
                        })
                        .error(function (data) {
                            swal.fire("Oops", "No connection!", "error");
                        });
            }
        });
    }

    //  $("#tambah").on("click", function () {
    //     $("#addModal").modal('show');
    // });

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
            url: base_url + "/dashboard/tambah",
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

        $.ajax({
            url: base_url + "/dashboard/jawab",
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
                        window.location.reload();
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