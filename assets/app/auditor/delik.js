$(function () {
    $("#edittujuan").on("click", function () {
    // Ambil isi dari elemen <p id="tujuan">
    var isiTujuan = $("#tujuan").html();

    // Tampilkan modal
    $('#tujuanModal').modal('show');

    // Setelah modal tampil, masukkan isi ke Summernote
    $('#tujuanModal').on('shown.bs.modal', function () {
        $('#jwb_tujuan').summernote('code', isiTujuan);
    });
});


    $("#editreferensi").on("click", function () {
       $('#referensiModal').modal('show');
    });

    $("#editpertanyaan").on("click", function () {
       $('#pertanyaanModal').modal('show');
    });


    $("#edithasil").on("click", function () {
       $('#hasilModal').modal('show');
    });

    $("#edittemuan").on("click", function () {
       $('#temuanModal').modal('show');
    });

    $("#editcatatan").on("click", function () {
       $('#catatanModal').modal('show');
    });

});