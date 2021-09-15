<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Produk_model extends CI_Model {

	private $table = 'produk';

	public function create($data)
	{
		$pelanggan = $data["pelanggan"];
		unset($data["pelanggan"]);

		$userdata = $this->session->userdata();
		if($userdata["role"] != 1) $data["toko_id"] = $userdata["toko"]["id"];

		$this->db->insert($this->table, $data);
		$produk_id = $this->db->insert_id();

		if($produk_id){
			if(!empty($pelanggan)){
				foreach($pelanggan as $key => $value){
					$this->db->insert("tipe_produk_pelanggan",[
						"produk" => $produk_id,
						"tipe" => $value["tipe"],
						"harga" => $value["harga"],
						"diskon" => $value["diskon"],
					]);
				}
			}

			return $produk_id;
		}else{
			return;
		}
	}

	public function read()
	{
		$userdata = $this->session->userdata();
		if($userdata["role"] != 1) $this->db->where("produk.toko_id", $userdata["toko"]["id"]);

		$this->db->select('produk.id, produk.barcode, produk.nama_produk, tipe_pelanggan.nama pelanggan, tipe_produk_pelanggan.harga, tipe_produk_pelanggan.diskon, produk.stok, kategori_produk.kategori, satuan_produk.satuan')
		->from($this->table)
		->join('tipe_produk_pelanggan', 'produk.id = tipe_produk_pelanggan.produk', 'left')
		->join('tipe_pelanggan', 'tipe_pelanggan.id = tipe_produk_pelanggan.tipe', 'left')
		->join('kategori_produk', 'produk.kategori = kategori_produk.id', 'left')
		->join('satuan_produk', 'produk.satuan = satuan_produk.id', 'left')
		->group_by("tipe_produk_pelanggan.id")
		->order_by("produk.id", "DESC");

		return $this->db->get();
	}

	public function update($id, $data)
	{
		$pelanggan = $data["pelanggan"];
		unset($data["pelanggan"]);

		$userdata = $this->session->userdata();
		if($userdata["role"] != 1) $data["toko_id"] = $userdata["toko"]["id"];
		
		$this->db->trans_start();
		$this->db->where('id', $id);
		$q = $this->db->update($this->table, $data);
		
		if($q){
			if(!empty($pelanggan)){
				foreach($pelanggan as $key => $value){
					$detail = $this->db->get_where('tipe_produk_pelanggan', ["id"=>$value["id"]])->row();
					if($detail && $value["id"]){
						$this->db->where('id', $value["id"])->update("tipe_produk_pelanggan",[
							"produk" => $id,
							"tipe" => $value["tipe"],
							"harga" => $value["harga"],
							"diskon" => $value["diskon"],
						]);
					}else{
						$this->db->insert("tipe_produk_pelanggan",[
							"produk" => $id,
							"tipe" => $value["tipe"],
							"harga" => $value["harga"],
							"diskon" => $value["diskon"],
						]);
					}
				}
			}
		}

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			return;
		}else{
			$this->db->trans_complete();
			return true;
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

	public function getProduk($id)
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

	public function getBarcode($search='')
	{
		$userdata = $this->session->userdata();
		if($userdata["role"] != 1) $this->db->where("produk.toko_id", $userdata["toko"]["id"]);

		$this->db->select('produk.id, produk.barcode');
		$this->db->like('barcode', $search);
		return $this->db->get($this->table)->result();
	}

	public function getNama($id)
	{
		$userdata = $this->session->userdata();
		if($userdata["role"] != 1) $this->db->where("produk.toko_id", $userdata["toko"]["id"]);

		$this->db->select('nama_produk, stok');
		$this->db->where('id', $id);
		return $this->db->get($this->table)->row();
	}

	public function getStok($id)
	{
		$userdata = $this->session->userdata();
		if($userdata["role"] != 1) $this->db->where("produk.toko_id", $userdata["toko"]["id"]);

		$this->db->select('stok, nama_produk, harga, barcode');
		$this->db->where('id', $id);
		return $this->db->get($this->table)->row();
	}

	public function produkTerlaris()
	{	
		$userdata = $this->session->userdata();
		$where = ($userdata["role"] != 1)? 'produk.toko_id='.$userdata["toko"]["id"] : '1=1';

		return $this->db->query('SELECT produk.nama_produk, produk.terjual FROM `produk` WHERE '.$where.'
		ORDER BY CONVERT(terjual,decimal)  DESC LIMIT 5')->result();
	}

	public function dataStok()
	{
		$userdata = $this->session->userdata();
		$where = ($userdata["role"] != 1)? 'produk.toko_id='.$userdata["toko"]["id"] : '1=1';

		return $this->db->query('SELECT produk.nama_produk, produk.stok FROM `produk` WHERE '.$where.' ORDER BY CONVERT(stok, decimal) DESC LIMIT 50')->result();
	}

	public function getListProduk()
	{
		$userdata = $this->session->userdata();
		if($userdata["role"] != 1) $this->db->where("produk.toko_id", $userdata["toko"]["id"]);
		
		$this->db->select('*, nama_produk text');
		return $this->db->get($this->table)->result_array();
	}

}

/* End of file Produk_model.php */
/* Location: ./application/models/Produk_model.php */