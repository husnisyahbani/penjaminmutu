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
            /* Kolom No, Status, dan Aksi tidak perlu diurutkan. */
            {"targets": [0,6,7], "orderable": false}
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
        var tombol = $(this);
        hapus(tombol.attr('id'), tombol.data('nama') || '');
    });
    
    $("#akun").on("click", ".reset", function () {
        var id = $(this).attr('id');
        reset(id);
    });

    /* Kolom Aksi hanya satu tombol (ikon titik tiga). Menu pilihannya
       ditutup kembali setelah salah satu aksi dipilih. */
    $("#akun").on("click", ".dropdown-item", function () {
        var induk = $(this).closest('.dropdown');

        if ($.fn.dropdown) {
            induk.find('[data-toggle="dropdown"]').dropdown('hide');
        }
        induk.removeClass('show');
        induk.find('.dropdown-menu').removeClass('show');
    });

    /* Tombol status menggantikan tombol edit: pada laman ini yang diubah
       hanya status Aktif / Tidak Aktif akunnya. */
    $("#akun").on("click", ".status", function () {
        var tombol = $(this);
        ubahStatus(
            tombol.attr('id'),
            tombol.attr('data-aktif') === '1',
            tombol.data('nama') || 'akun ini'
        );
    });

    /* Login sebagai user terpilih (khusus admin PPM). */
    $("#akun").on("click", ".login-as", function () {
        var id = $(this).attr('id');
        var nama = $(this).data('nama') || '';
        var username = $(this).data('username') || '';
        var role = $(this).data('role') || '';
        masukSebagai(id, nama, username, role);
    });

    function masukSebagai(id, nama, username, role)
    {
        swal.fire({
            title: "Login Sebagai",
            html: "Masuk sebagai <b>" + nama + "</b> (" + username + " &middot; " + role + ")?"
                + "<br><br><small>Anda akan memakai hak akses akun ini. "
                + "Kembali ke akun admin melalui menu foto profil &rarr; <b>Kembali sebagai Admin</b>.</small>",
            type: "warning",
            showCancelButton: true,
            showLoaderOnConfirm: true,
            confirmButtonText: "Ya, Masuk!",
            cancelButtonText: 'Batal',
            preConfirm: function () {
                return $.ajax({
                    url: base_url + "/akun/loginsebagai",
                    type: "POST",
                    data: { id: id },
                    dataType: 'json'
                }).then(function (res) {
                    return res;
                }, function () {
                    return { status: false, pesan: 'Tidak dapat menghubungi server.' };
                });
            }
        }).then(function (hasil) {
            var res = hasil.value;

            if (!res || res.status !== true) {
                swal.fire("Gagal", (res && res.pesan) ? res.pesan : "Login sebagai user gagal.", "error");
                return;
            }

            location.href = res.redirect;
        });
    }
    
    

    /* ------------------------------ pembantu ------------------------------ */

    function pesan(judul, teks, tipe) {
        swal.fire(judul, teks, tipe || 'info');
    }

    function muat() {
        swal.fire({
            title: 'Loading',
            allowEscapeKey: false,
            allowOutsideClick: false,
            didOpen: function () { swal.showLoading(); }
        });
    }

    function selesai(teks) {
        swal.fire({
            title: "Berhasil",
            text: teks,
            type: "success"
        }).then(function () {
            location.href = base_url + "/akun";
        });
    }

    /* --------------------- aktif / nonaktif akun --------------------- */

    function ubahStatus(id, sedangAktif, nama)
    {
        swal.fire({
            title: sedangAktif ? "Nonaktifkan akun?" : "Aktifkan kembali akun?",
            html: sedangAktif
                ? "<b>" + nama + "</b> tidak dapat masuk ke aplikasi lagi dan tidak "
                  + "muncul pada pilihan auditor/auditee. Data audit yang lalu tetap tersimpan."
                : "<b>" + nama + "</b> dapat masuk ke aplikasi kembali.",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: sedangAktif ? "Ya, Nonaktifkan!" : "Ya, Aktifkan!",
            cancelButtonText: 'Tidak'
        }).then(function (konfirmasi) {
            if (!konfirmasi.value) {
                return;
            }

            muat();
            $.ajax({
                url: base_url + "/akun/status",
                type: "POST",
                data: { id: id, aktif: sedangAktif ? 0 : 1 },
                dataType: "json",
                success: function (hasil) {
                    swal.close();
                    if (!hasil || !hasil.status) {
                        pesan('Oops', hasil && hasil.pesan ? hasil.pesan : 'Status akun gagal diubah.', 'error');
                        return;
                    }
                    selesai(hasil.pesan);
                },
                error: function () {
                    swal.close();
                    pesan('Oops', 'Tidak dapat menghubungi server.', 'error');
                }
            });
        });
    }

    /* --------------------- hapus permanen --------------------- */

    function hapus($id, $nama)
    {
        swal.fire({
            title: "Hapus permanen?",
            html: "Akun <b>" + ($nama || 'ini') + "</b> akan dibuang dari database dan "
                + "tidak dapat dikembalikan.<br><br>Bila akun ini pernah mengikuti audit, "
                + "sebaiknya pilih <b>Nonaktifkan</b> saja supaya data auditnya tetap utuh.",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, Hapus!",
            cancelButtonText: 'Tidak'
        }).then(function (konfirmasi) {
            if (!konfirmasi.value) {
                return;
            }

            muat();
            $.ajax({
                url: base_url + "/akun/hapus",
                type: "POST",
                data: { id: $id },
                dataType: "json",
                success: function (hasil) {
                    swal.close();
                    if (!hasil || !hasil.status) {
                        pesan('Oops', hasil && hasil.pesan ? hasil.pesan : 'Akun gagal dihapus.', 'error');
                        return;
                    }
                    selesai(hasil.pesan);
                },
                error: function () {
                    swal.close();
                    pesan('Oops', 'Tidak dapat menghubungi server.', 'error');
                }
            });
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

    
    
    



});


