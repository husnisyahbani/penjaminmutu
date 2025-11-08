<div class="page">
  <div class="page-content container-fluid">
    <div class="row" data-by-row="true">
      <div class="col-xl-12 col-lg-24S">
        <!-- Widget Info -->
        <div class="card card-shadow">
          <div class="card-block px-30 py-20" style="position: relative;">

            <!-- Tombol kanan atas -->
            <div style="position: absolute; top: 20px; right: 30px;">
              <button type="button" class="btn btn-warning btn-sm btn_icon" id="kembali" audit_id="<?=$audit_id?>">
                <i class="icon md-undo" aria-hidden="true"></i> Kembali
              </button>
            </div>

            <div class="mb-20">
              <div class="font-size-20"><?=$result['form_nama']?></div>
              <hr/>
              <div class="font-size-14 grey-500">
                <span><b>Auditor : </b><?=$result['auditor']?></span>
              </div>
              <hr/>
              <div class="font-size-14 grey-500">
                <span><b>Auditee : <?=$result['auditee']?></b></span>
              </div>
              <hr/>
            </div>

            <div class="font-size-20">Evaluasi</div>
            <p><?php if(isset($jawab['dtform_pertanyaan'])) echo $jawab['dtform_pertanyaan'];?></p>
            <p><?php if(isset($jawab['dtform_lingkup'])) echo $jawab['dtform_lingkup'];?></p>
            <hr/>

            <div class="font-size-20">Hasil Evaluasi</div>
            <p><?php if(isset($jawab['jwb_jawaban'])) echo $jawab['jwb_jawaban'];?></p>
            <hr/>

            <div class="font-size-20">Tujuan</div>
            <p><textarea id="jwb_tujuan" class="editor" name="jwb_tujuan"></textarea></p>
            <button type="submit" class="btn btn-primary" id="submitjawaban" dtform_id="<?=$dtform_id?>" audit_id="<?=$audit_id?>">Simpan Tujuan</button>

            <hr/>
            <div class="font-size-20">Pertanyaan</div>
            <p><textarea id="jwb_pertanyaan" class="editor" name="jwb_pertanyaan"></textarea></p>
            <button type="submit" class="btn btn-primary" id="submitpertanyaan" dtform_id="<?=$dtform_id?>" audit_id="<?=$audit_id?>">Simpan Pertanyaan</button>

            <hr/>
            <div class="font-size-20">Referensi</div>
            <p><textarea id="jwb_referensi" class="editor" name="jwb_referensi"></textarea></p>
            <button type="submit" class="btn btn-primary" id="submitreferensi" dtform_id="<?=$dtform_id?>" audit_id="<?=$audit_id?>">Simpan Referensi</button>

            <hr/>
            <div class="font-size-20">Temuan</div>
            <p>
              <select class="form-control" name="jwb_temuan" id="jwb_temuan" data-fv-notempty="true"
                      data-fv-notempty-message="Wajib Dipilih"> 
                <option value="">--Pilih Temuan--</option>                                
                <option value="S" <?php if(isset($jawab['jwb_temuan']) && $jawab['jwb_temuan'] === 'S')echo 'selected'; ?>>S</option>
                <option value="OB" <?php if(isset($jawab['jwb_temuan']) && $jawab['jwb_temuan'] === 'OB')echo 'selected'; ?>>OB</option>
                <option value="TS MINOR" <?php if(isset($jawab['jwb_temuan']) && $jawab['jwb_temuan'] === 'TS MINOR')echo 'selected'; ?>>TS MINOR</option>
                <option value="TS MAYOR" <?php if(isset($jawab['jwb_temuan']) && $jawab['jwb_temuan'] === 'TS MAYOR')echo 'selected'; ?>>TS MAYOR</option>
              </select>
            </p>
            <button type="submit" class="btn btn-primary" id="submittemuan" dtform_id="<?=$dtform_id?>" audit_id="<?=$audit_id?>">Simpan Temuan</button>

            <hr/>
            <div class="font-size-20">Hasil</div>
            <p><textarea id="jwb_hasil" class="editor" name="jwb_hasil"></textarea></p>
            <button type="submit" class="btn btn-primary" id="submithasil" dtform_id="<?=$dtform_id?>" audit_id="<?=$audit_id?>">Simpan Hasil</button>

            <hr/>
            <div class="font-size-20">Catatan</div>
            <p><textarea id="jwb_catatan" class="editor" name="jwb_catatan"></textarea></p>
            <button type="submit" class="btn btn-primary" id="submitcatatan" dtform_id="<?=$dtform_id?>" audit_id="<?=$audit_id?>">Simpan Catatan</button>
          </div>
        </div>
        <!-- End Widget Info -->
      </div>
    </div>
  </div>
</div>

