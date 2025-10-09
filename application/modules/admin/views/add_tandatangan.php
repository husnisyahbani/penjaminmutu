<!-- Page -->
<div class="page">
    <div class="page-content container-fluid">

        <div class="row">
            <div class="col-lg-12">

                <div class="panel">
                    <div class="panel-heading">
                        <h3 class="panel-title">Taanda Tangan
                        </h3>
                    </div>
                    <div class="panel-body">
                        <form
                            class="form-horizontal"
                            id="formadd"
                            autocomplete="off"
                            action="<?php echo base_url() . 'admin/tandatangan/tambah';?>"
                            method="post"
                            enctype="multipart/form-data">

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Gambar Tanda Tangan</label>
                                <div class="col-md-9">
                                    <input
                                        type="file"
                                        id="input-file-now"
                                        name="upload_file"
                                        data-plugin="dropify"
                                        data-default-file="<?php if (isset($result[0]['tt_file'])) {echo base_url ().$result[0]['tt_file'];} ?>"/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">QR Code</label>
                                <div class="col-md-9">
                                    <input
                                        type="file"
                                        id="input-file-now"
                                        name="upload_qr"
                                        data-plugin="dropify"
                                        data-default-file="<?php if (isset($result[0]['tt_qr'])) {echo base_url ().$result[0]['tt_qr'];} ?>"/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Nama</label>
                                <div class="col-md-9">
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="tt_nama"
                                        value="<?php if (isset($result[0]['tt_nama'])) echo $result[0]['tt_nama']; ?>"/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Jabatan</label>
                                <div class="col-md-9">
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="tt_jabatan"
                                        value="<?php if (isset($result[0]['tt_jabatan'])) echo $result[0]['tt_jabatan']; ?>"/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">Pangkat</label>
                                <div class="col-md-9">
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="tt_pangkat"
                                        value="<?php if (isset($result[0]['tt_pangkat'])) echo $result[0]['tt_pangkat']; ?>"/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">NIP</label>
                                <div class="col-md-9">
                                    <input
                                        type="number"
                                        class="form-control"
                                        name="tt_nip"
                                        value="<?php if (isset($result[0]['tt_nip'])) echo $result[0]['tt_nip']; ?>"/>
                                </div>
                            </div>

                            

                            <div class="text-right">
                                <button
                                    type="submit"
                                    class="btn btn-primary"
                                    id="validateButton"
                                    name="submitdata">Submit</button>
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