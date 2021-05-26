<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		if (!isset($_SESSION['role_id'])) redirect();
		if ($_SESSION['role_id'] != 2) redirect(); //admin only
		$this->load->model('admin_model');
	}

	public function index()
	{
		if (isset($_SESSION['tab'])) {
			$data['tab'] = $_SESSION['tab'];
		} else {
			$data['tab'] = 1;
		}
		$data['style'] = $this->load->view('include/style', NULL, TRUE);
		$data['script'] = $this->load->view('include/script', NULL, TRUE);
		$data['navbar'] = $this->load->view('template/navbar', NULL, TRUE);
		$data['footer'] = $this->load->view('template/footer', NULL, TRUE);
		// table
		$data['tableCSS'] = $this->load->view('datatables/style', NULL, TRUE);
		$data['tableJS'] = $this->load->view('datatables/script', NULL, TRUE);
		$temp['dbdata'] = $this->admin_model->getUser();
		$temp['hoteldb'] = $this->admin_model->getHotel();
		$data['user'] = $this->load->view('datatables/userdb', $temp, TRUE);
		$data['hotel'] = $this->load->view('datatables/hoteldb', $temp, TRUE);

		$this->load->view('pages/admin', $data);

		unset($_SESSION['error']);
		unset($_SESSION['msg']);
	}

	public function update_user($uid, $role)
	{
		if ($role == 2) {
			$this->session->set_flashdata('error', 'Data Admin tidak dapat diubah melalui web!');
		} else {
			$this->db->set('name', $_POST['name']);
			$this->db->set('email', $_POST['email']);
			$this->db->set('date', $_POST['date']);
			$this->db->set('phone', $_POST['phone']);
			$this->db->set('role_id', $_POST['role_id']);
			$this->db->where('user_id', $uid);
			$this->db->update('user_login');
		}
		$_SESSION['tab'] = 1;
		redirect('admin');
	}

	public function delete_user($uid, $role, $path, $subpath, $file)
	{
		if ($role == 2) {
			$this->session->set_flashdata('error', 'Data Admin tidak dapat diubah melalui web!');
		} else {
			$this->db->where('user_id', $uid);
			$this->db->delete('user_login');

			if (strcmp('defaultprofile.png', $file) != 0) {
				$deldir = realpath(APPPATH . '../' . $path . '/' . $subpath . '/' . $file); //hack
				if (file_exists($deldir)) {
					unlink($deldir);
				}
				unset($deldir);
			}
		}
		$_SESSION['tab'] = 1;
		redirect('admin');
	}

	public function update_picture($id, $role)
	{
		if ($role == 2) {
			$this->session->set_flashdata('error', 'Data Admin tidak dapat diubah melalui admin page!');
		} else {
			$data['style'] = $this->load->view('include/style', NULL, TRUE);
			$data['script'] = $this->load->view('include/script', NULL, TRUE);
			$data['navbar'] = $this->load->view('template/navbar', NULL, TRUE);
			$data['footer'] = $this->load->view('template/footer', NULL, TRUE);

			$this->db->where('user_id', $id);
			$query = $this->db->get('user_login');
			$data['db'] = $query->row_array();
			$this->load->view('pages/admin/updatepic', $data);
		}
	}
	public function picture_reupload(){
		$_SESSION['tab'] = 1;
	}

	public function delete_picture($uid, $role, $path, $subpath, $file)
	{
		if ($role == 2) {
			$this->session->set_flashdata('error', 'Data Admin tidak dapat diubah melalui web!');
		} else {
			if (strcmp('defaultprofile.png', $file) != 0) {
				$deldir = realpath(APPPATH . '../' . $path . '/' . $subpath . '/' . $file); //hack
				if (file_exists($deldir)) {
					unlink($deldir);
				}
				unset($deldir);
				$this->db->set('picture', 'assets/images/defaultprofile.png');
				$this->db->where('user_id', $uid);
				$this->db->update('user_login');
			}
		}
		$_SESSION['tab'] = 1;
		redirect('admin');
	}

	public function reset_password($uid, $role)
	{
		if ($role == 2) {
			$this->session->set_flashdata('error', 'Data Admin tidak dapat diubah melalui web!');
		} else {
			$temppass = $this->generateRandomString();
			$this->session->set_flashdata('msg', 'Password User baru adalah : ' . $temppass);
			$this->db->set('password', hash("sha256", $temppass));
			$this->db->where('user_id', $uid);
			$this->db->update('user_login');
		}
		$_SESSION['tab'] = 1;
		redirect('admin');
	}

	public function new_user()
	{
		$this->Userform_rules();
		$this->load->model('register_model');
		$_POST['password'] = $this->generateRandomString();
		$result = $this->form_validation->run();
		if ($result) { //form input validation
			if ($this->register_model->oldUser($_POST['email'])) { //check duplicate user
				$uploadlog = $this->register_model->register(NULL);
				if (is_null($uploadlog)) {
					$this->session->set_flashdata('msg', 'Password User baru adalah : ' . $_POST['password']);
				} else {
					$this->session->set_flashdata('error', $uploadlog);
				}
			} else {
				$this->session->set_flashdata('error', 'Email sudah digunakan');
			};
		} else {
			$this->session->set_flashdata('error', $result);
		}
		$_SESSION['tab'] = 1;
		redirect('admin');
	}

	//untuk password sementara
	function generateRandomString($length = 10)
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}

	public function Userform_rules()
	{
		$this->form_validation->set_rules(
			'name',
			'name',
			'required',
			array('required' => 'Name Cant Be Empty !')
		);

		$this->form_validation->set_rules(
			'email',
			'email',
			'required',
			array('required' => 'Email Cant Be Empty !')
		);

		$this->form_validation->set_rules(
			'password',
			'password',
			'required',
			array('required' => 'Password Cant Be Empty !')
		);

		$this->form_validation->set_rules(
			'date',
			'date',
			'required',
			array('required' => 'Date Cant Be Empty !')
		);

		$this->form_validation->set_rules(
			'phone',
			'phone',
			'required',
			array('required' => 'Phone Number Cant Be Empty !')
		);

		$this->form_validation->set_rules(
			'role_id',
			'role_id',
			'required',
			array('required' => 'Phone Number Cant Be Empty !')
		);
	}
}
