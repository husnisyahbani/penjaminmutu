$(function () {

    var beritalist = $('#berita').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "searching": true,
        "order": [],
        "columnDefs": [
            {"targets": [0,3,5], "orderable": false}
        ],
        "ajax": {
            "url": base_url + "/berita/listberita",
            "type": "POST"
        }
    });

    $("#berita").on("click", ".edit", function () {
        var id = $(this).attr('id');
        location.href = base_url + "/berita/edit/" + id;
    });

    $("#berita").on("click", ".delete", function () {
        var id = $(this).attr('id');
        hapusberita(id);
    });   

    $("#tambahberita").on("click", function () {
         location.href = base_url + "/berita/tambah/";
    });

    

    function hapusberita($id)
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
                    url: base_url + "/berita/hapus",
                    type: "POST",
                    berita: { id: $id}
                })
                        .done(function (berita) {
                            swal.fire({
                                title: "Hapus",
                                text: "Data Telah Terhapus!",
                                type: "success",
                                preConfirm: function () {
                                    beritalist.ajax.reload();
                                }
                            });
                        })
                        .error(function (berita) {
                            swal.fire("Oops", "No connection!", "error");
                        });
            }
        });
    }

    $("#formaddberita").formValidation({
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
    });

    $("#formeditberita").formValidation({
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
    });

});


