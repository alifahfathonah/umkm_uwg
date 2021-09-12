let url, pengguna = $("#pengguna").DataTable({
    responsive: true,
    scrollX: true,
    ajax: readUrl,
    columnDefs: [{
        searcable: false,
        orderable: false,
        targets: 0
    }],
    // order: [
    //     [1, "asc"]
    // ],
    columns: [{
        data: null
    }, {
        data: "username"
    }, {
        data: "nama"
    }, {
        data: "role"
    }, {
        data: "toko"
    }, {
        data: "action"
    }]
});

function reloadTable() {
    pengguna.ajax.reload()
}

function addData() {
    $.ajax({
        url: addUrl,
        type: "post",
        dataType: "json",
        data: $("#form").serialize(),
        success: (res) => {
            if(res.success){
                $(".modal").modal("hide");
                Swal.fire("Sukses", res.message, "success"), reloadTable()
            }else{
                Swal.fire("Gagal", res.message, "warning")
            }
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
                success: (res) => {
                    if(res.success){
                        Swal.fire("Sukses", res.message, "success"), reloadTable()
                    }else{
                        Swal.fire("Gagal", res.message, "warning")
                    }
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
        success: (res) => {
            if(res.success){
                $(".modal").modal("hide");
                Swal.fire("Sukses", res.message, "success"), reloadTable()
            }else{
                Swal.fire("Gagal", res.message, "warning")
            }
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

    $('[name="toko"]').append(`<option value='${toko.id}'>${toko.nama}</option>`);
    $('[name="toko"]').val((toko.id != null)? toko.id : "").trigger('change');
}

function edit(id) {
    $.ajax({
        url: getPenggunaUrl,
        type: "post",
        dataType: "json",
        data: {
            id: id
        },
        success: res => {
            $('[name="id"]').val(res.id);
            $('[name="username"]').val(res.username);
            $('[name="nama"]').val(res.nama);
            $('[name="role"]').append(`<option value='${res.role_id}'>${res.role}</option>`);
            $('[name="role"]').val((res.role_id != null)? res.role_id : "").trigger('change');
            
            if(res.role_id != 1){
                enableToko();
                $('[name="toko"]').append(`<option value='${res.toko_id}'>${res.toko}</option>`);
                $('[name="toko"]').val(res.toko_id).trigger('change');
            }else{
                disableToko();
            }
            
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

pengguna.on("order.dt search.dt", () => {
    pengguna.column(0, {
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

function enableToko(){
    $("#toko").select2({
        placeholder: "Toko",
        disabled: false,
        ajax: {
            url: tokoSearchUrl,
            type: "post",
            dataType: "json",
            data: params => ({
                toko: params.term
            }),
            processResults: data => ({
                results: data
            }),
            cache: true
        }
    });
}

function disableToko(){
    $("#toko").select2({
        placeholder: "Toko",
        disabled: true,
    });
    $('[name="toko"]').val("").trigger('change');
}

disableToko();
$("#role").select2({
    placeholder: "Role",
    readonly: false,
    ajax: {
        url: roleSearchUrl,
        type: "post",
        dataType: "json",
        data: params => ({
            role: params.term
        }),
        processResults: data => ({
            results: data
        }),
        cache: true
    }
}).on('select2:select', function (e) {
    var data = e.params.data;
    if(data.id != 1){
        enableToko();        
    }else{
        disableToko();        
    }
});

// $('#mySelect2').on('select2:select', function (e) {
//     var data = e.params.data;
//     console.log(data);
// });