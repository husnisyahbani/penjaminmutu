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
                <p class="font-size-60"><?php if(isset($hasil)) {echo $hasil;}else{ echo "TIDAK DITEMUKAN";}?></p>
                <?php if(isset($hasil) && $hasil === "DITERIMA"){?>
                <button type="button" class="btn btn-success btn-block btn-lg mt-20" onclick="location.href='<?php echo base_url();?>umum/hasil/download?id=<?php echo $this->input->get('id');?>'">Download</button>
                <?php }?>
                <button type="button" class="btn btn-primary btn-block btn-lg mt-20" onclick="location.href='<?php echo base_url();?>cekpermohonan'">Cek Status Permohonan</button>
                <button type="button" class="btn btn-danger btn-block btn-lg mt-20" onclick="location.href='<?php echo base_url();?>'">Homepage</button>
            </div>
        </div>

        <footer class="page-copyright page-copyright-inverse">
            <p>WEBSITE BY DINAS PENDIDIKAN PROVINSI SUMATERA SELATAN</p>
            <p>© 2021. All RIGHT RESERVED.</p>
        </footer>
    </div>
</div>
<!-- End Page -->