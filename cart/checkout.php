<script>
	function updateCardNumber() {
		var selectedBank = document.getElementById("card_bank").value;
		var cardNumberField = document.getElementById("card_number");
		var ownerField = document.getElementById("owner");

		switch (selectedBank) {
			case "BRI":
				// Ganti dengan nomor rekening Bank BRI yang sesuai
				cardNumberField.value = "639801038041534";
				ownerField.value = "Laili Rizky Amalia";
				break;
			case "DANA":
				// Ganti dengan nomor rekening DANA yang sesuai
				cardNumberField.value = "085608676184";
				ownerField.value = "Laili Rizky Amalia";
				break;
				// Tambahkan case lain jika diperlukan untuk bank lainnya
		}
	}
</script>

<div class="container">
	<div class="row">


		<div class="col-lg-12">
			<h2 class="text-center text1">Konfirmasi Pembayaran</h2>
		</div>
		<?php
		$query = mysqli_query($conn, "SELECT * FROM members WHERE member_id = '" . $member . "'");
		$data = mysqli_fetch_array($query);

		if (!$query) {
			printf("Error: %s\n", mysqli_error($conn));
			exit();
		}

		$error = false;
		$A = $B = $name = $email = $address = $city = $state = $postcode = $phone = $cardbank = $cardnumber = $owner = $pay = "";
		$nameErr = $emailErr = $addressErr = $cityErr = $stateErr = $postcodeErr = $phoneErr = $cardbankErr = $cardnumberErr = $ownerErr = $payErr = "";

		if (isset($_POST['checkout'])) {
			if ($_SERVER['REQUEST_METHOD'] == "POST") {
				if (empty($_POST['name'])) {
					$error = true;
					$nameErr = "Masukkan isi nama pertama Anda";
				} else {
					$name = $_POST['name'];
					if (!preg_match("/^[a-zA-Z .\-']*$/", $_POST['name'])) {
						$error = true;
						$nameErr = "Isi nama harus menggunakan huruf, karakter dan spasi";
					}
				}

				if (empty($_POST['email'])) {
					$error = true;
					$emailErr = "Masukkan isi alamat email Anda";
				} else {
					$email = $_POST['email'];
					if (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9.]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/", $_POST['email'])) {
						$error = true;
						$emailErr = "Isi alamat email dengan benar";
					}
				}

				if (empty($_POST['address'])) {
					$error = true;
					$addressErr = "Masukkan isi alamat Anda";
				} else {
					$address = $_POST['address'];
				}

				if (empty($_POST['city'])) {
					$error = true;
					$cityErr = "Masukkan isi nama kota atau kabupaten";
				} else {
					$city = $_POST['city'];
					if (!preg_match("/^[a-zA-Z -'&]*$/", $_POST['city'])) {
						$error = true;
						$cityErr = "Isi nama kota atau kabupaten harus menggunakan huruf, karakter dan spasi";
					}
				}

				if (empty($_POST['state'])) {
					$error = true;
					$stateErr = "Masukkan isi provinsi";
				} else {
					$state = $_POST['state'];
					if (!preg_match("/^[a-zA-Z ,']*$/", $_POST['state'])) {
						$error = true;
						$stateErr = "Isi provinsi harus menggunakan huruf, karakter dan spasi";
					}
				}

				if (empty($_POST['postcode'])) {
					$error = true;
					$postcodeErr = "Masukkan isi kode pos";
				} else {
					$postcode = $_POST['postcode'];
					if (!is_numeric($postcode)) {
						$error = true;
						$postcodeErr = "Isi kode pos hanya menggunakan angka";
					}
				}

				if (empty($_POST['phone'])) {
					$error = true;
					$phoneErr = "Masukkan isi nomor telepon Anda";
				} else {
					$phone = $_POST['phone'];
				}

				if (trim($_POST['card_bank']) == "blank") {
					$error = true;
					$cardbankErr = "Pilih salah satu nama bank asal";
				} else {
					$cardbank = $_POST['card_bank'];
					if ($cardbank == "BRI") {
						$A = "selected";
					} elseif ($cardbank == "DANA") {
						$B = "selected";
					} else {
						echo '
						<option value="BRI">BRI</option>
						<option value="DANA">DANA</option>';
					}
				}

				if (empty($_POST['card_number'])) {
					$error = true;
					$cardnumberErr = "Masukkan isi nomor rekening Anda";
				} else {
					$cardnumber = $_POST['card_number'];
					if (!is_numeric($cardnumber)) {
						$error = true;
						$cardnumberErr = "Isi nomor rekening hanya menggunakan angka";
					}
				}


				if (empty($_POST['owner'])) {
					$error = true;
					$ownerErr = "Masukkan isi nama pemilik Anda";
				} else {
					$owner = $_POST['owner'];
					if (!preg_match("/^[a-zA-Z .\-']*$/", $_POST['owner'])) {
						$error = true;
						$ownerErr = "Isi nama pemilik harus menggunakan huruf dan spasi";
					}
				}

				if (empty($_POST['pay'])) {
					$error = true;
					$payErr = "Masukkan isi total pembayaran";
				} else {
					$pay = $_POST['pay'];
					if (!is_numeric($pay)) {
						$error = true;
						$payErr = "Isi total pembayaran hanya menggunakan angka";
					}
				}

				if ($_POST['pay'] != $_POST['hidden_total']) {
					echo '<div class="modal fade" id="totals" role="dialog" aria-hidden="true">
									<div class="modal-dialog">
										<div class="modal-content">
											<div class="modal-header">
												<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
												<h4 class="modal-title">Pemberitahuan</h4>
											</div>
											<div class="modal-body">
												<h3>Isi total tersebut tidak boleh di kurangi. Mohon silakan di bayar harus sama dengan total sementara.</h3>
											</div>
											<div class="modal-footer">
												<a href="../index.php?p=checkout" class="btn btn-info">Ya</a>
											</div>
										</div>
									</div>
								</div>';
				}

				if (!$error) {
					date_default_timezone_set('Asia/Jakarta');
					$id = autoNumber('order_id', 'orders');
					$regdate = date('Y-m-d');
					$regtime = date('G:i:s');

					$result = mysqli_query($conn, "INSERT INTO orders VALUES('" . $id . "','" . $member . "','" . $owner . "','" . $cardbank . "','" . $cardnumber . "','PAID','" . $pay . "','" . $regdate . "','" . $regtime . "','PENDING','" . $regdate . "','" . $regtime . "')");
					if (!$result) {
						die('Invalid query:' . mysqli_error($conn));
					}
					$order_id = $id;
					$order_detail = '';
					$_SESSION["order_id"] = $order_id;
					foreach ($_SESSION["cart"] as $keys => $values) {
						$order_detail = "INSERT INTO order_detail VALUES (NULL,'" . $order_id . "','" . $values['item_img'] . "','" . $values['product_id'] . "','" . $values['item_name'] . "','" . $values['size'] . "','" . $values['qty'] . "','" . $values['disc'] . "','" . $values['price'] . "')";
						if (mysqli_query($conn, $order_detail)) {
							unset($_SESSION['cart']);
							echo "<script>document.location = '../index.php?p=order'; </script>";
						}
						mysqli_query($conn, "UPDATE items SET stock = stock -'" . $values['qty'] . "' WHERE item_id = '" . $values['product_id'] . "'");
						$qry = mysqli_query($conn, "SELECT * FROM items WHERE stock='0' AND item_id = '" . $values['product_id'] . "'");
						$data = mysqli_fetch_array($qry);
						if ($data['available']) {
							mysqli_query($conn, "UPDATE items SET available = 'Habis' WHERE item_id = '" . $values['product_id'] . "'");
						}
					}
					mysqli_query($conn, "UPDATE members SET fullname = '" . $name . "', address = '" . $address . "', city = '" . $city . "', state = '" . $state . "', zip_code = '" . $postcode . "', phone = '" . $phone . "', email = '" . $email . "' WHERE member_id = '" . $member . "'");
				}
			}
		}
		?>
		<div class="col-md-8 center-block">
			<form action="../index.php?p=checkout" class="form-horizontal" method="POST">
				<legend>Data Diri</legend>
				<div class="form-group">
					<label class="col-md-2 control-label">Nama Lengkap</label>
					<div class="col-md-9">
						<input type="text" name="name" class="form-control" value="<?php echo isset($_POST['name']) ? $_POST['name'] : $data['fullname']; ?>">
						<span class="text-danger msg-error"><?php echo $nameErr; ?></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-2 control-label">Alamat Email</label>
					<div class="col-md-9">
						<input type="text" name="email" class="form-control" value="<?php echo isset($_POST['email']) ? $_POST['email'] : $data['email']; ?>">
						<span>Example: yourname@mail.com</span>
						<span class="text-danger msg-error"><?php echo $emailErr; ?></span>
					</div>
				</div>
				<legend>Detail Alamat</legend>
				<div class="form-group">
					<label class="col-md-2 control-label">Alamat</label>
					<div class="col-md-9">
						<input type="text" name="address" class="form-control" value="<?php echo isset($_POST['address']) ? $_POST['address'] : $data['address']; ?>">
						<span class="text-danger msg-error"><?php echo $addressErr; ?></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-2 control-label">Kota / Kabupaten</label>
					<div class="col-md-9">
						<input type="text" name="city" class="form-control" value="<?php echo isset($_POST['city']) ? $_POST['city'] : $data['city']; ?>">
						<span class="text-danger msg-error"><?php echo $cityErr; ?></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-2 control-label">Provinsi</label>
					<div class="col-md-9">
						<input type="text" name="state" class="form-control" value="<?php echo isset($_POST['state']) ? $_POST['state'] : $data['state']; ?>">
						<span class="text-danger msg-error"><?php echo $stateErr; ?></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-2 control-label">Kode Pos</label>
					<div class="col-md-9">
						<input type="text" name="postcode" class="form-control" value="<?php echo isset($_POST['postcode']) ? $_POST['postcode'] : $data['zip_code']; ?>">
						<span class="text-danger msg-error"><?php echo $postcodeErr; ?></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-2 control-label">Nomor Telepon</label>
					<div class="col-md-9">
						<input type="text" name="phone" class="form-control" maxlength="14" value="<?php echo isset($_POST['phone']) ? $_POST['phone'] : $data['phone']; ?>">
						<span class="text-danger msg-error"><?php echo $phoneErr; ?></span>
					</div>
				</div>
				<legend>Metode Pembayaran</legend>

				<div class="form-group">
					<label class="col-md-2 control-label">Bank Tujuan</label>
					<div class="col-md-3">
						<select name="card_bank" id="card_bank" class="form-control" onchange="updateCardNumber()">
							<option value="blank">-- Pilih Bank Tujuan--</option>
							<option value="BRI" <?php echo $A; ?>>BRI</option>
							<option value="DANA" <?php echo $B; ?>>DANA</option>
						</select>
						<span class="text-danger msg-error"><?php echo $cardbankErr; ?></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-2 control-label">Nomor Rekening</label>
					<div class="col-md-9">
						<input type="text" name="card_number" id="card_number" class="form-control" value="<?php echo isset($cardnumber) ? $cardnumber : ' '; ?>">
						<span class="text-danger msg-error"><?php echo $cardnumberErr; ?></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-2 control-label">Nama Pemilik Rekening</label>
					<div class="col-md-9">
						<input type="text" name="owner" id="owner" class="form-control" value="<?php echo isset($owner) ? $owner : ' '; ?>">
						<span class="text-danger msg-error"><?php echo $ownerErr; ?></span>
					</div>
				</div>
				<legend>Ringkasan Pembayaran</legend>
				<div class="form-group">
					<label class="col-md-2 control-label">Total transaksi</label>
					<div class="col-md-3">
						<label class="control-label" style="padding-left: 0;">
							<?php
							if (!empty($_SESSION["cart"])) {
								$total = 0;
								foreach ($_SESSION["cart"] as $keys => $values) {
									$totalDisc = $values['price'] - ($values['price'] * $values['disc'] / 100);
									$total = $total + ($values['qty'] * $totalDisc);
								}
							}
							echo 'Rp ' . number_format($total, 0, ".", ".");
							?>
						</label>
						<input type="hidden" name="hidden_total" value="<?php echo ($total); ?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-2 control-label">Jumlah Bayar Sekarang</label>
					<div class="col-md-2">
						<input type="text" name="pay" class="form-control" value="<?php echo $total; ?>">
						<span class="text-danger msg-error"><?php echo $payErr; ?></span>
					</div>
				</div>
				<center>
					<div class="form-group">
						<button type="submit" class="btn btn-warning" name="checkout">Bayar Sekarang</button>
						<a href="../index.php"><button type="button" class="btn btn-link">Batal</button></a>
					</div>
				</center>
			</form>
		</div>
	</div>
</div>