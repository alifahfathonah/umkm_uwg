<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Utang Piutang</title>
  <link rel="stylesheet" href="<?php echo base_url('assets/vendor/adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') ?>">
  <link rel="stylesheet" href="<?php echo base_url('assets/vendor/adminlte/plugins/sweetalert2/sweetalert2.min.css') ?>">
  <link rel="stylesheet" href="<?php echo base_url('assets/vendor/adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') ?>">
  <?php $this->load->view('partials/head'); ?>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <?php $this->load->view('includes/nav'); ?>

  <?php $this->load->view('includes/aside'); ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col">
            <h1 class="m-0 text-dark">Utang Piutang UMKM</h1>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="card">
          <div class="card-body">
            <table class="table w-100 table-bordered table-hover" id="cicilan">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Nota</th>
                  <th>Total Bayar</th>
                  <th>Sisa Kekurangan</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

</div>

<div class="modal fade" id="modal">
<div class="modal-dialog">
<div class="modal-content">
  <div class="modal-header">
    <h5 class="modal-title">Add Data</h5>
    <button class="close" data-dismiss="modal">
      <span>&times;</span>
    </button>
  </div>
  <div class="modal-body">
    <form id="form" class="was-validated">
      <input type="hidden" name="id">
      <div class="form-group">
        <label>Nota</label>
        <input type="text" class="form-control" placeholder="Nota" name="nota" disabled="true">
      </div>
      <div class="form-group">
        <label>Kekurangan</label>
        <input type="text" class="form-control" placeholder="Kekurangan" name="kekurangan" disabled="true">
      </div>
      <div class="form-group">
        <label>Status</label>
        <input type="text" class="form-control" placeholder="Status" name="status" disabled="true">
      </div>
      <div class="form-group mt-4 mb-5">
        <div class="my-2" style="
          display: flex;
          align-items: center;
          justify-content: space-between;
        ">
          <label>Daftar Cicilan</label>
          <button class="btn btn-default" id="btn-add-cicilan" type="button" onclick="newCicilan()"><i class="fa fa-plus"></i> Transaksi</button>
        </div>
        <table id="tbl_cicilan" class="table my-table">
          <thead>
            <tr>
              <th class="text-center" width="40%">Tanggal</th>
              <th class="text-center" width="30%">Transaksi Terakhir</th>
              <th class="text-center" width="30%">Sisa Pembayaran</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
      <button class="btn btn-success" id="btn-save-cicilan" type="submit">Simpan</button>
      <button class="btn btn-danger" data-dismiss="modal">Close</button>
    </form>
  </div>
</div>
</div>
</div>
<!-- ./wrapper -->
<?php $this->load->view('includes/footer'); ?>
<?php $this->load->view('partials/footer'); ?>
<script src="<?php echo base_url('assets/vendor/adminlte/plugins/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?php echo base_url('assets/vendor/adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') ?>"></script>
<script src="<?php echo base_url('assets/vendor/adminlte/plugins/jquery-validation/jquery.validate.min.js') ?>"></script>
<script src="<?php echo base_url('assets/vendor/adminlte/plugins/sweetalert2/sweetalert2.min.js') ?>"></script>
<script>
  var readUrl = '<?php echo site_url('cicilan/read') ?>';
  var addUrl = '<?php echo site_url('cicilan/add') ?>';
  var removeUrl = '<?php echo site_url('cicilan/delete') ?>';
  var editUrl = '<?php echo site_url('cicilan/edit') ?>';
  var get_cicilanUrl = '<?php echo site_url('cicilan/get_cicilan') ?>';

  let cicilan_now = [];
</script>
<script src="<?php echo base_url('assets/js/unminify/cicilan.js') ?>"></script>
</body>
</html>
