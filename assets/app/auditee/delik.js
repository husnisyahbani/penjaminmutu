/* Halaman delik auditee.
 *
 * Sejak halaman ini disusun ulang seperti PTK (informasi pertanyaan + kartu
 * ringkasan + tabel butir), skripnya hanya menangani tombol Kembali ke
 * halaman detail audit. Modal "Masukkan Jawaban" lama sudah dihapus - jawaban
 * auditee kini diisi pada halaman detail audit (assets/app/auditee/
 * jawaban-lingkup.js).
 */
$(function () {

    $("#kembali").on("click", function () {
        var id = $(this).attr('audit_id');
        window.location.href = base_url + '/dashboard/detail/' + id;
    });

});
