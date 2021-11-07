<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stok_masuk extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		if ($this->session->userdata('status') !== 'login' ) {
			redirect('/');
		}
		$this->load->model(['stok_masuk_model', 'produk_model']);
	}

	public function index()
	{
		$d["list_produk"] = $this->produk_model->getListProduk();
		$this->load->view('stok_masuk', $d);
	}

	public function read()
	{
		header('Content-type: application/json');
		if ($this->stok_masuk_model->read()->num_rows() > 0) {
			foreach ($this->stok_masuk_model->read()->result() as $stok_masuk) {
				$tanggal = new DateTime($stok_masuk->tanggal);
				$data[] = array(
					'tanggal' => $tanggal->format('d-m-Y H:i:s'),
					'barcode' => $stok_masuk->barcode,
					'nama_produk' => $stok_masuk->nama_produk,
					'jumlah' => $stok_masuk->jumlah,
					'harga' => $stok_masuk->harga,
					'keterangan' => $stok_masuk->keterangan,
					'action' => '<button class="btn btn-sm btn-success" onclick="edit('.$stok_masuk->id.')"><i class="fas fa-edit"></i></button> <button class="btn btn-sm btn-danger" onclick="remove('.$stok_masuk->id.')"><i class="fas fa-trash"></i></button>'
				);
			}
		} else {
			$data = array();
		}
		$stok_masuk = array(
			'data' => $data
		);
		echo json_encode($stok_masuk);
	}

	public function add()
	{
		$id = $this->input->post('barcode');
		$jumlah = $this->input->post('jumlah');
		$harga = $this->input->post('harga');
		$stok = $this->stok_masuk_model->getStok($id)->stok;
		$rumus = max($stok + $jumlah,0);
		$addStok = $this->stok_masuk_model->addStok($id, $rumus);
		if ($addStok) {
			$tanggal = new DateTime($this->input->post('tanggal'));
			$data = array(
				'tanggal' => $tanggal->format('Y-m-d H:i:s'),
				'barcode' => $id,
				'jumlah' => $jumlah,
				'harga' => $harga,
				'keterangan' => $this->input->post('keterangan'),
				'supplier' => $this->input->post('supplier')
			);
			if ($this->stok_masuk_model->create($data)) {
				echo json_encode('sukses');
			}
		}
	}

	public function delete()
	{
		$id = $this->input->post('id');
		if ($this->produk_model->delete($id)) {
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
		
		if ($this->produk_model->update($id,$data)) {
			echo json_encode('sukses');
		}
	}

	public function get_stok_masuk()
	{
		header('Content-type: application/json');
		$id = !empty($this->input->post('id'))? $this->input->post('id') : !empty($this->input->get('id'))? $this->input->get('id') : null ;
		
		$produk = $this->produk_model->getProduk($id);
		echo json_encode($produk);
	}

	public function get_barcode()
	{
		$barcode = $this->input->post('barcode');
		$kategori = $this->stok_masuk_model->getKategori($id);
		if ($kategori->row()) {
			echo json_encode($kategori->row());
		}
	}

	public function laporan()
	{
		header('Content-type: application/json');
		if ($this->stok_masuk_model->laporan()->num_rows() > 0) {
			foreach ($this->stok_masuk_model->laporan()->result() as $stok_masuk) {
				$tanggal = new DateTime($stok_masuk->tanggal);
				$data[] = array(
					'tanggal' => $tanggal->format('d-m-Y H:i:s'),
					'barcode' => $stok_masuk->barcode,
					'nama_produk' => $stok_masuk->nama_produk,
					'jumlah' => $stok_masuk->jumlah,
					'harga' => $stok_masuk->harga,
					'keterangan' => $stok_masuk->keterangan,
					'supplier' => $stok_masuk->supplier
				);
			}
		} else {
			$data = array();
		}
		$stok_masuk = array(
			'data' => $data
		);
		echo json_encode($stok_masuk);
	}

	public function cetak_all(){
		if ($this->stok_masuk_model->laporan()->num_rows() > 0) {
			foreach ($this->stok_masuk_model->laporan()->result() as $stok_masuk) {
				$tanggal = new DateTime($stok_masuk->tanggal);
				$data[] = array(
					'tanggal' => $tanggal->format('d-m-Y H:i:s'),
					'barcode' => $stok_masuk->barcode,
					'nama_produk' => $stok_masuk->nama_produk,
					'jumlah' => $stok_masuk->jumlah,
					'harga' => $stok_masuk->harga,
					'keterangan' => $stok_masuk->keterangan,
					'supplier' => $stok_masuk->supplier
				);
			}
		} else {
			$data = array();
		}

		$d["transaksi"] = $data;
		$this->load->view('cetak/stok_masuk_all', $d);
	}

	public function stok_hari()
	{
		header('Content-type: application/json');
		$now = date('d m Y');
		$total = $this->stok_masuk_model->stokHari($now);
		echo json_encode($total->total == null ? 0 : $total);
	}

}

/* End of file Stok_masuk.php */
/* Location: ./application/controllers/Stok_masuk.php */