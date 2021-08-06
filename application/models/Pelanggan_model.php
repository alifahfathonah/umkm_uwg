<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pelanggan_model extends CI_Model {

	private $table = 'pelanggan';

	public function create($data)
	{
		return $this->db->insert($this->table, $data);
	}

	public function read()
	{	
		$this->db->select([
			"pelanggan.*",
			"tipe_pelanggan.id tipe_id",
			"tipe_pelanggan.nama tipe"
		])
		->from($this->table)
		->join("tipe_pelanggan", "tipe_pelanggan.id = pelanggan.tipe", "left");
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
		$this->db->select([
			"pelanggan.*",
			"tipe_pelanggan.id tipe_id",
			"tipe_pelanggan.nama tipe"
		])
		->from($this->table)
		->join("tipe_pelanggan", "tipe_pelanggan.id = pelanggan.tipe", "left")
		->where('pelanggan.id', $id);
		return $this->db->get();
	}

	public function search($search="")
	{
		$this->db->like('nama', $search);
		return $this->db->get($this->table)->result();
	}

	public function get_tipe()
	{
		return $this->db->get("tipe_pelanggan")->result();
	}

	public function getListPelanggan()
	{
		$this->db->select('*, nama text');
		return $this->db->get($this->table)->result_array();
	}

}

/* End of file Pelanggan_model.php */
/* Location: ./application/models/Pelanggan_model.php */