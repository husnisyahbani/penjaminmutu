<!-- Page -->
<div
    class="page vertical-align text-center"
    data-animsition-in="fade-in"
    data-animsition-out="fade-out">
    <div class="page-content vertical-align-middle">
        <div class="panel">
            <div class="panel-body">
                <div class="brand">
                    <img
                        class="brand-img"
                        src="<?php echo asset_url();?>/assets/images/logorsud.png"
                        alt="DINAS PENDIDIKAN SUMSEL"
                        width="60px">
                    <h2 class="brand-text font-size-18">No. Antrian</h2>
                </div>
                <form
                    id="formcekpermohonan"
                    method="post"
                    action="<?php echo base_url("cekpermohonan") ?>"
                    autocomplete="off">

                    <div class="form-group form-material floating" data-plugin="formMaterial">
                        <input type="number" class="form-control" name="no_test" id="no_test" data-fv-notempty="true"
                            data-fv-notempty-message="Wajib Diisi"/>
                        <label class="floating-label">No Permohonan</label>
                    </div>

                    <button
                        type="submit"
                        class="btn btn-primary btn-block btn-lg mt-40"
                        name="submitcek"
                        id="validateButton"
                        value="submitcek">Cari</button>
                    <button
                        type="button"
                        class="btn btn-danger btn-block btn-lg mt-20"
                        onclick="location.href='<?php echo base_url();?>'">Homepage</button>
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