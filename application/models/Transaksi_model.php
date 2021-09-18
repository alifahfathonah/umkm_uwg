<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transaksi_model extends CI_Model {

	private $table = 'transaksi';

	public function removeStok($id, $data)
	{
		$this->db->where('id', $id);
		$this->db->set($data);
		return $this->db->update('produk');
	}

	public function create($data)
	{
		$this->db->trans_start();
		
		$produk = $data["item"];
		unset($data["item"]);

		$userdata = $this->session->userdata();
		if($userdata["role"] != 1) $data["toko_id"] = $userdata["toko"]["id"];

		$this->db->insert($this->table, $data);
		$transaksi = $this->db->insert_id();

		if(!empty($produk)){
			foreach ($produk as $key => $value) {
				$this->db->insert("transaksi_item", [
					"transaksi_id" => $transaksi,
					"produk_id" => $value["produk"],
					"qty" => $value["qty"]
				]);
			}
		}
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			return;
		}else{
			$this->db->trans_complete();
			return $transaksi;
		}
	}

	public function read()
	{
		$userdata = $this->session->userdata();
		if($userdata["role"] != 1) $this->db->where("transaksi.toko_id", $userdata["toko"]["id"]);

		$this->db->select('
			transaksi.id, 
			transaksi.nota, 
			transaksi.tanggal, 
			transaksi.ongkir, 
			transaksi.total_bayar, 
			transaksi.jumlah_uang, 
			pelanggan.id pelanggan_id,
			pelanggan.nama as pelanggan,
			CONCAT(\'<a class="btn btn-sm btn-success" target="_blank" href="'.site_url('transaksi/cetak/').'\', transaksi.id, \'">Print</a> <button class="btn btn-sm btn-danger" onclick="remove(\', transaksi.id, \')">Delete</button>\') action,
			GROUP_CONCAT(\'<span class="label-produk">\',produk.nama_produk,\' <strong>(\',transaksi_item.qty, satuan_produk.satuan, \')</strong>\',\'</span>\' SEPARATOR \' \') produk
		')->from($this->table)
		->join('transaksi_item', 'transaksi.id = transaksi_item.transaksi_id', 'left')
		->join('produk', 'produk.id = transaksi_item.produk_id', 'left')
		->join('satuan_produk', 'produk.satuan = satuan_produk.id', 'left')
		->join('pelanggan', 'transaksi.pelanggan = pelanggan.id', 'left')
		->group_by("transaksi.id");
		return $this->db->get()->result_array();
	}

	public function delete($id)
	{
		$this->db->where('id', $id);
		return $this->db->delete($this->table);
	}

	public function getProduk($barcode, $qty)
	{
		$total = explode(',', $qty);
		foreach ($barcode as $key => $value) {
			$this->db->select('nama_produk');
			$this->db->where('id', $value);
			$data[] = '<tr><td>'.$this->db->get('produk')->row_array()["nama_produk"].' ('.$total[$key].')</td></tr>';
		}
		return join($data);
	}


	public function penjualanBulan($date)
	{
		$userdata = $this->session->userdata();
		$where = ($userdata["role"] != 1)? 'transaksi.toko_id ='.$userdata["toko"]["id"] : '1=1';

		$qty = $this->db->query("
			SELECT SUM(transaksi_item.qty) qty
			FROM transaksi 
			LEFT JOIN transaksi_item ON transaksi.id = transaksi_item.transaksi_id
			WHERE DATE(tanggal) = '$date'
			AND $where
			GROUP BY transaksi.id
			")->result();
		$d = [];
		$data = [];
		foreach ($qty as $key) {
			$d[] = explode(',', $key->qty);
		}
		foreach ($d as $key) {
			$data[] = array_sum($key);
		}
		return $data;
	}

	public function transaksiHari($hari)
	{
		$userdata = $this->session->userdata();
		$where = ($userdata["role"] != 1)? 'transaksi.toko_id ='.$userdata["toko"]["id"] : '1=1';

		return $this->db->query("SELECT COUNT(*) AS total FROM transaksi WHERE DATE_FORMAT(tanggal, '%d %m %Y') = '$hari' AND $where")->row();
	}

	public function transaksiTerakhir($hari)
	{
		$userdata = $this->session->userdata();
		$where = ($userdata["role"] != 1)? 'transaksi.toko_id ='.$userdata["toko"]["id"] : '1=1';

		return $this->db->query("
			SELECT SUM(transaksi_item.qty) qty
			FROM transaksi
			LEFT JOIN transaksi_item ON transaksi.id = transaksi_item.transaksi_id
			WHERE DATE(tanggal) = '$hari' AND $where
			GROUP BY transaksi.id
			LIMIT 1")->row();
	}

	public function getPrintTranskaksi($id)
	{
		$userdata = $this->session->userdata();
		$where = ($userdata["role"] != 1)? 'transaksi.toko_id ='.$userdata["toko"]["id"] : '1=1';

		$transaksi = $this->db->select('
			transaksi.id,   
			transaksi.nota, 
			transaksi.tanggal, 
			transaksi.total_bayar, 
			transaksi.jumlah_uang, 
			(transaksi.jumlah_uang - transaksi.total_bayar) kembalian,
			pelanggan.id pelanggan_id,
			pelanggan.nama as pelanggan,
			SUM(transaksi_item.qty) qty,
			pengguna.nama as kasir
		')->from($this->table)
		->join('transaksi_item', 'transaksi.id = transaksi_item.transaksi_id', 'left')
		->join('pelanggan', 'transaksi.pelanggan = pelanggan.id', 'left')
		->join('pengguna', 'transaksi.kasir = pengguna.id', 'left')
		->where('transaksi.id', $id)
		->where($where)
		->group_by("transaksi.id")->get()->row_array();

		$transaksi["item"] = $this->db->select('
			produk.id,
			produk.nama_produk,
			transaksi_item.qty,
			satuan_produk.satuan,
			tipe_produk_pelanggan.harga,
			tipe_produk_pelanggan.diskon
		')->from('transaksi_item')
		->join('transaksi', "transaksi_item.transaksi_id = transaksi.id AND $where", 'left')
		->join('pelanggan', 'transaksi.pelanggan = pelanggan.id', 'left')
		->join('produk', 'produk.id = transaksi_item.produk_id', 'left')
		->join('satuan_produk', 'produk.satuan = satuan_produk.id', 'left')
		->join('tipe_produk_pelanggan', 'produk.id = tipe_produk_pelanggan.produk and tipe_produk_pelanggan.tipe = pelanggan.tipe', 'left')
		->where('transaksi_id', $id)
		->group_by("transaksi_item.id")
		->get()->result_array();

		return $transaksi;
	}

}

/* End of file Transaksi_model.php */
/* Location: ./application/models/Transaksi_model.php */