let jurnal_laba_rugi=$("#jurnal_laba_rugi").DataTable( {
    responsive:true,
    scrollX:true,
    ajax:laporanUrl,
    columnDefs:[ {
        searcable: false,
        orderable: false,
        targets: 0
    }],
    initComplete: (d) => {
        let data = d.json.data;

        if(data.length>0){
            $('#saldo_akhir').text(data[data.length - 1].saldo);
        }else{
            $('#saldo_akhir').text(0);
        }
    },
    // order:[[1, "asc"]],
    columns:[ 
        { data: null }, 
        { data: "tanggal" }, 
        { data: "nota"}, 
        { data: "sell_produk" }, 
        { data: "sell_qty" }, 
        { data: "sell_harga" },
        { data: "buy_produk" },
        { data: "buy_qty" },
        { data: "buy_harga" },
        { data: "debet" },
        { data: "kredit" },
        { data: "saldo" },
    ]
}

);
function reloadTable() {
    jurnal_laba_rugi.ajax.reload()
}

jurnal_laba_rugi.on("order.dt search.dt", ()=> {
    jurnal_laba_rugi.column(0, {
        search: "applied",
        order: "applied"
    }).nodes().each((el, err)=> {
        el.innerHTML=err+1
    })
});
$(".modal").on("hidden.bs.modal", ()=> {
    $("#form")[0].reset();
    $("#form").validate().resetForm()
});