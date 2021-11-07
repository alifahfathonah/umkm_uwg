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
		$detail_before = $this->db->get_where($this->table, ["id"=>$id])->row_array();

		if(!empty($detail_before)){
			$this->db->trans_start();
			
			$this->db->where('id', $id);
			$this->db->update($this->table, $data);
			
			$detail = $this->db->get_where($this->table, ["id"=>$id])->row_array();
			
			$stok_now = $this->getStok($detail["barcode"]);
			$stok = 0;
			
			if($detail_before["jumlah"] > $detail["jumlah"]){
				$stok = $stok_now->stok + ($detail_before["jumlah"] - $detail["jumlah"]);
			}else{
				$stok = $stok_now->stok - ($detail["jumlah"] - $detail_before["jumlah"]);
			}
			
			$this->addStok($detail["barcode"], $stok);
			
			if($detail["jumlah"] == 0){
				$this->db->where('id', $id)
				->delete($this->table);
			}
			
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

		$detail = $this->db->get_where($this->table, ["id"=>$id])->row_array();
		$stok_now = $this->getStok($detail["barcode"]);
		$stok = $stok_now->stok + $detail["jumlah"];

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
	}

	public function getStokKeluar($id)
	{
		return $this->db->select("
			stok_keluar.*,
			stok_keluar.Keterangan keterangan,
			produk.barcode barcode_name
		")->from($this->table)
		->join("produk", "produk.id = stok_keluar.barcode", "LEFT")
		->where("stok_keluar.id",$id)
		->get()->row_array();
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