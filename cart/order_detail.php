<div id="divToPrint">
	<div class="container">
		<div class="row">
			<div class="col-xs-6">
				<!-- <img class="logo-img" src="../logo/miistore1.png" /> -->
			</div>
			<div class="col-xs-6 text-right" style="margin-top: 20px;">
				<address>
					Jl Peldasaruan RT 008 / RW 002,<br>
					Putat Lor, Gondanglegi
				</address>
			</div>
			<?php
			if (isset($_SESSION['order_id'])) {
				$date_detail = '';
				$cust_detail = '';
				$payment_detail = '';
				$total = 0;
				$query = "SELECT * FROM orders INNER JOIN members ON members.member_id = orders.customer_id WHERE orders.order_id = '" . $_SESSION['order_id'] . "'";
				$result = mysqli_query($conn, $query);
				while ($row = mysqli_fetch_array($result)) {

					$date = '' . $row['creation_date'] . '';
					$date_detail = date('d-m-Y', strtotime($date));

					$time_detail = '' . $row['creation_time'] . '';

					$cust_detail = '
```Nama		: ' . $row['fullname'] . '```
```Alamat	: ' . $row['address'] . '```
```Kota/Kab	: ' . $row['city'] . '```
```Provinsi	: ' . $row['state'] . '```
```Kode Pos	: ' . $row['zip_code'] . '```
```No Aktif	: ' . $row['phone'] . '```
';
					$payment_detail = '
```Nama	: ' . $row['owner_name'] . '```
```Bank	: ' . $row['cardbank_type'] . '```
```Rek.	: ' . $row['card_number'] . '```
';
				}
			}
			?>
			<div class="col-xs-12 text-center">
				<h3>Bukti Konfirmasi Pembayaran</h3>
				<center>
					<h5>Nomor Pemesanan : <?php echo $_SESSION['order_id']; ?></h5>
				</center>
			</div>
			<hr>
		</div>
		<div class="row" style="margin-bottom: 10px;">
			<div class="col-xs-6">
				<!-- <strong>Alamat Pemesanan</strong> -->
				<address>
				</address>
			</div>
			<div class="col-xs-6 text-right">
				<!-- <strong>Tanggal Pemesanan</strong><br /> -->
			</div>
			<div class="col-xs-6 text-right">
				<!-- <strong>Detail Pembayaran</strong><br /> -->
			</div>
		</div>
		<div class="col-lg-12">
			<table class="timetable_sub">
				<thead>
					<tr>
						<th>No</th>
						<th>Produk</th>
						<th>Jumlah</th>
						<th>Diskon</th>
						<th>Harga</th>
						<th>Subtotal</th>
					</tr>
				</thead>
				<?php
				$query = mysqli_query($conn, "SELECT * FROM order_detail WHERE order_id = '" . $_SESSION['order_id'] . "'");
				$no = 1;
				while ($row = mysqli_fetch_array($query)) {
					$totalDisc = $row['price'] - ($row['price'] * $row['disc'] / 100);
					$subtotal = $row['qty'] * $totalDisc;
					$total = $total + $subtotal;
				?>
					<tr>
						<td align="center"><?php echo $no; ?></td>
						<td>
							<div class="table-column-left">
								<img src="../miiadmin/img/<?php echo $row['bgimg']; ?>" class="img-small">
							</div>
							<div style="text-align: center; line-height: 1.5; padding-top: 25px;">
								Kode : <?php echo $row['item_code']; ?><br />
								Nama : <?php echo $row['item_name']; ?><br />
								Size : <?php echo $row['size']; ?><br />
							</div>
						</td>
						<td align="center"><?php echo $row['qty']; ?> Kg</td>
						<td align="center"><?php echo $row['disc']; ?> %</td>
						<td align="center"><?php echo 'Rp ' . number_format($row['price'], 0, ".", "."); ?></td>
						<td align="center"><?php echo 'Rp ' . number_format($subtotal, 0, ".", "."); ?></td>
					</tr>
				<?php
					$no++;
				}
				?>
				<tr>
					<td colspan="5" align="right">Total</td>
					<td align="center"><?php echo 'Rp ' . number_format($total, 0, ".", "."); ?></td>
				</tr>
			</table>
		</div>
	</div>
</div>

<!-- Tautan untuk membuka WhatsApp dengan pesan yang telah dibuat -->
<div class="row">
	<div class="col-xs-12 text-center">
		<?php
		$whatsappMessage = "*--- CEK ONGKOS PENGIRIMAN ---*\n\n" .
			"*Nomor Pemesanan:*\n"
			. $_SESSION['order_id'] . "\n" .
			"*Tanggal Pemesanan:*\n"
			. $date_detail . " " . $time_detail . "\n" .
			"----------------------------------------------------------\n" .
			"*Alamat Pemesanan:*" . $cust_detail .
			"----------------------------------------------------------\n" .
			"*Detail Pembayaran:*" . $payment_detail .
			"----------------------------------------------------------\n" .
			"*Detail Produk:*\n";

		// Loop through produk untuk menambahkannya ke pesan
		$query_produk = mysqli_query($conn, "SELECT * FROM order_detail WHERE order_id = '" . $_SESSION['order_id'] . "'");
		// $whatsappMessage .= "Produk yang dipesan:\n";
		$productNumber = 1;

		while ($row_produk = mysqli_fetch_array($query_produk)) {
			$totalDisc_produk = $row_produk['price'] - ($row_produk['price'] * $row_produk['disc'] / 100);
			$subtotal_produk = $row_produk['qty'] * $totalDisc_produk;

			$whatsappMessage .=
				"Produk #" . $productNumber . ":\n" .
				"Nama    : " . $row_produk['item_name'] . "\n" .
				"Ukuran  : " . $row_produk['size'] . "\n" .
				"Jumlah  : " . $row_produk['qty'] . "kg\n" .
				"Diskon  : " . $row_produk['disc'] . "%\n" .
				"Harga   : Rp " . number_format($row_produk['price'], 0, ".", ".") . "\n" .
				"Subtotal: Rp " . number_format($subtotal_produk, 0, ".", ".") . "\n" .
				"----------------------------------\n";

			// Increment product number
			$productNumber++;
		}

		// The rest of your code...

		// Tambahkan bagian total ke pesan
		$whatsappMessage .=
			"*Total: Rp " . number_format($total, 0, ".", ".") . "*\n\n" .
			"*--- CEK ONGKOS PENGIRIMAN ---*";


		$whatsappLink = "https://wa.me/6282139919501?text=" . urlencode($whatsappMessage);
		?>
		<br>
		<a href="<?php echo $whatsappLink; ?>" target="_blank" class="btn btn-primary">Cek Pengiriman Via WhatsApp</a>
		<br>
	</div>


</div>
</div>