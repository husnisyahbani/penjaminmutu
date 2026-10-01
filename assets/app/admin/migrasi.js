/* Halaman migrasi lingkup audit (menu admin > Migrasi Lingkup).
   Menjalankan pemindahan data lama dan penghapusan kolom lama. */
$(function () {

    function kirim(url, judul, teks, tombol, judulBerhasil) {
        swal.fire({
            title: judul,
            text: teks,
            type: 'warning',
            showCancelButton: true,
            showLoaderOnConfirm: true,
            confirmButtonText: tombol,
            cancelButtonText: 'Tidak',
            preConfirm: function () {
                return $.ajax({
                    url: base_url + url,
                    type: 'POST',
                    dataType: 'json'
                }).then(function (data) {
                    if (!data.status) {
                        swal.showValidationMessage(data.pesan);
                        return false;
                    }
                    return data;
                });
            }
        }).then(function (hasil) {
            if (hasil.value) {
                swal.fire({
                    title: judulBerhasil,
                    html: '<div style="text-align:left">' + hasil.value.pesan + '</div>',
                    type: 'success'
                }).then(function () {
                    window.location.reload();
                });
            }
        });
    }

    $('#jalankan').on('click', function () {
        kirim('/migrasi/jalankan',
            'Jalankan Migrasi Lingkup?',
            'Isi kolom lama akan dipecah menjadi butir lingkup dan jawaban tilik lama dipindahkan ke bentuk baru.',
            'Ya, Jalankan!',
            'Migrasi Selesai');
    });

    $('#hapuskolom').on('click', function () {
        kirim('/migrasi/hapuskolom',
            'Hapus Kolom Lama?',
            'Kolom detailform.dtform_lingkup akan dihapus permanen. Pastikan migrasi sudah benar.',
            'Ya, Hapus!',
            'Kolom Dihapus');
    });
});
