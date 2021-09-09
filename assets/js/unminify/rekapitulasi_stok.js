let rekapitulasi_stok=$("#rekapitulasi_stok").DataTable( {
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
        { data: "produk" }, 
        { data: "stok_awal"}, 
        { data: "stok_keluar"}, 
        { data: "stok_jual" }, 
        { data: "sisa" }, 
    ]
}

);
function reloadTable() {
    rekapitulasi_stok.ajax.reload()
}

rekapitulasi_stok.on("order.dt search.dt", ()=> {
    rekapitulasi_stok.column(0, {
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