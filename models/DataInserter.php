<?php
/**
 * Created by Raquel Baldavira on PhpStorm.
 * DS Aplicativos for dlg
 * Date: 08/01/2018
 * Time: 15:47
 */

/**
 * Class DataInserter
 * Responsável pela manipulação dos dados recebidos diretamente no banco de dados MySQL.
 */
class DataInserter extends CI_Model {

    /**
     * Recebe os dados carregados
     * @var array
     */
    private $spreadsheet;

    /**
     * DataInserter constructor.
     * Carrega a library Dao
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->library('util_libs/Dao');
    }

    /**
     * Distribui os dados recebidos para as funções de inserção
     * @param array $data
     */
    public function put($data) {
//        Define os dados recebidos em atributo
        $this->spreadsheet = $data;

        foreach ($data as $key => $value) {
            if ($value[0] == null) {
                return;
            }

            $role_id = $this->insertRole($value);
            $agreement_id = $this->insertAgreement($value);
            $user_id = $this->insertUser($value, $role_id);
            $this->insertShift($value, $user_id);
            $this->insertUserAgreement($user_id, $agreement_id);
        }
    }

    /**
     * Localiza o índice do label indicado, dentro da primeira linha da coluna
     * @param string $label
     * @return false|int|string
     */
    private function getColumn($label) {
        return array_search($label, $this->spreadsheet[0]);
    }

    /**
     * Insere ou atualiza os cargos
     * @param array $value
     * @return int
     */
    private function insertRole($value) {
        $nameKey = $this->getColumn('nomecargo');
        $item['nome'] = $value[$nameKey];
        return $this->dao->insert_or_update($item, 'funcao');
    }

    /**
     * Insere ou atualiza os contratos
     * @param array $value
     * @return int
     */
    private function insertAgreement($value) {
        $codeKey = $this->getColumn('codcli');
        $nameKey = $this->getColumn('nomecli');
        $item['numero_contrato'] = $value[$codeKey];
        $item['nome_empresa'] = $value[$nameKey];
        return $this->dao->insert_or_update($item, 'contrato');
    }

    /**
     * Insere ou atualiza os usuários
     * @param array $value
     * @param $role_id
     * @return int
     */
    private function insertUser($value, $role_id) {
        $this->load->model('auth/Ion_auth_model', 'ionauth');
        $item['re'] = $value[$this->getColumn('codvigil')];
        $item['name'] = $value[$this->getColumn('nomevigil')];
        $item['password'] = $this->ionauth->hash_password($value[$this->getColumn('codvigil')]);
        $item['funcao_id'] = $role_id;
        $item['supervisor'] = $value[$this->getColumn('nomegestor')];
        return $this->dao->insert_or_update($item, 'users');
    }

    /**
     * Insere ou atualiza os expedientes
     * @param array $value
     * @param int $user_id
     */
    private function insertShift($value, $user_id) {
        $item['usuario_id'] = $user_id;
        $item['inicio'] = $value[$this->getColumn('hr_entrada')];
        $item['fim'] = $value[$this->getColumn('hr_saida')];
        $this->dao->insert_or_update($item, 'expediente');
    }

    /**
     * Insere ou atualiza os usuários dos contratos
     * @param int $user_id
     * @param int $agreement_id
     */
    private function insertUserAgreement($user_id, $agreement_id) {
        $item['usuario_id'] = $user_id;
        $item['contrato_id'] = $agreement_id;
        $this->dao->insert_os_update($item, 'contrato_funcionario');
    }

}