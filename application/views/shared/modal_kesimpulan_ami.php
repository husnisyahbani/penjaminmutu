<div class="modal fade" id="modalKesimpulanAmi" tabindex="-1" role="dialog" aria-labelledby="judulKesimpulanAmi" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document"><div class="modal-content">
    <form id="formKesimpulanAmi">
      <div class="modal-header"><h4 class="modal-title" id="judulKesimpulanAmi">Kesimpulan Audit</h4><button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button></div>
      <div class="modal-body">
        <p>Jawab kesimpulan audit sebelum mengunduh Laporan AMI.</p>
        <input type="hidden" name="audit_id" id="kesimpulanAuditId">
        <div class="form-group"><label for="kesimpulan1">1. Sistem dokumen cukup lengkap dan terstruktur untuk mendukung pelaksanaan Sistem Penjamin Mutu Internal.</label>
          <select class="form-control" name="kesimpulan1" id="kesimpulan1" required><option value="">Pilih jawaban</option><option>Ya</option><option>Tidak</option><option>Lainnya</option></select>
          <input class="form-control mt-10" name="kesimpulan1_lainnya" id="kesimpulan1_lainnya" maxlength="255" placeholder="Sebutkan lainnya" hidden>
        </div>
        <div class="form-group"><label for="kesimpulan2">2. Program studi/unit telah menjalankan Sistem Penjamin Mutu Internal secara konsisten dan berkelanjutan.</label>
          <select class="form-control" name="kesimpulan2" id="kesimpulan2" required><option value="">Pilih jawaban</option><option>Ya</option><option>Tidak</option><option>Lainnya</option></select>
          <input class="form-control mt-10" name="kesimpulan2_lainnya" id="kesimpulan2_lainnya" maxlength="255" placeholder="Sebutkan lainnya" hidden>
        </div>
        <div class="form-group"><label for="kesimpulan3">3. Temuan pada periode audit ini adalah:</label>
          <select class="form-control" name="kesimpulan3" id="kesimpulan3" required><option value="">Pilih kategori</option><option>Mayor</option><option>Minor</option><option>Observasi</option></select>
        </div>
        <div id="kesimpulanAmiError" class="alert alert-danger" role="alert" hidden></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary" id="simpanKesimpulanAmi">Simpan dan Unduh</button></div>
    </form>
  </div></div>
</div>
