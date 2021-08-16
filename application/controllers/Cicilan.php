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

	public function edit()
	{
		$id = new DateTime($this->input->post('id'));
		$cicilan = json_decode($this->input->post('cicilan'));

		foreach ($produk as $produk) {
			$this->transaksi_model->removeStok($produk->id, ["stok" => $produk->stok, "terjual" => $produk->jumlah]);
			
			$data["item"][] = [
				'produk' => $produk->id,
				'qty' => $produk->jumlah
			];
		}
		
		if ($create = $this->transaksi_model->create($data)) {
			if($data["jumlah_uang"] < $data["total_bayar"]){
				$data_cicilan = [
					"transaksi_id" => $create,
					"hutang" => $data["total_bayar"] - $data["jumlah_uang"],
					"status" => "Belum Lunas"
				];

				$this->cicilan_model->create($data_cicilan);
			}

			echo json_encode($create);
		}else{
			echo json_encode(0);
		}
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