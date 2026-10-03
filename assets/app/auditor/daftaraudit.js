$(function () {
    var daftaraudit = $('#daftaraudit').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "searching": true,
        "order": [],
        "columnDefs": [
            {"targets": [0,5,6], "orderable": false},
            /* Kolom Aksi & Status: rata tengah. Lebarnya mengikuti isi
               (th width 1%) supaya tombol tidak pernah pindah baris. */
            {"targets": [5,6], "className": "text-center tabel-aksi-sel"}
        ],
        "ajax": {
            "url": base_url + "/daftaraudit/listmutu/",
            "type": "POST"
        }
    });

    /* Halaman detail audit kini berbentuk topik/activity yang dirender server
       (lihat assets/app/topik-aktivitas.js), jadi tidak ada DataTable
       #daftarpertanyaan lagi di halaman itu. */

    /* Kolom jwb_pertanyaan/jwb_referensi/jwb_hasil/jwb_temuan/jwb_catatan
       pada mutu_auditjawab sudah dihapus; formulir penilaian kini ditangani
       pada halaman Daftar Tilik (mutu_auditjawabdetail). */


    /* Tombol "Daftar Tilik" pada tiap topik (halaman detail). */
    $(document).on("click", "#topik_daftar .delik", function () {
        var audit_id = $(this).attr('audit_id');
        var dtform_id = $(this).attr('dtform_id');
         window.location.href = base_url+"/delik?audit_id="+audit_id+"&dtform_id="+dtform_id;
    });

    $('#daftaraudit').on('click', '.download', function () {
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

    $("#daftaraudit").on("click", ".detail", function () {
       // $('#pertanyaanModal').modal('show');
        var id = $(this).attr('id');
         window.location.href = base_url+'/daftaraudit/detail/'+id;
        //daftarpertanyaan.ajax.url(base_url + "/daftaraudit/listpertanyaan/"+id).load();
    });

    $("#daftaraudit").on("click", ".proses", function () {
        var id = $(this).attr('id');
        proses(id);
    });

    $("#daftaraudit").on("click", ".kembali", function () {
         var id = $(this).attr('id');
         kembali(id);
    });

    function kembali($id)
    {
        swal.fire({
            title: "Anda Yakin?",
            text: "Anda Yakin Ingin Mengembalikan Evaluasi Ini Kepada Auditee?",
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
                                        title: "Berhasil",
                                        text: "Evaluasi telah dikembalikan ke auditee!",
                                        type: "success",
                                        preConfirm: function () {
                                            daftaraudit.ajax.reload();
                                        }
                                    });
                            }else{
                                swal.fire({
                                        title: "Gagal",
                                        text: "Evaluasi tidak dapat dikembalikan ke auditee!",
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

    function proses(idku)
    {
        swal.fire({
            title: "Anda Yakin?",
            text: "Anda Yakin Ingin Memproses Formulir Ini?",
            type: "warning",
            showCancelButton: true,
            showLoaderOnConfirm: true,
            confirmButtonText: "Ya, Proses!",
            cancelButtonText: 'Tidak',
            preConfirm: function () {
                $.ajax({
                    url: base_url + "/daftaraudit/proses",
                    type: "POST",
                    data: { id: idku}
                })
                        .done(function (data) {
                            swal.fire({
                                title: "Diproses",
                                text: "Formulir Sedang Diproses!",
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

    $("#daftaraudit").on("click", ".selesai", function () {
        var id = $(this).attr('id');
        selesai(id);
    });

    function selesai($id)
    {
        swal.fire({
            title: "Anda Yakin?",
            text: "Anda Yakin Ingin Selesaikan Proses Formulir Ini?",
            type: "warning",
            showCancelButton: true,
            showLoaderOnConfirm: true,
            confirmButtonText: "Ya, Selesai!",
            cancelButtonText: 'Tidak',
            preConfirm: function () {
                $.ajax({
                    url: base_url + "/daftaraudit/selesai",
                    type: "POST",
                    data: { id: $id}
                })
                        .done(function (data) {
                            swal.fire({
                                title: "Selesai",
                                text: "Formulir Telah Selesai Diproses!",
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

    $("#kembali").on("click", function () {
         var id = $(this).attr('audit_id');
         window.location.href = base_url+'/daftaraudit/detail/'+id;
    });

$('#formtujuan').formValidation({
    framework: 'bootstrap4',
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

    var $form = $(e.target);
    var formData = new FormData(e.target);

    $.ajax({
        url: base_url + "daftaraudit/tujuan",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function () {
            Swal.fire({
                title: 'Loading...',
                allowEscapeKey: false,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        },
        success: function (data) {
            Swal.close();
            var list = data == null ? [] : (data instanceof Array ? data : [data]);
            $.each(list, function (index, org_types) {
                if (org_types.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Data telah tersimpan.',
                        showConfirmButton: false,
                        timer: 2000
                    });
                } else {
                    Swal.fire("Oops", org_types.pesan, "error");
                }
            });
            $form.formValidation('disableSubmitButtons', false)
                 .formValidation('resetForm', true);
        },
        error: function () {
            Swal.fire("Oops", "No connection!", "error");
            $form.formValidation('disableSubmitButtons', false)
                 .formValidation('resetForm', true);
        }
    });

    return false;
});

    

     $("#tambah").on("click", function () {
        $("#addModal").modal('show');
    });

});