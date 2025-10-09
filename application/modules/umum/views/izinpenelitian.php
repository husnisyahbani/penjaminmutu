<!-- Page -->
<div
    class="page vertical-align text-center"
    data-animsition-in="fade-in"
    data-animsition-out="fade-out">>
    <div class="page-content vertical-align-middle">
        <div class="panel">
            <div class="panel-body">
                <div class="brand">
                    <h2 class="brand-text font-size-18"><?=$title?></h2>
                </div>
                <form
                    id="formkonsultasi"
                    method="post"
                    action="<?php echo base_url("umum/izinpenelitian") ?>"
                    autocomplete="off"
                    enctype="multipart/form-data">
                    <div class="form-group form-material floating" data-plugin="formMaterial">
                        <input
                            type="text"
                            class="form-control"
                            name="ip_nama"
                            data-fv-notempty="true"
                            data-fv-notempty-message="Wajib Diisi"/>
                        <label class="floating-label">Nama</label>
                    </div>
                    <div class="form-group form-material floating" data-plugin="formMaterial">
                        <input
                            type="text"
                            class="form-control"
                            name="ip_npm"
                            data-fv-notempty="true"
                            data-fv-notempty-message="Wajib Diisi"/>
                        <label class="floating-label">NPM</label>
                    </div>
                    <div class="form-group form-material floating" data-plugin="formMaterial">
                        <input
                            type="email"
                            class="form-control"
                            name="ip_email"
                            data-fv-notempty="true"
                            data-fv-notempty-message="Wajib Diisi"/>
                        <label class="floating-label">Email</label>
                    </div>

                    <div class="form-group form-material floating" data-plugin="formMaterial">
                        <input
                            type="text"
                            class="form-control"
                            name="ip_asalpt"
                            data-fv-notempty="true"
                            data-fv-notempty-message="Wajib Diisi"/>
                        <label class="floating-label">Asal Perguruan Tinggi</label>
                    </div>

                    <div class="form-group form-material floating" data-plugin="formMaterial">
                        <input
                            type="text"
                            class="form-control"
                            name="ip_fakultas"
                            data-fv-notempty="true"
                            data-fv-notempty-message="Wajib Diisi"/>
                        <label class="floating-label">Fakultas (Kosongkan Jika Tidak Ada)</label>
                    </div>

                    <div class="form-group form-material floating" data-plugin="formMaterial">
                        <input
                            type="text"
                            class="form-control"
                            name="ip_prodi"
                            data-fv-notempty="true"
                            data-fv-notempty-message="Wajib Diisi"/>
                        <label class="floating-label">Program Studi</label>
                    </div>

                    
                    
                    <div class="form-group form-material floating">
                    
                        <input type="text" class="form-control" id="ip_tglsurat" name="ip_tglsurat">
                        <label class="floating-label">Tanggal Surat Permohonan</label>
                    </div>

                    <div class="form-group form-material floating" data-plugin="formMaterial">
                        <input
                            type="text"
                            class="form-control"
                            name="ip_nosurat"
                            data-fv-notempty="true"
                            data-fv-notempty-message="Wajib Diisi"/>
                        <label class="floating-label">No Surat Permohonan</label>
                    </div>

                    <div class="form-group form-material floating" data-plugin="formMaterial">
                        <input
                            type="text"
                            class="form-control"
                            name="ip_judul"
                            data-fv-notempty="true"
                            data-fv-notempty-message="Wajib Diisi"/>
                        <label class="floating-label">Judul Penelitian</label>
                    </div>

                    <div class="form-group form-material floating" data-plugin="formMaterial">
                        <input
                            type="text"
                            class="form-control"
                            name="ip_lokasi"
                            data-fv-notempty="true"
                            data-fv-notempty-message="Wajib Diisi"/>
                        <label class="floating-label">Lokasi Penelitian</label>
                    </div>

                    <div class="form-group form-material floating">
                    
                        <input type="text" class="form-control" id="ip_mulai" name="ip_mulai">
                        <label class="floating-label">Tanggal Mulai Penelitian</label>
                    </div>

                    <div class="form-group form-material floating">
                    
                        <input type="text" class="form-control" id="ip_selesai" name="ip_selesai">
                        <label class="floating-label">Tanggal Selesai Penelitian</label>
                    </div>

                    <div class="form-group form-material floating" data-plugin="formMaterial">
                        <label class="floating-label">File Surat Permohonan</label>
                        <input type="file" id="upload_file" name="upload_file" data-plugin="dropify"/>

                    </div>

                    <div class="form-group form-material floating" data-plugin="formMaterial">

                        <input
                            type="text"
                            class="form-control"
                            name="captcha"
                            data-fv-notempty="true"
                            data-fv-notempty-message="Wajib Diisi"/>
                        <label class="floating-label">Captcha</label>

                    </div>
                    <?php echo $captcha; ?>

                    <button
                        id="validateButton"
                        type="submit"
                        class="btn btn-primary btn-block btn-lg mt-40"
                        name="submitbutton"
                        value="submit">Kirim</button>

                        <button type="button" class="btn btn-danger btn-block btn-lg mt-20" onclick="location.href='<?php echo base_url();?>/cekpermohonan'">Cek Permohonan</button>
                </form>

            </div>
        </div>

        <footer class="page-copyright page-copyright-inverse">
            <p>WEBSITE BY DINAS PENDIDIKAN PROVINSI SUMATERA SELATAN</p>
            <p>© 2021. All RIGHT RESERVED.</p>
        </footer>
    </div>
</div>
<!-- End Page -->