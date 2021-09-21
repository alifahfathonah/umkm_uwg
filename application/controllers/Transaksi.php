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
			'total_bayar' => $this->input->post('total_bayar') - (!empty($this->input->post('ongkir'))? $this->input->post('ongkir') : 0),
			'jumlah_uang' => $this->input->post('jumlah_uang'),
			'ongkir' => $this->input->post('ongkir'),
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

	public function cetak_all(){
		$d["transaksi"] = $this->transaksi_model->read();
		$this->load->view('cetak/transaksi_all', $d);
	}

	public function cetak($id)
	{
		$transaksi = $this->transaksi_model->getPrintTranskaksi($id);

		$tanggal = new DateTime($transaksi["tanggal"]);
		$transaksi["tanggal"] = $tanggal->format('d m Y H:i:s');
		$this->load->view('cetak/transaksi', $transaksi);
	}

	public function penjualan_bulan()
	{
		header('Content-type: application/json');
		$day = $this->input->post('day');
		$tahun = !empty($this->input->post("year"))? $this->input->post("year") : date("Y") ;
		$bulan = !empty($this->input->post("month"))? $this->input->post("month") : date("m") ;

		foreach ($day as $key => $value) {
			$now = $tahun . "-" . $bulan . "-" . $day[$value];
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
		$now = date('Y-m');
		$lastTrans = $this->transaksi_model->transaksiTerakhir($now);
		echo json_encode(!empty($lastTrans) ? $lastTrans->total : 0);
	}

}

/* End of file Transaksi.php */
/* Location: ./application/controllers/Transaksi.php */