<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Cetak</title>
	<style>
		.label-produk{
			background: #eee;
			padding: 0.4rem 0.7rem;
			border-radius: 0.9rem;
			margin-right: 0.2rem;
			font-size: 0.9rem;
			margin-bottom: 0.5rem;
			display: inline-flex;
		}

		.label-produk strong{
			margin-left: 0.2rem;
		}
		
		.my-table{
			border-collapse: collapse;
		}

		.my-table tr th{
			border: 1px solid white;
			padding: 10px;
			color: #f5f5f5;
			background: #3498db;
		}

		.my-table tr > td{
			border: 1px solid #dedede;
			padding: 10px 10px 5px;
		}

		@media print {
			.my-table tr th, .my-table tr td{
				color: black;
				border: 1px solid black;
				padding: 10px;
			}	
		}
	</style>
</head>
<body>
	<div style="width: 80%; margin: auto;">
		<br>
		<center>
			<h2 style="font-weight:bold;">
				<?php echo $this->session->userdata('toko')["nama"]; ?> | Laporan Stok Masuk<br>
			</h2>
			<?php echo $this->session->userdata('toko')["alamat"]; ?><br><br>
			<hr><br/>
			<table width="100%" class="my-table">
				<tr>
					<th>No</th>
					<th>Tanggal</th>
					<th>Barcode</th>
					<th>Nama Produk</th>
					<th>Jumlah</th>
					<th>Keterangan</th>
					<th>Supplier</th>
				</tr>
				<?php $no = 1; foreach ($transaksi as $key): ?>
					<tr>
						<td><?=$no++ ?></td>
						<td><?=$key["tanggal"] ?></td>
						<td><?=$key["barcode"] ?></td>
						<td><?=$key["nama_produk"] ?></td>
						<td><?=$key["jumlah"] ?></td>
						<td><?=$key["keterangan"] ?></td>
						<td><?=$key["supplier"] ?></td>
					</tr>
				<?php endforeach ?>
			</table>
		</center>
	</div>
	<script>
		window.print()
	</script>
</body>
</html>