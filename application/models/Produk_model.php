<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Produk_model extends CI_Model {

	private $table = 'produk';

	public function create($data)
	{
		$pelanggan = $data["pelanggan"];
		unset($data["pelanggan"]);

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
		$this->db->select('produk.id, produk.barcode, produk.nama_produk, tipe_pelanggan.nama pelanggan, tipe_produk_pelanggan.harga, tipe_produk_pelanggan.diskon, produk.stok, kategori_produk.kategori, satuan_produk.satuan')
		->from($this->table)
		->join('tipe_produk_pelanggan', 'produk.id = tipe_produk_pelanggan.produk', 'left')
		->join('tipe_pelanggan', 'tipe_pelanggan.id = tipe_produk_pelanggan.tipe', 'left')
		->join('kategori_produk', 'produk.kategori = kategori_produk.id', 'left')
		->join('satuan_produk', 'produk.satuan = satuan_produk.id', 'left');

		return $this->db->get();
	}

	public function update($id, $data)
	{
		$pelanggan = $data["pelanggan"];
		unset($data["pelanggan"]);
		
		$this->db->where('id', $id);
		$q = $this->db->update($this->table, $data);

		print_r($this->db->affected_rows());
		exit;
		if($q){
			if(!empty($pelanggan)){
				foreach($pelanggan as $key => $value){
					$detail = $this->db->get_where('tipe_produk_pelanggan', ["id"=>$value["id"]]);
					if($detail && $value["id"]){
						$this->db->where('id', $value["id"])->update("tipe_produk_pelanggan",[
							"produk" => $produk_id,
							"tipe" => $value["tipe"],
							"harga" => $value["harga"],
							"diskon" => $value["diskon"],
						]);
					}else{
						$this->db->insert("tipe_produk_pelanggan",[
							"produk" => $produk_id,
							"tipe" => $value["tipe"],
							"harga" => $value["harga"],
							"diskon" => $value["diskon"],
						]);
					}
				}
			}

			return $produk_id;
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

	public function getProduk($id)
	{
		$this->db->select('produk.id, produk.barcode, produk.nama_produk, tipe_pelanggan.nama pelanggan, tipe_produk_pelanggan.harga, tipe_produk_pelanggan.diskon, produk.stok, kategori_produk.kategori, satuan_produk.satuan')
		->from($this->table)
		->join('tipe_produk_pelanggan', 'produk.id = tipe_produk_pelanggan.produk', 'left')
		->join('tipe_pelanggan', 'tipe_pelanggan.id = tipe_produk_pelanggan.tipe', 'left')
		->join('kategori_produk', 'produk.kategori = kategori_produk.id', 'left')
		->join('satuan_produk', 'produk.satuan = satuan_produk.id', 'left')
		->where('produk.id', $id);

		return $this->db->get();
	}

	public function getBarcode($search='')
	{
		$this->db->select('produk.id, produk.barcode');
		$this->db->like('barcode', $search);
		return $this->db->get($this->table)->result();
	}

	public function getNama($id)
	{
		$this->db->select('nama_produk, stok');
		$this->db->where('id', $id);
		return $this->db->get($this->table)->row();
	}

	public function getStok($id)
	{
		$this->db->select('stok, nama_produk, harga, barcode');
		$this->db->where('id', $id);
		return $this->db->get($this->table)->row();
	}

	public function produkTerlaris()
	{
		return $this->db->query('SELECT produk.nama_produk, produk.terjual FROM `produk` 
		ORDER BY CONVERT(terjual,decimal)  DESC LIMIT 5')->result();
	}

	public function dataStok()
	{
		return $this->db->query('SELECT produk.nama_produk, produk.stok FROM `produk` ORDER BY CONVERT(stok, decimal) DESC LIMIT 50')->result();
	}

}

/* End of file Produk_model.php */
/* Location: ./application/models/Produk_model.php */