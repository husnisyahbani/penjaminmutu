


<!-- Page -->
<div class="page">
    <div class="page-content container-fluid">

        <div class="row">
            <div class="col-lg-12">

                <div class="panel">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?=$title?></h3>
                    </div>
                    <div class="panel-body">
                        <form class="form-horizontal" id="formeditizin" autocomplete="off" action="<?php if(isset($result[0]['id_ip'])) echo base_url() . 'admin/izinpenelitian/detail?id='.$result[0]['id_ip'];?>" method="post" enctype="multipart/form-data">

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Dokument</label>
                                <div class="col-md-9">
                                <a type="button" class="btn btn-success" href="<?php if (isset($result[0]['ip_file']))  echo base_url('filedata/').$result[0]['ip_file']; ?>" target='_blank'>Download</a>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Nama</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="ip_nama" value="<?php if (isset($result[0]['ip_nama'])) echo $result[0]['ip_nama']; ?>" />
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">NPM</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="ip_npm" value="<?php if (isset($result[0]['ip_npm'])) echo $result[0]['ip_npm']; ?>" />
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Email</label>
                                <div class="col-md-9">
                                    <input type="email" class="form-control" name="ip_email" value="<?php if (isset($result[0]['ip_email'])) echo $result[0]['ip_email']; ?>" />
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Perguruan Tinggi Asal</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="ip_asalpt" value="<?php if (isset($result[0]['ip_asalpt'])) echo $result[0]['ip_asalpt']; ?>" />
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Fakultas</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="ip_fakultas" value="<?php if (isset($result[0]['ip_fakultas'])) echo $result[0]['ip_fakultas']; ?>" />
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Jurusan</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="ip_prodi" value="<?php if (isset($result[0]['ip_prodi'])) echo $result[0]['ip_prodi']; ?>" />
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Nomor Surat Dari Perguruan Tinggi</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="ip_nosurat" value="<?php if (isset($result[0]['ip_nosurat'])) echo $result[0]['ip_nosurat']; ?>" />
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Judul Penelitian</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="ip_judul" value="<?php if (isset($result[0]['ip_judul'])) echo $result[0]['ip_judul']; ?>" />
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Lokasi Penelitian</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="ip_lokasi" value="<?php if (isset($result[0]['ip_lokasi'])) echo $result[0]['ip_lokasi']; ?>" />
                                </div>
                            </div>


                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Status</label>
                                <div class="col-md-9">
                                <select name="ip_status" id="ip_status" class="form-control">
                                    <option value="VERIFIKASI" <?php if (isset($result[0]['ip_status']) && $result[0]['ip_status']=="VERIFIKASI") echo "selected"; ?>>VERIFIKASI</option>
                                    <option value="DITERIMA" <?php if (isset($result[0]['ip_status']) && $result[0]['ip_status']=="DITERIMA") echo "selected"; ?>>DITERIMA</option>
                                    <option value="DITOLAK" <?php if (isset($result[0]['ip_status']) && $result[0]['ip_status']=="DITOLAK") echo "selected"; ?>>DITOLAK</option>
                                </select>
                                </div>
                            </div>

                            


                            

                            


                            <div class="text-right">
                                <?php if (isset($result[0]['ip_status']) && $result[0]['ip_status']!="DITERIMA"){?>
                                <button type="submit" class="btn btn-success" id="validateButton" name="submitizin">Simpan</button>
                                <?php } ?>
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






