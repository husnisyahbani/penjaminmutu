


<!-- Page -->
<div class="page">
    <div class="page-content container-fluid">

        <div class="row">
            <div class="col-lg-12">

                <div class="panel">
                    <div class="panel-heading">
                        <h3 class="panel-title">Pegawai </h3>
                    </div>
                    <div class="panel-body">
                        <form class="form-horizontal" id="formeditpegawai" autocomplete="off" action="<?php if (isset($result[0]['pgi_foto'])) {echo base_url() . 'admin/pegawai/edit?id='.$result[0]['pgi_id'];}?>" method="post" enctype="multipart/form-data">

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Photo</label>
                                <div class="col-md-9">
                                    <input type="file" id="input-file-now" name="upload_file" data-plugin="dropify" data-default-file="<?php if (isset($result[0]['pgi_foto'])) {echo base_url ().$result[0]['pgi_foto'];} ?>"/>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Nama</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="pgi_nama" value="<?php if (isset($result[0]['pgi_nama'])) echo $result[0]['pgi_nama']; ?>"/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Alamat</label>
                                <div class="col-md-9">
                                    <textarea class="form-control" id="textareaDefault" name="pgi_alamat" rows="6" data-fv-notempty="true"
                                           data-fv-notempty-message="Wajib Diisi"><?php if (isset($result[0]['pgi_alamat'])) echo $result[0]['pgi_alamat']; ?></textarea>
                                    
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">NIK</label>
                                <div class="col-md-9">
                                    <input type="number" class="form-control" name="pgi_nik" value="<?php if (isset($result[0]['pgi_nik'])) echo $result[0]['pgi_nik']; ?>"/>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">No HP</label>
                                <div class="col-md-9">
                                    <input type="number" class="form-control" name="pgi_hp" value="<?php if (isset($result[0]['pgi_hp'])) echo $result[0]['pgi_hp']; ?>"/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Email</label>
                                <div class="col-md-9">
                                    <input type="email" class="form-control" name="pgi_email" value="<?php if (isset($result[0]['pgi_email'])) echo $result[0]['pgi_email']; ?>"/>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">No Rekening</label>
                                <div class="col-md-9">
                                    <input type="number" class="form-control" name="pgi_rek" value="<?php if (isset($result[0]['pgi_rek'])) echo $result[0]['pgi_rek']; ?>"/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Nama Bank</label>
                                <div class="col-md-9">
                                    <select class="form-control" name="pgi_bank" id="pgi_bank">                                 
                                        <option value="Bank Negara Indonesia">Bank Negara Indonesia</option>
                                        <option value="Bank Rakyat Indonesia">Bank Rakyat Indonesia</option>
                                        <option value="Bank Mandiri">Bank Mandiri</option>
                                        <option value="Bank Bca">Bank Bca</option>
                                        <option value="Bank Sumsel">Bank Sumsel</option>
                                        <option value="Bank BSI">Bank BSI</option>
                                        <option value="Bank Sumsel Syariah">Bank Sumsel Syariah</option>
                                        <option value="Bank Panin">Bank Panin</option>
                                        <option value="Bank Permata">Bank Permata</option>
                                        <option value="Bank Danamon">Bank Danamon</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Golongan</label>
                                <div class="col-md-9">
                                    <select class="form-control" name="pgi_gol" id="pgi_gol">
                                        <option value="">Non PNS</option>                                 
                                        <option value="IV.d" <?php if(isset($result[0]['pgi_gol']) && $result[0]['pgi_gol'] === "IV.d")echo 'selected';?>>IV.d</option>
                                        <option value="IV.c" <?php if(isset($result[0]['pgi_gol']) && $result[0]['pgi_gol'] === "IV.c")echo 'selected';?>>IV.c</option>
                                        <option value="IV.b" <?php if(isset($result[0]['pgi_gol']) && $result[0]['pgi_gol'] === "IV.b")echo 'selected';?>>IV.b</option>
                                        <option value="IV.a" <?php if(isset($result[0]['pgi_gol']) && $result[0]['pgi_gol'] === "IV.a")echo 'selected';?>>IV.a</option>
                                        <option value="III.d" <?php if(isset($result[0]['pgi_gol']) && $result[0]['pgi_gol'] === "III.d")echo 'selected';?>>III.d</option>
                                        <option value="III.c" <?php if(isset($result[0]['pgi_gol']) && $result[0]['pgi_gol'] === "III.c")echo 'selected';?>>III.c</option>
                                        <option value="III.b" <?php if(isset($result[0]['pgi_gol']) && $result[0]['pgi_gol'] === "III.b")echo 'selected';?>>III.b</option>
                                        <option value="III.a" <?php if(isset($result[0]['pgi_gol']) && $result[0]['pgi_gol'] === "III.a")echo 'selected';?>>III.a</option>
                                        <option value="II.d" <?php if(isset($result[0]['pgi_gol']) && $result[0]['pgi_gol'] === "II.d")echo 'selected';?>>II.d</option>
                                        <option value="II.c" <?php if(isset($result[0]['pgi_gol']) && $result[0]['pgi_gol'] === "II.c")echo 'selected';?>>II.c</option>
                                        <option value="II.b" <?php if(isset($result[0]['pgi_gol']) && $result[0]['pgi_gol'] === "II.b")echo 'selected';?>>II.b</option>
                                        <option value="II.a" <?php if(isset($result[0]['pgi_gol']) && $result[0]['pgi_gol'] === "II.a")echo 'selected';?>>II.a</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">NPWP</label>
                                <div class="col-md-9">
                                    <input type="number" class="form-control" name="pgi_npwp" value="<?php if (isset($result[0]['pgi_npwp'])) echo $result[0]['pgi_npwp']; ?>"/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">ID SIMRS</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="pgi_idsimrs" value="<?php if (isset($result[0]['pgi_idsimrs'])) echo $result[0]['pgi_idsimrs']; ?>"/>
                                </div>
                            </div>
                            

                            <div class="text-right">
                                <button type="submit" class="btn btn-primary" id="validateButton" name="submitpgi">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- End Panel Wizard Form Container -->
            </div>
        </div> 


    </div>
</div>
<!-- End Page -->






