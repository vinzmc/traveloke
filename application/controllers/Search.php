<?php
class Search extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('search_data');
    }

    public function index()
    {
        $data['style'] = $this->load->view('include/style', NULL, TRUE);
        $data['script'] = $this->load->view('include/script', NULL, TRUE);
        $data['navbar'] = $this->load->view('template/navbar', NULL, TRUE);
        $data['footer'] = $this->load->view('template/footer', NULL, TRUE);

        $this->load->view('pages/home', $data);
    }

    public function result()
    {
        $data['search'] = $this->search_data->search();
        $this->load->view('search', $data);
    }
}
