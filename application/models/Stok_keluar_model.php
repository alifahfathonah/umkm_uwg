<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stok_keluar_model extends CI_Model {

	private $table = 'stok_keluar';

	public function create($data)
	{
		$userdata = $this->session->userdata();
		if($userdata["role"] != 1) $data["toko_id"] = $userdata["toko"]["id"];

		return $this->db->insert($this->table, $data);
	}

	public function read()
	{
		$userdata = $this->session->userdata();
		if($userdata["role"] != 1) $this->db->where("stok_keluar.toko_id", $userdata["toko"]["id"]);

		$this->db->select('stok_keluar.id, stok_keluar.tanggal, stok_keluar.jumlah, stok_keluar.keterangan, produk.barcode, produk.nama_produk');
		$this->db->from($this->table);
		$this->db->join('produk', 'produk.id = stok_keluar.barcode');
		return $this->db->get();
	}

	public function getStok($id)
	{
		$this->db->select('stok');
		$this->db->where('id', $id);
		return $this->db->get('produk')->row();
	}

	public function addStok($id,$stok)
	{
		$this->db->where('id', $id);
		$this->db->set('stok', $stok);
		return $this->db->update('produk');
	}


	public function update($id, $data)
	{
		$detail = $this->db->get_where($this->table, ["id"=>$id])->row_array();

		print_r($detail);
		exit;

		if(!empty($detail)){
			$this->db->trans_start();
			
			$this->db->where('id', $id);
			$this->db->update($this->table, $data);
			
			$detail = $this->db->get_where($this->table, ["id"=>$id])->row_array();
			
			$stok_now = $this->getStok($detail["barcode"]);
			$stok = $stok_now->stok + $detail["jumlah"]; 
			
			$this->addStok($detail["barcode"], $stok);
			
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				return;
			}else{
				$this->db->trans_complete();
				return true;
			}

		}else{
			return;
		}
	}

	public function delete($id)
	{
		$this->db->trans_start();

		$this->db->where('id', $id)
		->delete($this->table);

		if($this->db->affected_rows() > 0){
			$this->db->where('produk', $id)->delete("tipe_produk_pelanggan");
		}
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			return;
		}else{
			$this->db->trans_complete();
			print_r($this->db->trans_complete());
			return true;
		}
	}

	public function getStokKeluar($id)
	{
		$produk = [];

		$q1 = $this->db->select('produk.id, produk.barcode, produk.nama_produk,  produk.stok, produk.kategori kategori_id, kategori_produk.kategori, produk.satuan satuan_id, satuan_produk.satuan')
		->from($this->table)
		->join('kategori_produk', 'produk.kategori = kategori_produk.id', 'left')
		->join('satuan_produk', 'produk.satuan = satuan_produk.id', 'left')
		->where('produk.id', $id);
		$produk = $q1->get()->row_array();

		$q2 = $this->db->select('tipe_produk_pelanggan.id, tipe_pelanggan.nama pelanggan, harga, diskon')
		->from('tipe_produk_pelanggan')
		->join('tipe_pelanggan', 'tipe_pelanggan.id = tipe_produk_pelanggan.tipe', 'left')
		->where('tipe_produk_pelanggan.produk', $id);
		$pelanggan = $q2->get()->result_array();
		
		if (!empty($pelanggan)) $produk["pelanggan"] = $pelanggan;

		return $produk;
	}

	public function retur($id)
	{
		$detail = $this->db->get_where($this->table, ["id"=>$id])->row_array();
		if(!empty($detail)){
			$stok_now = $this->getStok($detail["barcode"]);
			$stok = $stok_now->stok + $detail["jumlah"]; 
			
			$this->db->trans_start();
	
			$this->addStok($detail["barcode"], $stok);
	
			$this->db->where('id', $id)
			->delete($this->table);
			
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				return;
			}else{
				$this->db->trans_complete();
				return true;
			}

		}else{
			return;
		}
	}

}

/* End of file Stok_keluar_model.php */
/* Location: ./application/models/Stok_keluar_model.php */