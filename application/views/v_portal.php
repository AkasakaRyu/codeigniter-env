<div class="d-flex align-items-center" style="min-height: 100vh">
	<div class="col-12 p-0">
		<section class="login-block">
			<div class="container">
				<div class="row h-100">
					<div class="col-md-4 login-sec">
						<h2 class="text-center mb-2">Login</h2>
						<p class="text-center" style="font-size:small">Silahkan Anda login disini jika sudah memiliki username dan password</p>
						<form id="Frm" class="login-form" method="post">
							<div class="row">
								<div class="col-lg-12">
									<div class="form-group">
										<label for="user_login" class="text-uppercase">Username</label>
										<input type="text" name="user_login" id="user_login" class="form-control" autocomplete="off" required="true">
									</div>
								</div>
								<div class="col-lg-12">
									<div class="form-group">
										<label for="user_pass" class="text-uppercase">Password / PIN</label>
										<input type="password" name="user_pass" id="user_pass" class="form-control" autocomplete="off" required="true">
									</div>
								</div>
								<div class="col-lg-12">
									<button type="submit" class="btn btn-login btn-block">Submit</button>
								</div>
							</div>
						</form><br />
						<div class="text-center" style="font-size:small">
							Anda bisa masuk dengan menggunakan link dari platform
							<a href="<?= create_github_url() ?>" class="text-dark"><i class="fab fa-github"></i> Github</a>,
							<a href="<?= create_google_url() ?>" class="text-success"><i class="fab fa-google"></i> Google</a> dan
							<a href="<?= create_twitter_url() ?>" class="text-info"><i class="fab fa-twitter"></i> Twitter</a>
						</div>
						<div class="text-center mt-1" style="font-size:small">Crafted with <i class="fa fa-heart text-danger"></i> by <a href="<?= $this->m->get_sysinfo()->info_devs_url; ?>" target="_blank"><?= $this->m->get_sysinfo()->info_devs; ?></a></div>
					</div>
					<div class="col-md-8 banner-sec d-none d-sm-block">
						<div class="banner-text rounded-right p-5">
							<h3><?= $this->m->get_sysinfo()->info_name; ?> <br /><small><?= $this->m->get_instansi()->instansi_nama ?></small></h3>
							<p>Silahkan mengisi formulir di samping menggunakan username dan password yang telah di berikan oleh administrator atau anda buat sebelumnya.</p>
						</div>
					</div>
				</div>
		</section>
	</div>
</div>
<script>
	$("#Frm").submit(function(e) {
		e.preventDefault();
		var form = $(this);
		var url = "<?= base_url('portal/proses_login') ?>";
		$.ajax({
			type: "POST",
			url: url,
			data: form.serialize(),
			beforeSend: function() {
				Pace.restart();
			},
			success: function(data) {
				var response = JSON.parse(data);
				Toast.fire({
					text: response.pesan,
					icon: response.kode
				}).then(function() {
					refreshPage(response.kode);
				});
			},
			error: function(xhr, httpStatusMessage, customErrorMessage) {
				$.toast({
					text: "Terjadi kesalahan! " + xhr.status + " " + xhr.statusText,
					bgColor: '#011627'
				});
			}
		})
	})

	function refreshPage(response) {
		if (response == "success") {
			window.location.reload();
		}
	}
</script>