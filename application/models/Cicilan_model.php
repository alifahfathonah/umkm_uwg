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
			transaksi_utang.hutang,
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
		$detail = $this->db->select("transaksi_utang.*, transaksi.nota")
			->from($this->table)
			->join("transaksi", "transaksi.id = transaksi_utang.transaksi_id")
			->where("transaksi_utang.id = $id")
			->get()->row_array();
		$detail["cicilan"] = $this->db->get("transaksi_cicilan", ["utang_id" => $id])->result_array();

		return $detail;
	}

	public function create_cicilan($data)
	{
		return $this->db->insert($this->table, $data);
	}

}

/* End of file cicilan_model.php */
/* Location: ./application/models/cicilan_model.php */