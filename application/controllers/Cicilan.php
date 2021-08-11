<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cicilan extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		if ($this->session->userdata('status') !== 'login' ) {
			redirect('/');
		}
		$this->load->model('cicilan_model');
	}

	public function index()
	{
		$this->load->view('cicilan');
	}

	public function read()
	{
		header('Content-type: application/json');
		if ($this->cicilan_model->read()->num_rows() > 0) {
			foreach ($this->cicilan_model->read()->result() as $cicilan) {
				$data[] = array(
					'nota' => $cicilan->nota,
					'pelanggan' => $cicilan->pelanggan,
					'total_bayar' => $cicilan->total_bayar,
					'hutang' => $cicilan->hutang,
					'status' => $cicilan->status,
					'action' => '<button class="btn btn-sm btn-success" onclick="edit('.$cicilan->id.')">Detail Pembayaran</button>'
				);
			}
		} else {
			$data = array();
		}
		$cicilan = array(
			'data' => $data
		);
		echo json_encode($cicilan);
	}

	public function get_cicilan()
	{
		$id = !empty($this->input->post('id'))? $this->input->post('id') : !empty($this->input->get('id'))? $this->input->get('id') : null ;
		$cicilan = $this->cicilan_model->detail($id);

		echo json_encode($cicilan);
	}

}

/* End of file cicilan.php */
/* Location: ./application/controllers/cicilan.php */