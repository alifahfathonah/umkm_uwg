let stok_masuk = $("#stok_masuk").DataTable({
	responsive: true,
	scrollX: true,
	ajax: readUrl,
	columnDefs: [
		{
			searcable: false,
			orderable: false,
			targets: 0,
		},
	],
	order: [[1, "desc"]],
	columns: [
		{
			data: null,
		},
		{
			data: "tanggal",
		},
		{
			data: "barcode",
		},
		{
			data: "nama_produk",
		},
		{
			data: "jumlah",
		},
		{
			data: "harga",
		},
		{
			data: "keterangan",
		},
		{
			data: "action",
		},
	],
});

function reloadTable() {
    stok_masuk.ajax.reload()
}

function checkKeterangan(obj) {
    if (obj.value == "lain") {
        $(".supplier").hide();
        $("#supplier").attr("disabled", "disabled");
        $(".lain").removeClass("d-none") 
    } else {
        $(".lain").addClass("d-none");
        $("#supplier").removeAttr("disabled");
        $(".supplier").show()
    }
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
					id: id,
				},
				success: (a) => {
					Swal.fire("Sukses", "Sukses Menghapus Data", "success");
					reloadTable();
				},
				error: (a) => {
					console.log(a);
				},
			});
		}
	});
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
		error: (err) => {
			console.log(err);
		},
	});
}

function add() {
	url = "add";
	$(".modal-title").html("Add Data");
	$('.modal button[type="submit"]').html("Add");
	$("#barcode").val("").trigger("change");
	$("#supplier").val("").trigger("change");
}

function edit(id) {
	$("#barcode").val("").trigger("change");
	$("#supplier").val("").trigger("change");
	$.ajax({
		url: getDetailUrl + "?id=" + id,
		type: "get",
		dataType: "json",
		success: (res) => {
			$('[name="id"]').val(res.id);
			$('[name="tanggal"]').val(res.tanggal);
			$('[name="barcode"]').append(
				`<option value='${res.barcode}'>${res.barcode_name}</option>`
			);
			$("#barcode").val(res.barcode).trigger("change");
            $('[name="nama_produk"]').val(res.nama_produk);
			$('[name="jumlah"]').val(res.jumlah);
			$('[name="harga"]').val(res.harga);
			$('[name="keterangan"]').val(res.keterangan);
            $('[name="supplier"]').append(
                `<option value='${res.supplier}'>${res.supplier_name}</option>`
            );
            $("#supplier").val(res.supplier).trigger("change");
                
			$(".modal").modal("show");
			$(".modal-title").html("Edit Data");
			$('.modal button[type="submit"]').html("Edit");
			url = "edit";
		},
		error: (err) => {
			console.log(err);
		},
	});
}

stok_masuk.on("order.dt search.dt", () => {
    stok_masuk.column(0, {
        search: "applied",
        order: "applied"
    }).nodes().each((el, val) => {
        el.innerHTML = val + 1
    })
});
$("#form").validate({
    errorElement: "span",
    errorPlacement: (err, el) => {
        err.addClass("invalid-feedback"), el.closest(".form-group").append(err)
    },
    submitHandler: () => {
        "edit" == url ? editData() : addData();
    }
});
$("#tanggal").datetimepicker({
    format: "dd-mm-yyyy h:ii:ss"
});
$("#barcode").select2({
    placeholder: "Barcode",
    ajax: {
        url: getBarcodeUrl,
        type: "post",
        dataType: "json",
        data: params => ({
            barcode: params.term
        }),
        processResults: res => ({
            results: res
        }),
        cache: true
    }
}).on('select2:select', function (e) {
    var data = e.params.data;
    let produk = listProduk.filter((r)=>r.id == data.id)

    if (produk.length > 0){
        $('[name="nama_produk"]').val(produk[0].nama_produk);
    }else{
        $('[name="nama_produk"]').val("");
    }
});

$("#supplier").select2({
    placeholder: "Supplier",
    ajax: {
        url: supplierSearchUrl,
        type: "post",
        dataType: "json",
        data: params => ({
            supplier: params.term
        }),
        processResults: res => ({
            results: res
        }),
        cache: true
    }
});
$(".modal").on("hidden.bs.modal", () => {
    $("#form")[0].reset();
    $("#form").validate().resetForm()
    $("#barcode").val("").trigger("change");
})
$(".modal").on("show.bs.modal", () => {
    let a = moment().format("D-MM-Y H:mm:ss");
    $("#tanggal").val(a)
});