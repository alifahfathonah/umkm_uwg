<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Cetak</title>
	<style>
		.span-diskon{
			margin-left: 5px;
			background: red;
			font-weight: bold;
			color: white;
			padding: 1px 5px;
			font-size: 13px;
			border-radius: 5px;
		}
	</style>
</head>
<body>
	<div style="width: 500px; margin: auto;">
		<br>
		<center>
			<h2 style="font-weight:bold;">
				<?php echo $this->session->userdata('toko')->nama; ?><br>
			</h2>
			<?php echo $this->session->userdata('toko')->alamat; ?><br><br>
			<table width="100%">
				<tr>
					<td><?php echo $nota ?></td>
					<td align="right"><?php echo $tanggal ?></td>
				</tr>
			</table>
			<hr>
			<table width="100%">
				<tr>
					<td width="50%"></td>
					<td width="3%"></td>
					<td width="10%" align="right"></td>
					<td align="right" width="17%"><?php echo $kasir ?></td>
				</tr>
				<?php foreach ($item as $key): ?>
					<tr>
						<td><?php echo $key["nama_produk"] ?> <?php echo !empty($key["diskon"])? '<span class="span-diskon">'.$key["diskon"].'%</span>' : '';  ?></td>
						<td></td>
						<td align="right"><?php echo $key["qty"]." ".$key["satuan"] ?></td>
						<td align="right"><?php echo ($key["harga"] - ($key["harga"] * $key["diskon"] / 100) * $key["qty"]) ?></td>
					</tr>
				<?php endforeach ?>
			</table>
			<hr>
			<table width="100%">
				<tr>
					<td width="76%" align="right">
						Harga Jual
					</td>
					<td width="23%" align="right">
						<?php echo $total_bayar ?>
					</td>
				</tr>
			</table>
			<hr>
			<table width="100%">
				<tr>
					<td width="76%" align="right">
						Total
					</td>
					<td width="23%" align="right">
						<?php echo $total_bayar ?>
					</td>
				</tr>
				<tr>
					<td width="76%" align="right">
						Bayar
					</td>
					<td width="23%" align="right">
						<?php echo $jumlah_uang ?>
					</td>
				</tr>
				<tr>
					<td width="76%" align="right">
						Kembalian
					</td>
					<td width="23%" align="right">
						<?php echo $kembalian ?>
					</td>
				</tr>
			</table>
			<br>
			Terima Kasih <br>
			<?php echo $this->session->userdata('toko')->nama; ?>
		</center>
	</div>
	<script>
		window.print()
	</script>
</body>
</html>