<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transaksi extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		if ($this->session->userdata('status') !== 'login' ) {
			redirect('/');
		}
		$this->load->model(['transaksi_model', 'cicilan_model']);
	}

	public function index()
	{
		$this->load->view('transaksi');
	}

	public function read()
	{
		header('Content-type: application/json');
		$transaksi = array(
			'data' => $this->transaksi_model->read()
		);
		echo json_encode($transaksi);
	}

	public function add()
	{
		$produk = json_decode($this->input->post('produk'));
		$tanggal = new DateTime($this->input->post('tanggal'));
		$data = [
			'tanggal' => $tanggal->format('Y-m-d H:i:s'),
			'total_bayar' => $this->input->post('total_bayar'),
			'jumlah_uang' => $this->input->post('jumlah_uang'),
			'pelanggan' => $this->input->post('pelanggan'),
			'nota' => $this->input->post('nota'),
			'kasir' => $this->session->userdata('id'),
			'item' => []
		];

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

	public function delete()
	{
		$id = $this->input->post('id');
		if ($this->transaksi_model->delete($id)) {
			echo json_encode('sukses');
		}
	}

	public function cetak($id)
	{
		$produk = $this->transaksi_model->getAll($id);
		$tanggal = new DateTime($produk->tanggal);
		$barcode = explode(',', $produk->barcode);
		$qty = explode(',', $produk->qty);

		$produk->tanggal = $tanggal->format('d m Y H:i:s');

		$dataProduk = $this->transaksi_model->getName($barcode);
		foreach ($dataProduk as $key => $value) {
			$value->total = $qty[$key];
			$value->harga = $value->harga * $qty[$key];
		}

		$data = array(
			'nota' => $produk->nota,
			'tanggal' => $produk->tanggal,
			'produk' => $dataProduk,
			'total' => $produk->total_bayar,
			'bayar' => $produk->jumlah_uang,
			'kembalian' => $produk->jumlah_uang - $produk->total_bayar,
			'kasir' => $produk->kasir
		);
		$this->load->view('cetak', $data);
	}

	public function penjualan_bulan()
	{
		header('Content-type: application/json');
		$day = $this->input->post('day');
		foreach ($day as $key => $value) {
			$now = date($day[$value].' m Y');
			if ($qty = $this->transaksi_model->penjualanBulan($now) !== []) {
				$data[] = array_sum($this->transaksi_model->penjualanBulan($now));
			} else {
				$data[] = 0;
			}
		}
		echo json_encode($data);
	}

	public function transaksi_hari()
	{
		header('Content-type: application/json');
		$now = date('d m Y');
		$total = $this->transaksi_model->transaksiHari($now);
		echo json_encode($total->total == null ? 0 : $total);
		// echo json_encode($total);
	}

	public function transaksi_terakhir($value='')
	{
		header('Content-type: application/json');
		$now = date('d m Y');
		foreach ($this->transaksi_model->transaksiTerakhir($now) as $key) {
			$total = explode(',', $key);
		}
		echo json_encode($total->total == null ? 0 : $total);
		// echo json_encode($total);
	}

}

/* End of file Transaksi.php */
/* Location: ./application/controllers/Transaksi.php */