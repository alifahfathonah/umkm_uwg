<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Produk</title>
  <link rel="stylesheet" href="<?php echo base_url('assets/vendor/adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') ?>">
  <link rel="stylesheet" href="<?php echo base_url('assets/vendor/adminlte/plugins/sweetalert2/sweetalert2.min.css') ?>">
  <link rel="stylesheet" href="<?php echo base_url('assets/vendor/adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') ?>">
  <link rel="stylesheet" href="<?php echo base_url('assets/vendor/adminlte/plugins/select2/css/select2.min.css') ?>">
  <link rel="stylesheet" href="<?php echo base_url('assets/vendor/adminlte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') ?>">
  <?php $this->load->view('partials/head'); ?>
  <style>
    .my-table{
      box-shadow: 2px 3px 10px 1px #d8d8d8;
      border-radius: 10px;
    }

    .my-table > thead > tr > th{
      border: none;
    }

    table.table-bordered.dataTable tbody th, table.table-bordered.dataTable tbody td {
      vertical-align:middle;
    }
  </style>
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
            <h1 class="m-0 text-dark">Produk - UMKM</h1>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="card">
          <div class="card-header">
            <button class="btn btn-success" data-toggle="modal" data-target="#modal" onclick="add()">Add</button>
          </div>
          <div class="card-body">
            <table class="table w-100 table-bordered table-hover" id="produk"></table>
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
    <form id="form">
      <input type="hidden" name="id">
      <div class="form-group">
        <label>Kode Item</label>
        <input type="text" class="form-control" placeholder="Barcode" name="barcode" required>
      </div>
      <div class="form-group">
        <label>Nama Barang</label>
        <input type="text" class="form-control" placeholder="Nama" name="nama_produk" required>
      </div>
      <div class="form-group">
        <label>Satuan</label>
        <select name="satuan" id="satuan" class="form-control select2" required></select>
      </div>
      <div class="form-group">
        <label>Kategori</label>
        <select name="kategori" id="kategori" class="form-control select2" required></select>
      </div>
      <div class="form-group my-4">
        <table class="table my-table">
          <thead>
            <tr>
              <th class="text-center" width="35%">Pelanggan</th>
              <th class="text-center">Harga</th>
              <th class="text-center">Dikon(%)</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($tipe_pelanggan as $key => $value): ?>
            <tr>
              <td>
                <?=$value->nama ?>
                <input type="hidden" class="form-control" name="id_<?=strtolower($value->nama) ?>" value="">
              </td>
              <td>
                <input type="text" class="form-control" name="harga_<?=strtolower($value->nama) ?>" value="">
              </td>
              <td>
                <input type="text" class="form-control" name="diskon_<?=strtolower($value->nama) ?>" value="">
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="form-group">
        <label>Stok</label>
        <input type="text" class="form-control" placeholder="Stok" name="stok" value="0" readonly>
      </div>
      <button class="btn btn-success" type="submit">Add</button>
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
<script src="<?php echo base_url('assets/vendor/adminlte/plugins/select2/js/select2.min.js') ?>"></script>
<script>
  var readUrl = '<?php echo site_url('produk/read') ?>';
  var addUrl = '<?php echo site_url('produk/add') ?>';
  var deleteUrl = '<?php echo site_url('produk/delete') ?>';
  var editUrl = '<?php echo site_url('produk/edit') ?>';
  var getProdukUrl = '<?php echo site_url('produk/get_produk') ?>';
  var kategoriSearchUrl = '<?php echo site_url('kategori_produk/search') ?>';
  var satuanSearchUrl = '<?php echo site_url('satuan_produk/search') ?>';
  var listPelanggan = <?php echo json_encode($tipe_pelanggan); ?>;
</script>
<script src="//cdn.rawgit.com/ashl1/datatables-rowsgroup/v1.0.0/dataTables.rowsGroup.js"></script>
<script src="<?php echo base_url('assets/js/unminify/produk.js') ?>"></script>
</body>
</html>
