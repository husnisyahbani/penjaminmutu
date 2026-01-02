$(function () {

    var pengumumanlist = $('#pengumuman').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "searching": true,
        "order": [],
        "columnDefs": [
            {"targets": [0,3,5], "orderable": false}
        ],
        "ajax": {
            "url": base_url + "/pengumuman/listpengumuman",
            "type": "POST"
        }
    });

    $("#pengumuman").on("click", ".edit", function () {
        var id = $(this).attr('id');
        location.href = base_url + "/pengumuman/edit/" + id;
    });

    $("#pengumuman").on("click", ".delete", function () {
        var id = $(this).attr('id');
        hapuspengumuman(id);
    });   

    $("#tambahpengumuman").on("click", function () {
         location.href = base_url + "/pengumuman/tambah/";
    });

    

    function hapuspengumuman($id)
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
                    url: base_url + "/pengumuman/hapus",
                    type: "POST",
                    pengumuman: { id: $id}
                })
                        .done(function (pengumuman) {
                            swal.fire({
                                title: "Hapus",
                                text: "Data Telah Terhapus!",
                                type: "success",
                                preConfirm: function () {
                                    pengumumanlist.ajax.reload();
                                }
                            });
                        })
                        .error(function (pengumuman) {
                            swal.fire("Oops", "No connection!", "error");
                        });
            }
        });
    }

    $("#formaddpengumuman").formValidation({
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

    $("#formeditpengumuman").formValidation({
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


