<?php defined('BASEPATH') or exit('No direct script access allowed');
class User_model extends CI_Model
{
	protected $user = "ak_data_system_user";
	protected $level = "ak_data_system_level";

	public function get_list_data()
	{
		$this->datatables->select('user_id, level_nama, user_nama, user_login, last_login');
		$this->datatables->from($this->user);
		$this->datatables->where($this->user . '.deleted', FALSE);
		$this->datatables->where($this->user . '.user_id!=', 1);
		$this->datatables->join($this->level, $this->level . '.level_id=' . $this->user . '.level_id');
		$this->datatables->add_column('view', "<a id='edit' class='text-primary' data='$1' style='cursor:pointer'><i class='fa fa-edit'></i></a> | <a id='hapus' class='text-danger' data='$1' style='cursor:pointer'><i class='fa fa-trash'></i></a>", "user_id");
		return $this->datatables->generate();
	}

	public function simpan($data)
	{
		return $this->db->insert($this->user, $data);
	}

	public function get_data()
	{
		return $this->db->where($this->user . '.deleted', false)->where($this->user . '.user_id', $this->input->post('user_id'))->get($this->user)->row();
	}

	public function edit($data)
	{
		return $this->db->where($this->user . '.deleted', false)->where($this->user . '.user_id', $this->input->post('user_id'))->update($this->user, $data);
	}

	public function hapus($data)
	{
		return $this->db->where($this->user . '.user_id', $this->input->post('user_id'))->update($this->user, $data);
	}

	public function options($src)
	{
		$opt = $this->db->like('user_nama', $src, 'both')->where('deleted', FALSE)->or_where('user_id', $src)->get($this->user)->result();

		$data = array();
		foreach ($opt as $opt) {
			$data[] = array("id" => $opt->user_id, "text" => $opt->user_nama);
		}

		return $data;
	}
}
