<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Jurnal_laba_rugi extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		if ($this->session->userdata('status') !== 'login' ) {
			redirect('/');
		}
		$this->load->model('jurnal_laba_rugi_model');
	}

	public function index()
	{
		$this->load->view('jurnal_laba_rugi');
	}

	public function read()
	{
		header('Content-type: application/json');
        $jurnal = $this->jurnal_laba_rugi_model->read();

		if (count($jurnal) > 0) {
            $saldo = 0;
            $debet = 0;
            $kredit = 0;
            
			foreach ($jurnal as $key => $value) {
                $debet = $value["debet"];
                $kredit = $value["kredit"];
                $saldo += $debet - $kredit;
                $value["saldo"] = $saldo;
				$data[] = $value;
			}
		} else {
			$data = [];
		}
		echo json_encode(["data"=>$data]);
	}
}