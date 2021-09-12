<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pengguna_model extends CI_Model {

	private $table = 'pengguna';

	public function create($data)
	{
		$userdata = $this->session->userdata();
		if($userdata["role"] != 1) $data["toko_id"] = $userdata["toko"]["id"];
		
		return $this->db->insert($this->table, $data);
	}

	public function read($user)
	{
		$this->db->select("pengguna.*, pengguna.role role_id, role_pengguna.nama role, toko.nama toko")
		->from($this->table)
		->join("role_pengguna", "pengguna.role = role_pengguna.id", "left")
		->join("toko", "pengguna.toko_id = toko.id", "left")
		->order_by("pengguna.role ASC, pengguna.id ASC");

		if($user["role"] != 1){
			$this->db->where("pengguna.role != 1");
			$this->db->where("pengguna.toko_id", $user["toko"]["id"]);
		}

		return $this->db->get();
	}

	public function update($id, $data)
	{
		$userdata = $this->session->userdata();
		if($userdata["role"] != 1) $data["toko_id"] = $userdata["toko"]["id"];

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

	public function search_role($search="", $role)
	{
		$this->db->like('nama', $search);

		if($role != 1) $this->db->where('id !=', 1);
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