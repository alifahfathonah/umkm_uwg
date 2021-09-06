<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Supplier_model extends CI_Model {

	private $table = 'supplier';

	public function create($data)
	{
		$userdata = $this->session->userdata();
		if($userdata["role"] != 1) $data["toko_id"] = $userdata["toko"]["id"];

		return $this->db->insert($this->table, $data);
	}

	public function read()
	{
		$userdata = $this->session->userdata();
		if($userdata["role"] != 1) $this->db->where("supplier.toko_id", $userdata["toko"]["id"]);

		return $this->db->get($this->table);
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

	public function getSupplier($id)
	{
		$this->db->where('id', $id);
		return $this->db->get($this->table);
	}

	public function search($search="")
	{
		$userdata = $this->session->userdata();
		if($userdata["role"] != 1) $this->db->where("supplier.toko_id", $userdata["toko"]["id"]);

		$this->db->like('nama', $search);
		return $this->db->get($this->table)->result();
	}

}

/* End of file Supplier_model.php */
/* Location: ./application/models/Supplier_model.php */