let isCetak = false,
    produk = [],
    transaksi = $("#transaksi").DataTable({
        responsive: true,
        lengthChange: false,
        searching: false,
        scrollX: true
    });

function reloadTable() {
    transaksi.ajax.reload()
}

function nota(jumlah) {
    let hasil = "",
        char = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789",
        total = char.length;
    for (var r = 0; r < jumlah; r++) hasil += char.charAt(Math.floor(Math.random() * total));
    return hasil
}

function setProduk() {
    if ($("#produk").val() != null) {
        $.ajax({
            url: produkUrl+"?id=" + $("#produk").val(),
            type: "get",
            dataType: "json",
            success: res => {
                product_now = res;
                $("#barcode").val(product_now.barcode);
                $("#satuan").val(product_now.satuan);

                if (product_cart.length > 0) {
                    let check_product = product_cart.filter(r => r.id == product_now.id);
                    if (check_product.length > 0) {
                        product_now.stok = check_product[0].stok;
                    }else{
                        product_now.stok = res.stok;
                    }
                }else{
                    product_now.stok = res.stok;
                }

                $("#sisa").html(`Sisa ${product_now.stok}`);
                
                if (product_now.pelanggan.length > 0) {
                    tipe_pelanggan = product_now.pelanggan.filter(r => r.pelanggan == $("#tipe_pelanggan").val())
                    product_now.harga = tipe_pelanggan[0].harga;
                    product_now.diskon = tipe_pelanggan[0].diskon;
                    $("#harga").html(`RP${tipe_pelanggan[0].harga}`);
                }
    
                $("#produk_detail").fadeIn();
                checkEmpty()
            },
            error: err => {
                console.log(err)
            }
        })
    }
}

function setPelanggan(element) {
    $.ajax({
        url: pelangganUrl+"?id=" + $("#pelanggan").val(),
        type: "get",
        dataType: "json",
        success: res => {
            // Reset data if change customer
            if (pelanggan_now.id != res.id) {
                if(product_cart.length > 0){
                    Swal.fire({
                        title: "Reset Keranjang",
                        text: 'Mengubah pelanggan akan menghapus semua data yang ada di keranjang, apakah anda yakin?',
                        type: "warning",
                        showDenyButton: true,
                        showCancelButton: true,
                        confirmButtonText: `Yes`,
                        denyButtonText: `No`,
                      }).then((result) => {
                          if (result.value) {
                            pelanggan_now = res;
                            $("#tipe_pelanggan").val(res.tipe);
                            $("#produk_detail").hide();
                            $("#produk").val("").trigger("change");
                            $("#barcode").val("");
                            $("#satuan").val("");
                            if(res.tipe == null || res.tipe == ""){
                                $("#produk").attr("disabled", true);
                            }else{
                                $("#produk").attr("disabled", false);
                            }
                            $("#total").html("0");
                            $("#jumlah").val("");
                            $("#tambah").attr("disabled", true);
                            $("#bayar").attr("disabled", true);
    
                            product_cart = [];
                            product_now = {};
                            transaksi.clear().draw();
                        } else {
                            $("#pelanggan").val(pelanggan_now.id).trigger("change");
                        }
                    });
                }else{
                    pelanggan_now = res;
                    $("#tipe_pelanggan").val(res.tipe);
                    $("#produk_detail").hide();
                    $("#produk").val("").trigger("change");
                    $("#barcode").val("");
                    $("#satuan").val("");
                    if(res.tipe == null || res.tipe == ""){
                        $("#produk").attr("disabled", true);
                    }else{
                        $("#produk").attr("disabled", false);
                    }
                    $("#total").html("0");
                    $("#jumlah").val("");
                    $("#tambah").attr("disabled", true);
                    $("#bayar").attr("disabled", true);
                }
            }
            checkEmpty()
        },
        error: err => {
            console.log(err)
        }
    })
}

function checkStok() {
    let jumlah = parseFloat($('#jumlah').val());

    if(parseInt(product_now.stok) >= parseInt(jumlah)) {
        if (product_cart.length > 0) {
            let found = false;
            product_cart.forEach((r,i) => {
                if(r.id == product_now.id){
                    product_cart[i].stok = product_cart[i].stok - jumlah;
                    product_cart[i].jumlah = product_cart[i].jumlah + jumlah;
                    product_now = product_cart[i];
                    found = true;
                }
            });

            if (!found) {
                product_now.stok = product_now.stok - jumlah;
                product_now.jumlah = jumlah;
                product_cart.push(product_now);
            }
        }else{
            product_now.stok = product_now.stok - jumlah
            product_now.jumlah = jumlah;
            product_cart.push(product_now);
        }
    
        let total = 0;
        let sub_total = 0;
        let sub_total_ori = 0;
        transaksi.clear().draw();
        product_cart.forEach((r,i)=> {
            sub_total = (parseFloat(r.harga) - parseFloat((r.harga * r.diskon)/100)) * parseInt(r.jumlah);        
            sub_total_ori = parseFloat(r.harga) * parseInt(r.jumlah);        
            total += sub_total
    
            transaksi.row.add([
            r.barcode,
            r.nama_produk,
            r.harga,
            (parseFloat(r.diskon)>0)?`<span class="label-diskon">${r.diskon+"%"}</span>`: '',
            r.jumlah+" "+r.satuan,
            (parseFloat(r.diskon)>0)?sub_total+`<span class="label-diskon-subtotal">${sub_total_ori}</span>`:sub_total,
            `<button name="${r.id}" class="btn btn-sm btn-danger" onclick="remove('${r.id}')">Hapus</btn>`]).draw();
        });   
        transaksi.columns.adjust().draw(); // Redraw the DataTable
        $("#sisa").html(`Sisa ${product_now.stok}`);
        $("#total").html(total);
        $("#jumlah").val("");
        $("#tambah").attr("disabled", "disabled");
        $("#bayar").removeAttr("disabled")
    }else{
        Swal.fire("Gagal", "Stok Tidak Cukup", "warning");
    }
}

