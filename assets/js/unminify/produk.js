let url;
let produk = $("#produk").DataTable({
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
    columns: [
        { name: null, title: "No", data: null }, 
        { name: "barcode", title: "Kode Item", data: "barcode" },
        { name: "nama", title: "Nama Barang", data: "nama" },
        { name: "satuan", title: "Satuan", data: "satuan" },
        { name: "kategori", title: "Kategori", data: "kategori" },
        { name: "pelanggan", title: "Pelanggan", data: "pelanggan" },
        { name: "harga", title: "Harga", data: "harga" },
        { name: "diskon", title: "Diskon(%)", data: "diskon" },
        { name: "stok", title: "Stok", data: "stok" },
        { name: "action", title: "Actions", data: "action" }
    ],
    rowsGroup: [
        'barcode:name',
        'nama:name',
        'satuan:name',
        'kategori:name',
        'stok:name',
        'action:name',
    ],
});

function reloadTable() {
    produk.ajax.reload()
}

function addData() {
    $.ajax({
        url: addUrl,
        type: "post",
        dataType: "json",
        data: $("#form").serialize(),
        success: res => {
            $(".modal").modal("hide");
            Swal.fire("Sukses", "Sukses Menambahkan Data", "success");
            reloadTable();
        },
        error: res => {
            console.log(res);
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
                    reloadTable();
                },
                error: () => {
                    console.log(a);
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
            reloadTable();
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
        url: getProdukUrl+"?id="+id,
        type: "get",
        dataType: "json",
        success: res => {
            $('[name="id"]').val(res.id);
            $('[name="barcode"]').val(res.barcode);
            $('[name="nama_produk"]').val(res.nama_produk);
            $('[name="satuan"]').append(`<option value='${res.satuan_id}'>${res.satuan}</option>`);
            $('[name="satuan"]').val((res.satuan_id != null)? res.satuan_id : "").trigger('change');
            $('[name="kategori"]').append(`<option value='${res.kategori_id}'>${res.kategori}</option>`);
            $('[name="kategori"]').val((res.kategori_id != null)? res.kategori_id : "").trigger('change');
            $('[name="harga"]').val(res.harga);
            $('[name="stok"]').val(res.stok);

            if (res.pelanggan.length > 0) {
                res.pelanggan.forEach(r => {
                    $(`[name="id_${r.pelanggan.toLowerCase()}"]`).val(r.id);
                    $(`[name="harga_${r.pelanggan.toLowerCase()}"]`).val(r.harga);
                    $(`[name="diskon_${r.pelanggan.toLowerCase()}"]`).val(r.diskon);
                });
            }

            $(".modal").modal("show");
            $(".modal-title").html("Edit Data");
            $('.modal button[type="submit"]').html("Edit");
            url = "edit";
        },
        error: err => {
            console.log(err)
        }
    });
}
produk.on("order.dt search.dt", () => {
    produk.column(0, {
        search: "applied",
        order: "applied"
    }).nodes().each((el, val) => {
        el.innerHTML = val + 1
    });
});
$("#form").validate({
    errorElement: "span",
    errorPlacement: (err, el) => {
        err.addClass("invalid-feedback");
        el.closest(".form-group").append(err)
    },
    submitHandler: () => {
        "edit" == url ? editData() : addData()
    }
});
$("#kategori").select2({
    placeholder: "Kategori",
    ajax: {
        url: kategoriSearchUrl,
        type: "post",
        dataType: "json",
        data: params => ({
            kategori: params.term
        }),
        processResults: data => ({
            results: data
        }),
        cache: true
    }
});
$("#satuan").select2({
    placeholder: "Satuan",
    ajax: {
        url: satuanSearchUrl,
        type: "post",
        dataType: "json",
        data: paras => ({
            satuan: paras.term
        }),
        processResults: data => ({
            results: data
        }),
        cache: true
    }
});
$(".modal").on("hidden.bs.modal", () => {
    $("#form")[0].reset();
    $("#form").validate().resetForm();
});