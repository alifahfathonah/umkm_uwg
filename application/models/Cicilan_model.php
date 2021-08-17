<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cicilan_model extends CI_Model {

	private $table = 'transaksi_utang';

	public function create($data)
	{
		return $this->db->insert($this->table, $data);
	}

	public function read()
	{	
		$this->db->select('
			transaksi_utang.id,
			transaksi.nota,
			pelanggan.nama as pelanggan,
			transaksi.total_bayar,
			IF(transaksi_utang.status="Lunas", 0, transaksi_utang.hutang) hutang,
			transaksi_utang.status

		')->from($this->table)
		->join('transaksi', 'transaksi_utang.transaksi_id = transaksi.id','left')
		->join('pelanggan', 'transaksi.pelanggan = pelanggan.id', 'left');
		return $this->db->get();
	}

	public function update($id, $data)
	{
		$this->db->where('id', $id);
		return $this->db->update($this->table, $data);
	}

	public function delete($id)
	{
		$this->db->where('id', $id);
		return $this->db->delete($this->table);
	}

	public function detail($id)
	{
		$detail = $this->db->select("
				transaksi_utang.*, 
				IF(transaksi_utang.status='Lunas', 0, transaksi_utang.hutang) hutang,
				transaksi.nota
			")
			->from($this->table)
			->join("transaksi", "transaksi.id = transaksi_utang.transaksi_id")
			->where("transaksi_utang.id = $id")
			->get()->row_array();
		$detail["cicilan"] = $this->db->order_by("sisa", "desc")->get_where("transaksi_cicilan", ["utang_id" => $id])->result_array();

		return $detail;
	}

	public function get_utang($id)
	{
		return $this->db->get_where("transaksi_utang", ["id"=>$id])->row_array();
	}

	public function last_trans($id) 
	{
		$last_trans = $this->db->from("transaksi_cicilan")->order_by("sisa", "asc")->limit("1")->get()->row_array();

		if($last_trans){
			return $last_trans["sisa"];
		}else{
			$utang = $this->db->get_where("transaksi_utang", ["id"=>$id])->row_array();
			return $utang["hutang"];
		}
	}

	public function create_cicilan($id, $cicilan)
	{
		$this->db->trans_start();
		
		foreach ($cicilan as $key => $item) {
			$get_cicillan = $this->db->get_where("transaksi_cicilan", ["id"=>$item->id])->row_array();
			$last_trans = $this->last_trans($id);
			$sisa = $last_trans - $item->trans_terakhir;

			if(!$get_cicillan){
				$this->db->insert("transaksi_cicilan", [
					'utang_id' => $id,
					'tanggal' => $item->tanggal,
					'trans_terakhir' => $item->trans_terakhir,
					'sisa' => $sisa,
				]);
			}

			if($sisa == 0){
				$this->db->where("id", $id)->update("transaksi_utang", ["status"=>"Lunas"]);
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

}

/* End of file cicilan_model.php */
/* Location: ./application/models/cicilan_model.php */