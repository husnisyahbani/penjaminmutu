/* ==========================================================================
   Pengaturan Home - script panel admin
   Halaman: admin/pengaturanhome
   ========================================================================== */
$(function () {

    /* Catatan: antar bagian (sub menu) berpindah dengan memuat halaman baru,
       jadi tidak ada lagi pengelolaan tab di sisi klien. */

    /* ---------- notifikasi ---------- */
    function muat(pesan) {
        swal.fire({
            title: 'Memproses',
            allowEscapeKey: false,
            allowOutsideClick: false,
            onOpen: function () {
                swal.showLoading();
            }
        });
    }

    function sukses(pesan) {
        swal.fire({
            title: 'Berhasil',
            text: pesan || 'Perubahan telah disimpan.',
            type: 'success'
        }).then(function () {
            window.location.reload();
        });
    }

    function gagal(pesan) {
        swal.fire('Oops', pesan || 'Terjadi kesalahan.', 'error');
    }

    /* ---------- buat tabel pengaturan ---------- */
    $('#pasangTabel').on('click', function () {
        swal.fire({
            title: 'Buat Tabel Pengaturan?',
            text: 'Tabel mutu_home_setting dan mutu_home_item akan dibuat beserta isi bawaannya.',
            type: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, buat sekarang',
            cancelButtonText: 'Batal',
            preConfirm: function () {
                return $.ajax({
                    url: base_url + '/pengaturanhome/pasang',
                    type: 'POST',
                    dataType: 'json'
                });
            }
        }).then(function (hasil) {
            if (hasil && hasil.value && hasil.value.status) {
                sukses(hasil.value.pesan);
            } else if (hasil && hasil.value) {
                gagal(hasil.value.pesan);
            }
        });
    });

    /* ---------- simpan pengaturan ---------- */
    $('.form-pengaturan').on('submit', function (e) {
        e.preventDefault();

        var $form = $(this);
        var formData = new FormData(this);

        $.ajax({
            url: base_url + '/pengaturanhome/simpan',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: function () {
                muat();
            },
            success: function (data) {
                swal.close();

                if (data.status) {
                    sukses(data.pesan);
                } else {
                    gagal(data.pesan);
                }
            },
            error: function () {
                swal.close();
                gagal('Tidak dapat menghubungi server.');
            }
        });
    });

    /* ======================================================================
       ITEM (konten berulang)
       =================================================================== */

    /* tampilkan hanya field yang dipakai grup terpilih */
    function aturField(grup) {
        var konfigurasi = HOME_ITEM_GROUPS[grup];
        var fields = konfigurasi && konfigurasi.fields ? konfigurasi.fields : [];

        $('#formitem').find('[data-field]').each(function () {
            var nama = $(this).attr('data-field');
            $(this).toggle(fields.indexOf(nama) !== -1);
        });
    }

    function resetForm() {
        var $form = $('#formitem');

        $form[0].reset();
        $('#item_id').val('');
        $('#item_gambar_lama').val('');
        $('#item_gambar_preview').attr('src', '').hide();
        $('#item_gambar_kosong').show();
        $('#item_urutan').val(0);
        $('#item_status').val('1');
    }

    $('.tambah-item').on('click', function () {
        var grup = $(this).attr('data-grup');
        var konfigurasi = HOME_ITEM_GROUPS[grup] || {};

        resetForm();
        aturField(grup);

        $('#item_grup').val(grup);
        $('#itemModalTitle').text('Tambah ' + (konfigurasi.judul || 'Konten'));
        $('#itemModal').modal('show');
    });

    $('.edit-item').on('click', function () {
        var id = $(this).attr('data-id');

        $.ajax({
            url: base_url + '/pengaturanhome/getItem/' + id,
            type: 'GET',
            dataType: 'json',
            beforeSend: function () {
                muat();
            },
            success: function (item) {
                swal.close();

                if (!item || !item.status) {
                    gagal('Konten tidak ditemukan.');
                    return;
                }

                resetForm();
                aturField(item.item_grup);

                $('#item_id').val(item.item_id);
                $('#item_grup').val(item.item_grup);
                $('#item_judul').val(item.item_judul);
                $('#item_isi').val(item.item_isi);
                $('#item_ikon').val(item.item_ikon);
                $('#item_link').val(item.item_link);
                $('#item_label_link').val(item.item_label_link);
                $('#item_urutan').val(item.item_urutan);
                $('#item_status').val(item.item_status);

                if (item.item_warna && /^#[0-9a-f]{6}$/i.test(item.item_warna)) {
                    $('#item_warna').val(item.item_warna);
                }

                $('#item_gambar_lama').val(item.item_gambar);

                if (item.item_gambar) {
                    var sumber = /^(https?:)?\/\//i.test(item.item_gambar)
                        ? item.item_gambar
                        : root_url + item.item_gambar;

                    $('#item_gambar_preview').attr('src', sumber).show();
                    $('#item_gambar_kosong').hide();
                }

                $('#itemModalTitle').text('Edit ' + ((HOME_ITEM_GROUPS[item.item_grup] || {}).judul || 'Konten'));
                $('#itemModal').modal('show');
            },
            error: function () {
                swal.close();
                gagal('Tidak dapat menghubungi server.');
            }
        });
    });

    $('#formitem').on('submit', function (e) {
        e.preventDefault();

        var $form = $(this);
        var id = $('#item_id').val();
        var alamat = id ? base_url + '/pengaturanhome/edititem' : base_url + '/pengaturanhome/tambahitem';
        var formData = new FormData(this);

        $.ajax({
            url: alamat,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: function () {
                $('#itemModal').modal('hide');
                muat();
            },
            success: function (data) {
                swal.close();

                if (data.status) {
                    sukses(data.pesan);
                } else {
                    gagal(data.pesan);
                }
            },
            error: function () {
                swal.close();
                gagal('Tidak dapat menghubungi server.');
            }
        });
    });

    $('.hapus-item').on('click', function () {
        var id = $(this).attr('data-id');

        swal.fire({
            title: 'Hapus konten ini?',
            text: 'Konten yang dihapus tidak dapat dikembalikan.',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
            preConfirm: function () {
                return $.ajax({
                    url: base_url + '/pengaturanhome/hapusitem',
                    type: 'POST',
                    data: { item_id: id },
                    dataType: 'json'
                });
            }
        }).then(function (hasil) {
            if (hasil && hasil.value && hasil.value.status) {
                sukses(hasil.value.pesan);
            } else if (hasil && hasil.value) {
                gagal(hasil.value.pesan);
            }
        });
    });

    $('.status-item').on('click', function () {
        var id = $(this).attr('data-id');

        $.ajax({
            url: base_url + '/pengaturanhome/statusitem',
            type: 'POST',
            data: { item_id: id },
            dataType: 'json',
            beforeSend: function () {
                muat();
            },
            success: function (data) {
                swal.close();

                if (data.status) {
                    sukses('Status konten berhasil diubah.');
                } else {
                    gagal(data.pesan);
                }
            },
            error: function () {
                swal.close();
                gagal('Tidak dapat menghubungi server.');
            }
        });
    });

    /* pratinjau gambar pada form item */
    $('#item_gambar').on('change', function () {
        var berkas = this.files && this.files[0];

        if (!berkas) { return; }

        var pembaca = new FileReader();

        pembaca.onload = function (e) {
            $('#item_gambar_preview').attr('src', e.target.result).show();
            $('#item_gambar_kosong').hide();
        };

        pembaca.readAsDataURL(berkas);
    });

    $('#hapus_gambar').on('change', function () {
        if ($(this).is(':checked')) {
            $('#item_gambar_preview').hide();
            $('#item_gambar_kosong').show();
        } else if ($('#item_gambar_lama').val()) {
            $('#item_gambar_preview').show();
            $('#item_gambar_kosong').hide();
        }
    });

});
