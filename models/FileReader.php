<?php
/**
 * Created by Raquel Baldavira on PhpStorm.
 * DS Aplicativos for system_update
 * Date: 08/01/2018
 * Time: 15:46
 */

/**
 * Class FileReader
 * Responsável pelo carregamento, leitura e conversão de dados de uma planilha do Microsoft Office Excel.
 */
class FileReader extends CI_Model {

    /**
     * FileReader constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('DataInserter', 'inserter');
        $this->load->library('phpexcel/PHPExcel');
    }

    /**
     * Responsável pelo envio de mensagens relacionadas ao sucesso do carregamento
     * @return array
     */
    public function send() {
        $data = array();
        if (sizeof($_POST) > 0) {
            if ($this->uploadFile() == true) {
                $data['color'] = 'success';
                $data['message'] = 'Carregamento concluído com sucesso!';
            }
            else {
                $data['color'] = 'danger';
                $data['message'] = 'Falha ao carregar o arquivo.';
            }
            return $data;
        }
    }

    /**
     * Realiza o carregamento do arquivo em um local específico
     * @return bool
     */
    private function uploadFile() {
        $config['upload_path'] = $this->verifyDirectory();
        $config['allowed_types'] = "*";
        $config['max_size'] = "*";
        $config['max_width'] = "*";
        $config['max_height'] = "*";

        $this->load->library('upload', $config);

        if ($this->upload->do_upload()) {
            $finfo = $this->upload->data();
            if (!$this->isSpreadsheet($finfo['file_name'])) {
                return false;
            }
            $data['uploadInfo'] = $finfo;
            $data['thumbnail_name'] = $finfo['raw_name'] . '_thumb' . $finfo['file_ext'];
            $url_file = $data['uploadInfo']['full_path'];
            if ($this->read($url_file))
                return true;
        }
    }

    private function isSpreadsheet($fileName) {
        $extension = explode('.', $fileName)[1];
        return trim($extension) == 'xls' || $extension == 'xlsx' ? true : false;
    }

    /**
     * Verifica a existência do diretório onde a planilha será carregada.
     * Caso não exista, realiza a criação do diretório.
     * @return string
     */
    private function verifyDirectory() {
        if (!file_exists(FCPATH . '/assets/worksheets/')) {
            mkdir(FCPATH . '/assets/worksheets/');
        }
        return FCPATH . '/assets/worksheets/';
    }

    /**
     * Carrega o arquivo no componente PHPExcel e converte os dados para uma matriz
     * @param file $excelFile
     * @return bool
     */
    private function read($excelFile) {
        $objPHPExcel = PHPExcel_IOFactory::load($excelFile);
        foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
            $arrayData = $worksheet->toArray();
        }
        if ($this->inserter->put($arrayData)) {
            return true;
        }
    }

}