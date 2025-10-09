
    

    <!-- Page -->
    <div class="page">
        
      <div class="page-header">
        
        <div class="page-header-actions">
          <button type="button" class="btn btn-sm btn-icon btn-success" data-toggle="tooltip"
            data-original-title="Download" id="downloadpegawai">
            <i class="icon md-download" aria-hidden="true"></i>Download
          </button>
          <button type="button" class="btn btn-sm btn-icon btn-success" data-toggle="tooltip"
            data-original-title="Tambah" id="tambahpegawai">
            <i class="icon md-plus" aria-hidden="true"></i>Tambah
          </button>
          <button type="button" class="btn btn-sm btn-icon btn-danger" data-toggle="tooltip"
            data-original-title="Import" id="importpegawai">
            <i class="icon md-upload" aria-hidden="true"></i>Import
          </button>
          
        </div>
      </div>
        
      <div class="page-content container-fluid">
       

        <div class="panel">
            <header class="panel-heading">
                <h3 class="panel-title">Pegawai</h3> 
                <div class="panel-actions panel-actions-keep">
<!--                    <button type="button" id="tambahsaksi" class="btn btn-block btn-success"><i class="icon md-plus" aria-hidden="true"></i>Tambah</button>-->
                </div>
            </header>
            <div class="panel-body">
                <table class="table table-hover dataTable table-striped w-full" id="pegawai">
                    <thead>
                        <tr>
                            <th width="20px">No</th>
                            <th width="120px">Foto</th>
                            <th>Nama</th>
                            <th>Alamat</th>
                            <th>No. Hape</th>
                            <th>NIP</th>
                            <th>Golongan</th>
                            <th>Email</th>
                            <th width="100px">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
          
          
    
    </div>
    </div>
    <!-- End Page -->
    
<div class="modal fade" id="PhotoModal" aria-hidden="false" aria-labelledby="exampleFormModalLabel"
     role="dialog" tabindex="-1">
    <div class="modal-dialog modal-simple">
        <form class="modal-content" >
        <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="exampleFormModalLabel">Photo</h4>
        </div>
            <div class="modal-body">
                
                    <div class="row">
                        <div class="col-md-12 center">
                            <img class="img-fluid" id="gambar" src="" style="max-height:600px;"/>
                        </div>
                    </div>
                        
            </div>
            </form>
    </div>
        
</div>


    
