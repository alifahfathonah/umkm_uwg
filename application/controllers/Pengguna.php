<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pengguna extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		if ($this->session->userdata('status') !== 'login' ) {
			redirect('/');
		}
		$this->load->model('pengguna_model');
	}

	public function index()
	{
		$this->load->view('pengguna');
	}
	
	public function read()
	{
		$userdata = $this->session->userdata();
		header('Content-type: application/json');
		if ($this->pengguna_model->read($userdata)->num_rows() > 0) {
			foreach ($this->pengguna_model->read($userdata)->result() as $pengguna) {
				$data[] = array(
					'username' => $pengguna->username,
					'nama' => $pengguna->nama,
					'role' => $pengguna->role,
					'toko' => $pengguna->toko,
					'action' => '<button class="btn btn-sm btn-success" onclick="edit('.$pengguna->id.')">Edit</button> <button class="btn btn-sm btn-danger" onclick="remove('.$pengguna->id.')">Delete</button>'
				);
			}
		} else {
			$data = array();
		}
		$pengguna = array(
			'data' => $data
		);
		echo json_encode($pengguna);
	}

	public function add()
	{	
		$userdata = $this->session->userdata();
		$data = array(
			'username' => $this->input->post('username'),
			'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
			'nama' => $this->input->post('nama'),
			'role' => $this->input->post('role'),
			'toko_id' => $this->input->post('toko')
		);

		if($userdata["role"] != 1){
			$data["toko_id"] = $userdata["toko"]["id"];
		}

		if ($this->pengguna_model->create($data)) {
			echo json_encode(["success"=>true, "message"=>"Create Data Success"]);
		}else{
			echo json_encode(["success"=>false, "message"=>"Create Data Failed"]);
		}
	}

	public function delete()
	{
		$id = $this->input->post('id');
		if ($this->pengguna_model->delete($id)) {
	
			echo json_encode(["success"=>true, "message"=>"Delete Data Success"]);
		}else{
			echo json_encode(["success"=>false, "message"=>"Delete Data Failed"]);
		}
	}

	public function edit()
	{
		$id = $this->input->post('id');
		$pengguna = $this->pengguna_model->getPengguna($id)->row_array();
		if($pengguna){
			$data = array(
				'username' => $this->input->post('username'),
				'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
				'nama' => $this->input->post('nama'),
				'role' => $this->input->post('role'),
				'toko_id' => $this->input->post('toko')
			);

			if(empty($this->input->post('password'))) unset($data["password"]);

			if ($this->pengguna_model->update($id,$data)) {
				echo json_encode(["success"=>true, "message"=>"Update Data Success"]);
			}else{
				echo json_encode(["success"=>false, "message"=>"Update Data Failed"]);
			}
		}else{
			echo json_encode(["success"=>false, "message"=>"Invalid ID"]);
		}
	}

	public function get_pengguna()
	{
		$id = $this->input->post('id');
		$pengguna = $this->pengguna_model->getPengguna($id);
		if ($pengguna->row()) {
			echo json_encode($pengguna->row());
		}
	}

	public function search_role()
	{
		$userdata = $this->session->userdata();
		header('Content-type: application/json');
		$role = $this->input->post('role');
		$search = $this->pengguna_model->search_role($role, $userdata["role"]);
		foreach ($search as $role) {
			$data[] = array(
				'id' => $role->id,
				'text' => $role->nama
			);
		}
		echo json_encode($data);
	}

	public function search_toko()
	{
		header('Content-type: application/json');
		$toko = $this->input->post('toko');
		$search = $this->pengguna_model->search_toko($toko);
		foreach ($search as $toko) {
			$data[] = array(
				'id' => $toko->id,
				'text' => $toko->nama
			);
		}
		echo json_encode($data);
	}

}

/* End of file Pengguna.php */
/* Location: ./application/controllers/Pengguna.php */