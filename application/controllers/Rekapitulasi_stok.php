<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rekapitulasi_stok extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		if ($this->session->userdata('status') !== 'login' ) {
			redirect('/');
		}
		$this->load->model('rekapitulasi_stok_model');
	}

	public function index()
	{
		$this->load->view('rekapitulasi_stok');
	}

	public function read()
	{
		header('Content-type: application/json');
        $data = $this->rekapitulasi_stok_model->read();
		echo json_encode(["data"=>$data]);
	}
}