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
                <span><b>Auditee : </b><?=$result['auditee']?></span>
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
            <form class="form-horizontal" id="formtujuan" autocomplete="off" method="post" enctype="multipart/form-data">
                            <div class="form-group row">
                                <div class="col-md-12">
                                  <textarea id="jwb_tujuan" class="editor" name="jwb_tujuan"  rows="6" data-fv-notempty="true"
                                           data-fv-notempty-message="Wajib Diisi"></textarea>
                                </div>
                            </div>

                            <input type="hidden" name="audit_id" value="<?php echo $audit_id;?>"/>
                            <input type="hidden" name="dtform_id" value="<?php echo $dtform_id;?>"/>

                            <div class="text-right">
                                <button type="submit" class="btn btn-primary" id="submittujuan" name="submittujuan">Simpan Tujuan</button>
                            </div>
                        </form>

                        <hr/>

            

            <div class="font-size-20">referensi</div>
            <form class="form-horizontal" id="formreferensi" autocomplete="off" method="post" enctype="multipart/form-data">
                            <div class="form-group row">
                                <div class="col-md-12">
                                  <textarea id="jwb_referensi" class="editor" name="jwb_referensi"  rows="6" data-fv-notempty="true"
                                           data-fv-notempty-message="Wajib Diisi"></textarea>
                                </div>
                            </div>

                            <div class="text-right">
                                <button type="submit" class="btn btn-primary" id="submitreferensi" name="submitreferensi">Simpan Referensi</button>
                            </div>
                        </form>

                        <div class="font-size-20">Pertanyaan</div>
            <form class="form-horizontal" id="formpertanyaan" autocomplete="off" method="post" enctype="multipart/form-data">
                            <div class="form-group row">
                                <div class="col-md-12">
                                  <textarea id="jwb_pertanyaan" class="editor" name="jwb_pertanyaan"  rows="6" data-fv-notempty="true"
                                           data-fv-notempty-message="Wajib Diisi"></textarea>
                                </div>
                            </div>

                            <div class="text-right">
                                <button type="submit" class="btn btn-primary" id="submitpertanyaan" name="submitpertanyaan">Simpan Pertanyaan</button>
                            </div>
                        </form>


                        <div class="font-size-20">Temuan</div>
            <form class="form-horizontal" id="formtemuan" autocomplete="off" method="post" enctype="multipart/form-data">
                            <div class="form-group row">
                                <div class="col-md-12">
                                <select class="form-control" name="jwb_temuan" id="jwb_temuan" data-fv-notempty="true"
                                        data-fv-notempty-message="Wajib Dipilih"> 
                                  <option value="">--Pilih Temuan--</option>                                
                                  <option value="S" <?php if(isset($jawab['jwb_temuan']) && $jawab['jwb_temuan'] === 'S')echo 'selected'; ?>>S</option>
                                  <option value="OB" <?php if(isset($jawab['jwb_temuan']) && $jawab['jwb_temuan'] === 'OB')echo 'selected'; ?>>OB</option>
                                  <option value="TS MINOR" <?php if(isset($jawab['jwb_temuan']) && $jawab['jwb_temuan'] === 'TS MINOR')echo 'selected'; ?>>TS MINOR</option>
                                  <option value="TS MAYOR" <?php if(isset($jawab['jwb_temuan']) && $jawab['jwb_temuan'] === 'TS MAYOR')echo 'selected'; ?>>TS MAYOR</option>
                                </select>
                                </div>
                            </div>

                            <div class="text-right">
                                <button type="submit" class="btn btn-primary" id="submittemuan" name="submittemuan">Simpan Temuan</button>
                            </div>
                        </form>

                        <div class="font-size-20">Hasil</div>
            <form class="form-horizontal" id="formhasil" autocomplete="off" method="post" enctype="multipart/form-data">
                            <div class="form-group row">
                                <div class="col-md-12">
                                  <textarea id="jwb_hasil" class="editor" name="jwb_hasil"  rows="6" data-fv-notempty="true"
                                           data-fv-notempty-message="Wajib Diisi"></textarea>
                                </div>
                            </div>

                            <div class="text-right">
                                <button type="submit" class="btn btn-primary" id="submithasil" name="submithasil">Simpan Hasil</button>
                            </div>
                        </form>

                        <div class="font-size-20">Catatan</div>
            <form class="form-horizontal" id="formcatatan" autocomplete="off" method="post" enctype="multipart/form-data">
                            <div class="form-group row">
                                <div class="col-md-12">
                                  <textarea id="jwb_catatan" class="editor" name="jwb_catatan"  rows="6" data-fv-notempty="true"
                                           data-fv-notempty-message="Wajib Diisi"></textarea>
                                </div>
                            </div>

                            <div class="text-right">
                                <button type="submit" class="btn btn-primary" id="submitcatatan" name="submitcatatan">Simpan Catatan</button>
                            </div>
                        </form>

            <!-- <div class="font-size-20">Tujuan</div>
            <textarea id="jwb_tujuan" class="editor" name="jwb_tujuan"></textarea>
            <button type="submit" class="btn btn-primary" id="submittujuan" dtform_id="<?=$dtform_id?>" audit_id="<?=$audit_id?>">Simpan Tujuan</button> -->

            <!-- <hr/>
            <div class="font-size-20">Pertanyaan</div>
            <textarea id="jwb_pertanyaan" class="editor" name="jwb_pertanyaan"></textarea>
            <button type="submit" class="btn btn-primary" id="submitpertanyaan" dtform_id="<?=$dtform_id?>" audit_id="<?=$audit_id?>">Simpan Pertanyaan</button>

            <hr/>
            <div class="font-size-20">Referensi</div>
            <textarea id="jwb_referensi" class="editor" name="jwb_referensi"></textarea>
            <button type="submit" class="btn btn-primary" id="submitreferensi" dtform_id="<?=$dtform_id?>" audit_id="<?=$audit_id?>">Simpan Referensi</button>

            <hr/>
            <div class="font-size-20">Temuan</div>
            
              <select class="form-control" name="jwb_temuan" id="jwb_temuan" data-fv-notempty="true"
                      data-fv-notempty-message="Wajib Dipilih"> 
                <option value="">--Pilih Temuan--</option>                                
                <option value="S" <?php if(isset($jawab['jwb_temuan']) && $jawab['jwb_temuan'] === 'S')echo 'selected'; ?>>S</option>
                <option value="OB" <?php if(isset($jawab['jwb_temuan']) && $jawab['jwb_temuan'] === 'OB')echo 'selected'; ?>>OB</option>
                <option value="TS MINOR" <?php if(isset($jawab['jwb_temuan']) && $jawab['jwb_temuan'] === 'TS MINOR')echo 'selected'; ?>>TS MINOR</option>
                <option value="TS MAYOR" <?php if(isset($jawab['jwb_temuan']) && $jawab['jwb_temuan'] === 'TS MAYOR')echo 'selected'; ?>>TS MAYOR</option>
              </select>
            
            <button type="submit" class="btn btn-primary" id="submittemuan" dtform_id="<?=$dtform_id?>" audit_id="<?=$audit_id?>">Simpan Temuan</button>

            <hr/>
            <div class="font-size-20">Hasil</div>
            <textarea id="jwb_hasil" class="editor" name="jwb_hasil"></textarea>
            <button type="submit" class="btn btn-primary" id="submithasil" dtform_id="<?=$dtform_id?>" audit_id="<?=$audit_id?>">Simpan Hasil</button>

            <hr/>
            <div class="font-size-20">Catatan</div>
            <textarea id="jwb_catatan" class="editor" name="jwb_catatan"></textarea>
            <button type="submit" class="btn btn-primary" id="submitcatatan" dtform_id="<?=$dtform_id?>" audit_id="<?=$audit_id?>">Simpan Catatan</button>
          </div> -->
        </div>
        <!-- End Widget Info -->
      </div>
    </div>
  </div>
</div>