function bayarCetak() {
    isCetak = true
}

function bayar() {
    isCetak = false
}

function checkEmpty() {
    let barcode = $("#barcode").val(),
        jumlah = $("#jumlah").val();
    if (barcode !== "" && jumlah !== "" && parseInt(jumlah) >= 1) {
        $("#tambah").removeAttr("disabled")    
    } else {
        $("#tambah").attr("disabled", "disabled")
    }
}

function checkUang() {
    let jumlah_uang = $('[name="jumlah_uang"').val(),
        total_bayar = parseInt($(".total_bayar").html());

    if (jumlah_uang.length > 0) {
        $("#add").removeAttr("disabled");
        $("#cetak").removeAttr("disabled")
    } else {
        $("#add").attr("disabled", "disabled");
        $("#cetak").attr("disabled", "disabled")
    }
}

function remove(id) {
    
    var removeIndex = product_cart.map((r,i) =>{ return (r.id=="5")? i : null });

    let data = transaksi.row($("[name=" + id + "]").closest("tr")).data(),
    jumlah = parseFloat(product_cart[removeIndex[0]].jumlah),
    harga = parseFloat(product_cart[removeIndex[0]].harga),
    diskon = parseFloat(product_cart[removeIndex[0]].diskon),
    total = parseFloat($("#total").html());
    akhir = total - (jumlah * ((diskon > 0)? harga-(harga * diskon/100): harga))

    $("#total").html(akhir);

    transaksi.row($("[name=" + id + "]").closest("tr")).remove().draw();
    $("#tambah").attr("disabled", "disabled");
    if (akhir < 1) {
        $("#bayar").attr("disabled", "disabled")
    }
    
    ~removeIndex && product_cart.splice(removeIndex[0], 1);
}

function add() {
    if ($('[name="ongkir"]').val() > $('[name="jumlah_uang"]').val()) {
        Swal.fire("Gagal", "Jumlah uang harus lebih besar atau sama dengan ongkir", "warning");
    } else {
        $("#total").html($(".total_bayar").html());
        $.ajax({
            url: addUrl,
            type: "post",
            dataType: "json",
            data: {
                produk: JSON.stringify(product_cart),
                tanggal: $("#tanggal").val(),
                total_bayar: $(".total_bayar").html(),
                jumlah_uang: $('[name="jumlah_uang"]').val(),
                ongkir: $('[name="ongkir"]').val(),
                pelanggan: $("#pelanggan").val(),
                nota: $("#nota").html(),
            },
            success: (res) => {
                if (isCetak) {
                    Swal.fire("Sukses", "Sukses Membayar", "success").then(() => {
                        window.open(cetakUrl + "/" + res, "_blank");
                        setTimeout(() => window.location.reload(), 0);
                    });
                } else {
                    Swal.fire("Sukses", "Sukses Membayar", "success").then(() =>
                        window.location.reload()
                    );
                }
            },
            error: (err) => {
                console.log(err);
            },
        });
    }

}

function kembalian() {
    let total = parseFloat(($("#total").html())? $("#total").html() : 0 ),
        jumlah_uang = parseFloat(($('[name="jumlah_uang"').val())? $('[name="jumlah_uang"').val() : 0 ),
        ongkir = parseFloat(($('[name="ongkir"').val())? $('[name="ongkir"').val() : 0 );

    $('.total_bayar').html(total+ongkir);
    $(".kembalian").html(jumlah_uang - (total+ongkir));
    checkUang()
}
$("#produk").select2({
    placeholder: "Produk",
    ajax: {
        url: listProduk,
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
});
$("#pelanggan").select2({
    placeholder: "Pelanggan",
    ajax: {
        url: listPelanggan,
        type: "post",
        dataType: "json",
        data: params => ({
            pelanggan: params.term
        }),
        processResults: res => ({
            results: res
        }),
        cache: true
    }
});
$("#tanggal").datetimepicker({
    format: "dd-mm-yyyy h:ii:ss"
});
$(".modal").on("hidden.bs.modal", () => {
    $("#form")[0].reset();
    $("#form").validate().resetForm()
});
$(".modal").on("show.bs.modal", () => {
    let now = moment().format("D-MM-Y H:mm:ss"),
        total = $("#total").html(),
        jumlah_uang = $('[name="jumlah_uang"').val();
    $("#tanggal").val(now), $(".total_bayar").html(total), $(".kembalian").html(Math.max(jumlah_uang - total, 0))
});
$("#form").validate({
    errorElement: "span",
    errorPlacement: (err, el) => {
        err.addClass("invalid-feedback"), el.closest(".form-group").append(err)
    },
    submitHandler: () => {
        add()
    }
});
$("#nota").html(nota(15));
$("#produk_detail").hide();
$("#produk").attr("disabled", true);

// $('#pelanggan').on('select2:select', function (e) {
//     var data = e.params.data;
//     console.log(data);
//     console.log(e);
// });
