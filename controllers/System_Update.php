<?php
/**
 * Created by Raquel Baldavira on PhpStorm.
 * DS Aplicativos for system_update
 * Date: 08/01/2018
 * Time: 15:44
 */

class System_Update extends MY_Controller {

    /**
     * System_Update constructor.
     * Carregamento de leitor de documentos Excel
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('system_update/FileReader', 'reader');
    }

    /**
     * Página de carregamento de arquivo
     */
    public function index() {
        $data['info'] = $this->reader->send();
        $html = $this->load->view('system_update/index', $data, true);
        $this->show_public($html);
    }


}