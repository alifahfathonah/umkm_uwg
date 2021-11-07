<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stok_keluar extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		if ($this->session->userdata('status') !== 'login' ) {
			redirect('/');
		}
		$this->load->model('stok_keluar_model');
	}

	public function index()
	{
		$this->load->view('stok_keluar');
	}

	public function read()
	{
		header('Content-type: application/json');
		if ($this->stok_keluar_model->read()->num_rows() > 0) {
			foreach ($this->stok_keluar_model->read()->result() as $stok_keluar) {
				$tanggal = new DateTime($stok_keluar->tanggal);
				$data[] = array(
					'tanggal' => $tanggal->format('d-m-Y H:i:s'),
					'barcode' => $stok_keluar->barcode,
					'nama_produk' => $stok_keluar->nama_produk,
					'jumlah' => $stok_keluar->jumlah,
					'keterangan' => $stok_keluar->keterangan,
					'action' => '<button class="btn btn-sm btn-info" onclick="retur('.$stok_keluar->id.')">Pengembalian</button> <button class="btn btn-sm btn-success" onclick="edit('.$stok_keluar->id.')"><i class="fas fa-edit"></i></button> <button class="btn btn-sm btn-danger" onclick="remove('.$stok_keluar->id.')"><i class="fas fa-trash"></i></button>'
				);
			}
		} else {
			$data = array();
		}
		$stok_keluar = array(
			'data' => $data
		);
		echo json_encode($stok_keluar);
	}

	public function add()
	{
		$id = $this->input->post('barcode');
		$jumlah = $this->input->post('jumlah');
		$stok = $this->stok_keluar_model->getStok($id)->stok;
		$rumus = max($stok - $jumlah,0);
		$addStok = $this->stok_keluar_model->addStok($id, $rumus);
		if ($addStok) {
			$tanggal = new DateTime($this->input->post('tanggal'));
			$data = array(
				'tanggal' => $tanggal->format('Y-m-d H:i:s'),
				'barcode' => $id,
				'jumlah' => $jumlah,
				'keterangan' => $this->input->post('keterangan'),
			);
			if ($this->stok_keluar_model->create($data)) {
				echo json_encode('sukses');
			}
		}
	}

	public function delete()
	{
		$id = $this->input->post('id');
		if ($this->stok_keluar_model->delete($id)) {
			echo json_encode('sukses');
		}
	}

	public function edit()
	{
		$id = $this->input->post('id');
		$data = array(
			'barcode' => $this->input->post('barcode'),
			'nama_produk' => $this->input->post('nama_produk'),
			'satuan' => $this->input->post('satuan'),
			'kategori' => $this->input->post('kategori'),
			'stok' => $this->input->post('stok')
		);

		$tipe_pelanggan = $this->pelanggan_model->get_tipe();
		if(!empty($tipe_pelanggan)){
			foreach($tipe_pelanggan as $key => $value){
				$pelanggan = strtolower($value->nama);
				$data["pelanggan"][] = [
					"tipe" => $value->id,
					"id" => $this->input->post("id_".$pelanggan),
					"harga" => $this->input->post("harga_".$pelanggan),
					"diskon" => $this->input->post("diskon_".$pelanggan),
				];
			}
		}
		
		if ($this->stok_keluar_model->update($id,$data)) {
			echo json_encode('sukses');
		}
	}

	public function get_stok_keluar()
	{
		header('Content-type: application/json');
		$id = !empty($this->input->post('id'))? $this->input->post('id') : !empty($this->input->get('id'))? $this->input->get('id') : null ;
		
		$stok_keluar = $this->stok_keluar_model->getStokKeluar($id);
		echo json_encode($stok_keluar);
	}

	public function get_barcode()
	{
		$barcode = $this->input->post('barcode');
		$kategori = $this->stok_keluar_model->getKategori($id);
		if ($kategori->row()) {
			echo json_encode($kategori->row());
		}
	}

	public function retur()
	{
		$id = $this->input->post('id');
		if ($this->stok_keluar_model->retur($id)) {
			echo json_encode('sukses');
		}
	}

}

/* End of file Stok_keluar.php */
/* Location: ./application/controllers/Stok_keluar.php */