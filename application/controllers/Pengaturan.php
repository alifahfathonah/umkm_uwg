<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pengaturan extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		if ($this->session->userdata('status') !== 'login' ) {
			redirect('/');
		}
		$this->load->model('toko_model');
	}
	
	public function index()
	{
		$toko = $this->db->get('toko')->row();
		$data['toko'] = $toko;
		$this->load->view('pengaturan', $data);
	}

	public function set_toko()
	{
		$data = array(
			'nama' => $this->input->post('nama'),
			'alamat' => $this->input->post('alamat')
		);
		$this->db->where('id', 1);
		if ($this->db->update('toko', $data)) {
			$this->db->select('nama, alamat');
			$toko = $this->db->get('toko')->row();
			$this->session->set_userdata('toko', $toko);
			echo json_encode('sukses');
		}
	}

	public function read()
	{
		$userdata = $this->session->userdata();
		header('Content-type: application/json');

		$toko = $this->toko_model->read();
		if (count($toko) > 0) {
			foreach ($toko as $key => $value) {
				$data[] = array(
					'nama' => $value['nama'],
					'alamat' => $value['alamat'],
					'action' => '<button class="btn btn-sm btn-success" onclick="edit('.$value['id'].')">Edit</button> <button class="btn btn-sm btn-danger" onclick="remove('.$value['id'].')">Delete</button>'
				);
			}
		} else {
			$data = array();
		}
		$new_data = array(
			'data' => $data
		);
		echo json_encode($new_data);
	}

	public function add()
	{
		$data = array(
			'nama' => $this->input->post('nama'),
			'alamat' => $this->input->post('alamat'),
		);
		if ($this->toko_model->create($data)) {
			echo json_encode('sukses');
		}
	}

	public function delete()
	{
		$id = $this->input->post('id');
		if ($this->toko_model->delete($id)) {
			echo json_encode('sukses');
		}
	}

	public function edit()
	{
		$id = $this->input->post('id');
		$data = array(
			'nama' => $this->input->post('nama'),
			'alamat' => $this->input->post('alamat'),
		);
		if ($this->toko_model->update($id,$data)) {
			echo json_encode('sukses');
		}
	}

	public function detail()
	{
		$id = $this->input->post('id');
		$toko = $this->toko_model->detail($id);
		if (count($toko) > 0) {
			echo json_encode($toko);
		}
	}
}
