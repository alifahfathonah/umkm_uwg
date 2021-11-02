let url, cicilan = $("#cicilan").DataTable({
    responsive: true,
    scrollX: true,
    ajax: readUrl,
    columnDefs: [{
        searcable: false,
        orderable: false,
        targets: 0
    }],
    columns: [{
        data: null
    }, {
        data: "nota"
    }, {
        data: "pelanggan"
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

function getSisa(){
    let arr = cicilan_now.filter((a) => a.sisa != undefined);
    if (arr.length > 0){
        return arr[arr.length - 1].sisa;
    }else{
        return 0;
    }
}

function editData() {
    let totalSisa = getSisa();
    let totalTrans = cicilan_now.reduce((a, b)=>parseFloat(a.trans_terakhir)+parseFloat(b.trans_terakhir));

    if (totalSisa < totalTrans){
        Swal.fire("Error", "Nominal pembayaran, melebihi hutang", "error");
        return false;
    }
    
    $.ajax({
        url: editUrl,
        type: "post",
        dataType: "json",
        data: {
            "id": $('[name="id"]').val(),
            "cicilan": JSON.stringify(cicilan_now)
        },
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
            $("#tbl_cicilan tbody").html('');

            if(res.status == "Lunas"){
                $("#btn-add-cicilan").hide();                
                $("#btn-save-cicilan").attr("disabled", true);                
            }else{
                $("#btn-add-cicilan").show();                
                $("#btn-save-cicilan").attr("disabled", false);
            }

            $("#list-barang").html("");
            if(res.barang.length > 0) {
                res.barang.forEach((r)=>{
                    $("#list-barang").append(`<span class="span-produk">${r.nama_produk} (${r.qty})</span>`);
                });
            }

            if(res.cicilan.length > 0) {
                cicilan_now = res.cicilan;

                cicilan_now.forEach(r => {
                    $("#tbl_cicilan tbody").append(`
                        <tr>
                            <td align="center">${r.tanggal}</td>
                            <td align="center">${r.trans_terakhir}</td>
                            <td align="center">${r.sisa}</td>
                        </tr>
                    `);    
                });
            }else{
                var id = myID();
                cicilan_now = [];

                $("#tbl_cicilan tbody").append(`
                    <tr>
                        <td align="center">
                            <input type="date" class="form-control" placeholder="Date" required="true" onchange="updateCicilan('${id}')" name="date_${id}" value="${valToday()}">
                        </td>
                        <td align="center">
                            <input type="text" class="form-control" placeholder="" onchange="updateCicilan('${id}')" name="bayar_${id}" required>
                        </td>
                        <td align="center">${res.hutang}</td>
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

function myID() {
    var n = 4;
    var add = 1, max = 12 - add;   // 12 is the min safe number Math.random() can generate without it starting to pad the end with zeros.

    if ( n > max ) {
            return generate(max) + generate(n - max);
    }

    max        = Math.pow(10, n+add);
    var min    = max/10; // Math.pow(10, n) basically
    var number = Math.floor( Math.random() * (max - min + 1) ) + min;

    return  (Math.random() + 1).toString(36).substring(7) + ("" + number).substring(add); 
}

function newCicilan() {
    var id = myID();

    $("#tbl_cicilan tbody").append(`
        <tr>
            <td align="center">
                <input type="date" class="form-control" placeholder="Date" onchange="updateCicilan('${id}')" name="date_${id}" required="true" value="${valToday()}">
            </td>
            <td align="center">
                <input type="text" class="form-control" placeholder="" onchange="updateCicilan('${id}')" name="bayar_${id}" required>
            </td>
            <td align="center">
                <span>0</span>
            </td>
        </tr>
    `);
}

function updateCicilan(id) {
    let find_cicilan = cicilan_now.filter(r => r.id == id);

    if (find_cicilan.length > 0) {
        cicilan_now.forEach((r,i)=>{
            if(r.id == id){
                cicilan_now[i] = {
                    "id": id,
                    "tanggal": $(`[name="date_${id}"]`).val(),
                    "trans_terakhir": $(`[name="bayar_${id}"]`).val(),        
                };
            }
        });
    }else{
        cicilan_now.push({
            "id": id,
            "tanggal": $(`[name="date_${id}"]`).val(),
            "trans_terakhir": $(`[name="bayar_${id}"]`).val(),        
        });
    }
}

function valToday() {
    var now = new Date();
    var month = (now.getMonth() + 1);               
    var day = now.getDate();
    if (month < 10) 
        month = "0" + month;
    if (day < 10) 
        day = "0" + day;
    return now.getFullYear() + '-' + month + '-' + day;
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
        el.closest(".form-group").append(err)
    },
    submitHandler: () => {
        "edit" == url ? editData() : addData()
    }
});
$(".modal").on("hidden.bs.modal", () => {
    $("#form")[0].reset();
    $("#form").validate().resetForm()
});