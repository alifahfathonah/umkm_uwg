<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stok_masuk_model extends CI_Model {

	private $table = 'stok_masuk';

	public function create($data)
	{
		$userdata = $this->session->userdata();
		if($userdata["role"] != 1) $data["toko_id"] = $userdata["toko"]["id"];
		
		return $this->db->insert($this->table, $data);
	}

	public function read()
	{	
		$userdata = $this->session->userdata();
		if($userdata["role"] != 1) $this->db->where("stok_masuk.toko_id", $userdata["toko"]["id"]);

		$this->db->select('stok_masuk.id, stok_masuk.tanggal, stok_masuk.jumlah, stok_masuk.harga, stok_masuk.keterangan, produk.barcode, produk.nama_produk');
		$this->db->from($this->table);
		$this->db->join('produk', 'produk.id = stok_masuk.barcode');
		return $this->db->get();
	}

	public function laporan()
	{
		$userdata = $this->session->userdata();
		if($userdata["role"] != 1) $this->db->where("stok_masuk.toko_id", $userdata["toko"]["id"]);

		$this->db->select('stok_masuk.tanggal, stok_masuk.jumlah, stok_masuk.harga, stok_masuk.keterangan, produk.barcode, produk.nama_produk, supplier.nama as supplier');
		$this->db->from($this->table);
		$this->db->join('produk', 'produk.id = stok_masuk.barcode');
		$this->db->join('supplier', 'supplier.id = stok_masuk.supplier', 'left outer');
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

	public function stokHari($hari)
	{
		$userdata = $this->session->userdata();
		$where = ($userdata["role"] != 1)? 'stok_masuk.toko_id ='.$userdata["toko"]["id"] : '1=1';

		return $this->db->query("SELECT SUM(jumlah) AS total FROM stok_masuk WHERE DATE_FORMAT(tanggal, '%d %m %Y') = '$hari' AND $where")->row();
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
				$stok = $stok_now->stok - ($detail_before["jumlah"] - $detail["jumlah"]);
			}else{
				$stok = $stok_now->stok + ($detail["jumlah"] - $detail_before["jumlah"]);
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
		$stok = $stok_now->stok - $detail["jumlah"];

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

	public function getStokMasuk($id)
	{
		return $this->db->select("
			stok_masuk.*,
			produk.nama_produk nama_produk,
			produk.barcode barcode_name,
			supplier.nama supplier_name
		")->from($this->table)
		->join("produk", "produk.id = stok_masuk.barcode", "LEFT")
		->join("supplier", "supplier.id = stok_masuk.supplier", "LEFT")
		->where("stok_masuk.id",$id)
		->get()->row_array();
	}

}

/* End of file Stok_masuk_model.php */
/* Location: ./application/models/Stok_masuk_model.php */