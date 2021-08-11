let url, cicilan = $("#cicilan").DataTable({
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
        data: "nota"
    }, {
        data: "total_bayar"
    }, {
        data: "hutang"
    }, {
        data: "status"
    }, {
        data: "action"
    }]
});

function reloadTable() {
    cicilan.ajax.reload();
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
            reloadTable();
        },
        error: err => {
            console.log(err);
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
                url: removeUrl,
                type: "post",
                dataType: "json",
                data: {
                    id: id
                },
                success: () => {
                    Swal.fire("Sukses", "Sukses Menghapus Data", "success");
                    reloadTable();
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
            Swal.fire("Sukses", "Sukses Mengedit Data", "success");
            reloadTable()
        },
        error: err => {
            console.log(err)
        }
    })
}

function add() {
    url = "add";
    $(".modal-title").html("Add Data");
    $('.modal button[type="submit"]').html("Add")
}

function edit(id) {
    $.ajax({
        url: get_cicilanUrl+"?id="+id,
        type: "get",
        dataType: "json",
        success: res => {
            $('[name="id"]').val(res.id);
            $('[name="nota"]').val(res.nota);
            $('[name="kekurangan"]').val(res.hutang);
            $('[name="status"]').val(res.status);

            if(res.cicilan.length > 0) {
                // $("#tbl_cicilan")
            }else{
                $("#tbl_cicilan tbody").append(`
                    <tr>
                        <td align="center">2021-01-01</td>
                        <td align="center">5000</td>
                        <td align="center">34000</td>
                    </tr>
                `);
            }

            $(".modal").modal("show");
            $(".modal-title").html("Detail Pembayaran");
            $('.modal button[type="submit"]').html("Simpan");
            url = "edit"
        },
        error: err => {
            console.log(err)
        }
    })
}
cicilan.on("order.dt search.dt", () => {
    cicilan.column(0, {
        search: "applied",
        order: "applied"
    }).nodes().each((el, val) => {
        el.innerHTML = val + 1
    })
});
$("#form").validate({
    errorElement: "span",
    errorPlacement: (err, el) => {
        err.addClass("invalid-feedback");
        el.closest(".form-group").append(a)
    },
    submitHandler: () => {
        "edit" == url ? editData() : addData()
    }
});
$(".modal").on("hidden.bs.modal", () => {
    $("#form")[0].reset();
    $("#form").validate().resetForm()
});