/* Halaman delik auditee.
 *
 * Halaman disusun seperti PTK (informasi pertanyaan + empat kartu ringkasan +
 * tabel butir). Skripnya menangani tombol Kembali ke halaman detail audit dan
 * penyimpanan rencana koreksi tiap butir langsung dari kolomnya (butir "S"
 * tidak perlu koreksi, jadi tidak diberi kotak isian oleh view). Modal
 * "Masukkan Jawaban" lama sudah dihapus - jawaban auditee kini diisi pada
 * halaman detail audit (assets/app/auditee/jawaban-lingkup.js).
 */
$(function () {

    $("#kembali").on("click", function () {
        var id = $(this).attr('audit_id');
        window.location.href = base_url + '/dashboard/detail/' + id;
    });

    /* Rencana koreksi tiap butir disimpan langsung dari kolomnya.
       Tombol disable + pesan singkat, tanpa memuat ulang halaman. */
    $(document).on('click', '.koreksi-simpan', function () {
        var kotak  = $(this).closest('.koreksi-kotak');
        var pesan  = kotak.find('.koreksi-pesan');
        var tombol = $(this);

        tombol.prop('disabled', true);
        pesan.removeClass('text-danger text-success').text('Menyimpan...');

        $.ajax({
            url: base_url + '/delik/koreksi',
            type: 'POST',
            dataType: 'json',
            data: {
                audit_id:  kotak.attr('data-audit_id'),
                dtjwb_id:  kotak.attr('data-dtjwb_id'),
                koreksi:   kotak.find('.koreksi-isi').val()
            }
        }).done(function (data) {
            if (data && data.status) {
                pesan.addClass('text-success').text('Tersimpan');
            } else {
                pesan.addClass('text-danger').text('Gagal menyimpan');
            }
        }).fail(function () {
            pesan.addClass('text-danger').text('Gagal menyimpan');
        }).always(function () {
            tombol.prop('disabled', false);
        });
    });

});
