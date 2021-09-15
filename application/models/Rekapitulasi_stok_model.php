<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rekapitulasi_stok_model extends CI_Model {

    public function read()
	{
        $userdata = $this->session->userdata();
        if ($userdata['role'] != 1) $this->db->where('produk.toko_id', $userdata["toko"]["id"]);
        
        $q = $this->db->select('
            produk.nama_produk produk,
            IFNULL(stok_masuk.jumlah, 0) stok_awal,
            IFNULL(stok_keluar.jumlah, 0) stok_keluar,
            IFNULL(SUM(transaksi_item.jumlah), 0) stok_jual,
            (IFNULL(SUM(stok_masuk.jumlah), 0) - IFNULL(SUM(transaksi_item.jumlah), 0) - IFNULL(SUM(stok_keluar.jumlah), 0)) sisa
        ')
        ->from('produk')
        ->join('(SELECT barcode, SUM(jumlah) jumlah FROM stok_masuk GROUP BY stok_masuk.barcode) stok_masuk', 'produk.id = stok_masuk.barcode', 'left')
        ->join('(SELECT barcode, SUM(jumlah) jumlah FROM stok_keluar GROUP BY stok_keluar.barcode) stok_keluar', 'produk.id = stok_keluar.barcode', 'left')
        ->join('(SELECT produk_id, SUM(qty) jumlah FROM transaksi_item GROUP BY transaksi_item.produk_id)  transaksi_item', 'produk.id = transaksi_item.produk_id', 'left')
        ->group_by('produk.id')->get();
        
        return $q->result_array();
    }

}