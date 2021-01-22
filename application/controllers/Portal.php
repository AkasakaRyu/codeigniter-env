<?php defined('BASEPATH') or exit('No direct script access allowed');

use Abraham\TwitterOAuth\TwitterOAuth;

class Portal extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$isLogin = $this->session->userdata('LoggedIn');
		if ($isLogin) {
			redirect('dashboard');
		} else {
			$this->load->model('Portal_model', 'm');
		}
	}

	public function index()
	{
		$data['Title'] = "Portal";
		$data['Template'] = "templates/public";
		$data['Components'] = array(
			'main' => "/v_public",
			'header' => $data['Template'] . "/components/v_header",
			'footer' => $data['Template'] . "/components/v_footer",
			'content' => "v_portal"
		);
		$this->load->view('v_main', $data);
	}

	public function proses_login()
	{
		$isValid = $this->m->cek_validasi();
		if ($isValid) {
			$data = $this->m->get_user($this->input->post('user_login'));
			$this->simpan_session($data);
			$pesan = array(
				'warning' => 'Akses diterima!',
				'kode' => 'success',
				'pesan' => 'Berhasil masuk ke dalam sistem!'
			);
		} else {
			$pesan = array(
				'warning' => 'Akses ditolak!',
				'kode' => 'error',
				'pesan' => 'Gagal masuk ke dalam sistem!'
			);
		}
		echo json_encode($pesan);
	}

	public function simpan_session($data)
	{
		$session = array(
			'id' => $data['user_id'],
			'level_id' => $data['level_id'],
			'nama' => $data['user_nama'],
			'level' => $data['level_nama'],
			'AppInfo' => $this->m->get_sysinfo()->info_name,
			'DevInfo' => $this->m->get_sysinfo()->info_devs,
			'UrlDev' => $this->m->get_sysinfo()->info_devs_url,
			'LoggedIn' => TRUE
		);
		$this->session->set_userdata($session);
		$this->m->update_login($data['user_login']);
	}

	public function check_users($data)
	{
		$user = $this->m->search_users($data);
		if (empty($user)) {
			$this->registrasi($data);
			$user = $this->m->search_users($data);
		}
		$this->simpan_session($user);
	}

	public function registrasi($data)
	{
		$user_data = array(
			'user_nama' => $data['user_nama'],
			'user_pass' => password_hash(rand(1000, 9999), PASSWORD_BCRYPT),
			'created_by' => $data['user_platform'],
			'created_date' => date('Y-m-d H:i:s')
		);

		if ($data['user_platform'] == "Github") {
			$user_data['user_github'] = $data['user_platform_id'];
			$user_data['user_login'] = uniqid();
		} elseif ($data['user_platform'] == "Google") {
			$user_data['user_google'] = $data['user_platform_id'];
			$user_data['user_login'] = uniqid();
		} elseif ($data['user_platform'] == "Twitter") {
			$user_data['user_twitter'] = $data['user_platform_id'];
			$user_data['user_login'] = uniqid();
		}

		$this->m->simpan($user_data);
	}

	public function github_callback()
	{
		if (!$this->session->userdata('github_auth')) {
			$access_token = get_github_token($this->input->get('code'));
			if (!isset($access_token['error'])) {
				$gh_session = array(
					'github_access_token' => $access_token['access_token'],
					'github_type_token' => $access_token['token_type'],
					'github_auth' => TRUE
				);
				$this->session->set_userdata($gh_session);
				$users_array = json_decode(fetch_github_users($this->session->userdata('github_type_token'), $this->session->userdata('github_access_token')), true);
				// echo json_encode(show_github_data($users_array));
				$this->check_users(show_github_data($users_array));
				redirect('/');
			} else {
				echo json_encode($access_token);
			}
		} else {
			$users_array = json_decode(fetch_github_users($this->session->userdata('github_type_token'), $this->session->userdata('github_access_token')), true);
			// echo json_encode(show_github_data($users_array));
			$this->check_users(show_github_data($users_array));
			redirect('/');
		}
	}

	public function twitter_callback()
	{
		$users = get_twitter_users();
		$users_array = json_decode(json_encode($users), true);
		// echo json_encode(show_twitter_data($users_array));
		$this->check_users(show_twitter_data($users_array));
		redirect('/');
	}

	public function google_callback()
	{
		$access_token = get_google_token($this->input->get('code'));
		if (!$this->session->userdata('google_auth')) {
			if (!isset($access_token['error'])) {
				$go_session = array(
					'google_id_token' => $access_token['id_token'],
					'google_access_token' => $access_token['access_token'],
					'google_type_token' => $access_token['token_type'],
					'google_auth' => TRUE
				);
				$this->session->set_userdata($go_session);
				$users_array = json_decode(json_encode(fetch_google_users($this->session->userdata('google_access_token'))), true);
				// echo json_encode(show_google_data($users_array));
				$this->check_users(show_google_data($users_array));
				redirect('/');
			} else {
				revoke_google_token();
				echo json_encode($access_token);
			}
		} else {
			$users_array = json_decode(json_encode(fetch_google_users($this->session->userdata('google_access_token'))), true);
			// echo json_encode(show_google_data($users_array));
			$this->check_users(show_google_data($users_array));
			redirect('/');
		}
	}
}
