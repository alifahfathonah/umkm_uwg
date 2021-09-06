<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pengguna_model extends CI_Model {

	private $table = 'pengguna';

	public function create($data)
	{
		return $this->db->insert($this->table, $data);
	}

	public function read($user)
	{
		$this->db->select("pengguna.*, pengguna.role role_id, role_pengguna.nama role, toko.nama toko")
		->from($this->table)
		->join("role_pengguna", "pengguna.role = role_pengguna.id", "left")
		->join("toko", "pengguna.toko_id = toko.id", "left");

		if($user["role"] == 2){
			$this->db->where("pengguna.role", $user["role"]);
			$this->db->where("pengguna.toko_id", $user["toko"]["id"]);
		}

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

	public function getPengguna($id)
	{
		$this->db->select("pengguna.*, pengguna.role role_id, role_pengguna.nama role, toko.nama toko")
		->from($this->table)
		->join("role_pengguna", "pengguna.role = role_pengguna.id", "left")
		->join("toko", "pengguna.toko_id = toko.id", "left")
		->where('pengguna.id', $id);
		return $this->db->get();
	}

	public function search_role($search="")
	{
		$this->db->like('nama', $search);
		return $this->db->get("role_pengguna")->result();
	}

	public function search_toko($search="")
	{
		$this->db->like('nama', $search);
		return $this->db->get("toko")->result();
	}

}

/* End of file Pengguna_model.php */
/* Location: ./application/models/Pengguna_model.php */