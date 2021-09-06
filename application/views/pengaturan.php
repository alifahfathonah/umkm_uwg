<?php 
$role = $this->session->userdata('role'); 
?>
<!DOCTYPE html>
<html>
<head>
  <title>Pengaturan</title>
  <link rel="stylesheet" href="<?php echo base_url('assets/vendor/adminlte/plugins/sweetalert2/sweetalert2.min.css') ?>">
  <link rel="stylesheet" href="<?php echo base_url('assets/vendor/adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') ?>">
  <link rel="stylesheet" href="<?php echo base_url('assets/vendor/adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') ?>">
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
            <h1 class="m-0 text-dark">Pengaturan</h1>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>


    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="card">
          <?php if($role == 1): ?>
            <div class="card-header">
              <button class="btn btn-success" data-toggle="modal" data-target="#modal" onclick="add()">Add</button>
            </div>
          <?php endif; ?>
          <div class="card-body">
          <?php if($role != 1): ?>
            <form id="toko">
              <div class="form-row">
                <div class="col-6">
                  <div class="form-group">
                    <label>Nama Toko</label>
                    <input type="text" class="form-control" placeholder="Nama Toko" name="nama" value="<?php echo $toko->nama ?>" required>
                  </div>
                  <div class="form-group">
                    <label>Alamat</label>
                    <textarea name="alamat" placeholder="Alamat" class="form-control" required><?php echo $toko->alamat ?></textarea>
                  </div>
                  <div class="form-group">
                    <button class="btn btn-success" type="submit">Simpan</button>
                  </div>
                </div>
              </div>
            </form>
          <?php else: ?>
            <table class="table w-100 table-bordered table-hover" id="toko_table">
              <thead>
                <tr>
                  <th width="1%">No</th>
                  <th>Nama</th>
                  <th>Alamat</th>
                  <th>Action</th>
                </tr>
              </thead>
            </table>
          <?php endif; ?>
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
            <label>Nama Toko</label>
            <input type="text" class="form-control" placeholder="Nama Toko" name="nama" required>
          </div>
          <div class="form-group">
            <label>Alamat</label>
            <textarea name="alamat" placeholder="Alamat" class="form-control"></textarea>
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
<script>
  var readUrl = '<?php echo site_url('pengaturan/read') ?>';
  var detailUrl = '<?php echo site_url('pengaturan/detail') ?>';
  var editUrl = '<?php echo site_url('pengaturan/edit') ?>';
  var addUrl = '<?php echo site_url('pengaturan/add') ?>';
  var deleteUrl = '<?php echo site_url('pengaturan/delete') ?>';
  
  let toko = $("#toko_table").DataTable({
      responsive: true,
      scrollX: true,
      ajax: readUrl,
      columnDefs: [{
          searcable: false,
          orderable: false,
          targets: 0
      }],
      order: [
          [1, "asc"]
      ],
      columns: [{
          data: null
      }, {
          data: "nama"
      }, {
          data: "alamat"
      }, {
          data: "action"
      }]
  });

  function reloadTable() {
    toko.ajax.reload()
  }

  function addData() {
    $.ajax({
        url: addUrl,
        type: "post",
        dataType: "json",
        data: $("#form").serialize(),
        success: () => {
            $(".modal").modal("hide");
            Swal.fire("Sukses", "Sukses Menambahkan Data", "success");
            reloadTable()
        },
        error: err => {
            console.log(err)
        }
    })
  }

  function remove(id) {
      Swal.fire({
          title: "Hapus",
          text: "Hapus data ini?",
          type: "warning",
          showDenyButton: true,
          showCancelButton: true,
          confirmButtonText: `Yes`,
          denyButtonText: `No`,
      }).then((result) => {
          if (result.value) {
              $.ajax({
                  url: deleteUrl,
                  type: "post",
                  dataType: "json",
                  data: {
                      id: id
                  },
                  success: () => {
                      Swal.fire("Sukses", "Sukses Menghapus Data", "success");
                      reloadTable()
                  },
                  error: err => {
                      console.log(err)
                  }
              })
          }
      })
  }

  function editData() {
      $.ajax({
          url: editUrl,
          type: "post",
          dataType: "json",
          data: $("#form").serialize(),
          success: () => {
              $(".modal").modal("hide");
              Swal.fire("Sukses", "Sukses Mengedit Data", "success"), reloadTable()
          },
          error: err => {
              console.log(err)
          }
      })
  }

  function add() {
      url = "add";
      $(".modal-title").html("Add Data");
      $('.modal button[type="submit"]').html("Add");
  }

  function edit(id) {
      $.ajax({
          url: detailUrl,
          type: "post",
          dataType: "json",
          data: {
              id: id
          },
          success: res => {
              $('[name="id"]').val(res.id);
              $('[name="nama"]').val(res.nama);
              $('[name="alamat"]').val(res.alamat);
              $(".modal").modal("show");
              $(".modal-title").html("Edit Data");
              $('.modal button[type="submit"]').html("Edit");
              url = "edit"
          },
          error: err => {
              console.log(err)
          }
      })
  }
  toko.on("order.dt search.dt", () => {
      toko.column(0, {
          search: "applied",
          order: "applied"
      }).nodes().each((el, err) => {
          el.innerHTML = err + 1
      })
  });

  $("#form").validate({
    errorElement: "span",
    errorPlacement: (err, el) => {
        err.addClass("invalid-feedback"), el.closest(".form-group").append(err)
    },
    submitHandler: () => {
        "edit" == url ? editData() : addData()
    }
  });

  $(".modal").on("hidden.bs.modal", () => {
      $("#form")[0].reset();
      $("#form").validate().resetForm();
      $('[name="role"]').val("").trigger("change");
      $('[name="toko"]').val("").trigger("change");
  });

  $('#toko').validate({
    errorElement: 'span',
    errorPlacement: (error, element) => {
      error.addClass('invalid-feedback')
      element.closest('.form-group').append(error)
    },
    submitHandler: () => {
      $.ajax({
        url: '<?php echo site_url('pengaturan/set_toko') ?>',
        type: 'post',
        dataType: 'json',
        data: $('#toko').serialize(),
        success: res => {
          Swal.fire('Sukses', 'Sukses Mengedit', 'success').then(() => window.location.reload())
        }
      })
    }
  })
</script>
</body>
</html>
