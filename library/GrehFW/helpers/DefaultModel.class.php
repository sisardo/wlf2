<?php

/**
 * Gerencia campos e relações da tabela
 * 
 * @author Angreh <angreh@gmail.com>
 * @version 2.0
 */
class DefaultModel {

    /**
     * Nome da tabela
     * @var string
     */
    public $table;

    /**
     * campo "PRIMARY KEY"
     * @var string
     */
    public $primaryKey = 'id';
    public $prefix = NULL;
    public $relations = array();
    public $references = array();

    /**
     *
     * @var array contem valor do "campos" da tabela
     */
    public $values = array();
    public $limit = NULL;
    public $where = NULL;
    public $order = array();
    public $joins = array();

    public function __construct($table = null) {
        if (!empty($table)) {
            $this->setTable($table);
        }
        $this->init();
    }

    public function init() {
        
    }

    public function checkPrefix($str) {
        if ($this->prefix == NULL || strpos($str, $this->prefix) === 0)
            return $str;
        else
            return $this->prefix . $str;
    }

    public function getPrefix() {
        return $this->prefix;
    }

    /**
     * Seta valor do campo
     * 
     * @param type $name indice da resposta
     * @param type $value valor da resposta
     */
    public function __set($name, $value) {
        if ($name == 'id') {
            $name = $this->primaryKey;
        }
        $name = $this->checkPrefix($name);
        $this->values[$name] = $value;
    }

    /**
     * Busca valor do campo
     * 
     * @param type $name indice do valor
     * @return type retorna o valor do "campo" da tabela ou false caso não exista
     */
    public function __get($name) {
        if ($name == 'id') {
            $name = $this->primaryKey;
        }
        $name = $this->checkPrefix($name);
        if (isset($this->values[$name]))
            return $this->values[$name];
        else
            return false;
    }

    /**
     * Limpa os 'campos' do objeto
     */
    public function reset() {
        $this->values = array();
    }

    /**
     * Retorna todos as calusulas joins como string e limpa o 'join' da classe
     * 
     * @return string calusulas JOIN montadas 
     */
    public function getJoins() {
        if (empty($this->joins)) {
            return NULL;
        } else {
            $joins = $this->joins;
            $this->joins = array();
            return implode(' ', $joins);
        }
    }

    /**
     * Retorna o nome do campo primary key da tabela
     * 
     * @return string nome do campo primary key 
     */
    public function getPrimaryKey() {
        return $this->checkPrefix($this->primaryKey);
    }

    /**
     * Retorna o nome da tabela cujo a classe é responsavel
     * 
     * @return string no da tabela
     */
    public function getTable() {
        return $this->table;
    }

    /**
     * Define o nome da tabela cuja classe é responsavel
     * @return DefaultModel instância da classe para fluent interface
     */
    public function setTable($table) {
        $this->table = $table;
        return $this;
    }

    /**
     * Adiciona relação com outra tabela
     * 
     * @param string $relation nome usado para a relação
     * @param string $table_model nome do modelo com qual se relaciona
     * @param string $key nome do campo no modelo que se referencia a tabela
     * @return DefaultModel fluent interface
     */
    public function setRelation($relation, $table_model, $key) {
        $this->relations[$relation] = array($table_model, $key);
        return $this;
    }

    /**
     * Adiciona referencia a outra tabela
     * 
     * @param string $table_model modelo no qual buscar a referencia
     * @param string $key nome do campo da tabela que referencia o modelo
     * @return DefaultModel fluent interface
     */
    public function setReference($table_model, $key) {
        $this->references[$table_model] = $key;
        return $this;
    }

    /**
     * Cria clausula JOIN para ser usado no metodo get
     * 
     * @param string $tableModel modelo que sera usado para o JOIN
     * @param type $idTableModel undefined
     * @param type $idThis undefined
     * @param type $type tipo de JOIN
     * @return DefaultModel 
     */
    public function join($tableModel, $idTableModel = NULL, $idThis = NULL, $type = 'INNER') {

        if (isset($this->references[$tableModel])) {
            $tableModelClass = new $tableModel();
            array_push($this->joins, "$type JOIN " . $tableModelClass->getTable() . " ON $this->table." . $this->checkPrefix($this->references[$tableModel]) . "=" . $tableModelClass->getTable() . "." . $tableModelClass->getPrimaryKey());
        } else {
            $tableModelClass = new $tableModel();
            $idTableModel = empty($idTableModel) ? $tableModelClass->getPrimaryKey() : $idTableModel;
            $idThis = empty($idThis) ? $this->getPrimaryKey() : $idThis;
            array_push($this->joins, strtoupper($type) . " JOIN " . $tableModelClass->getTable() . " ON $this->table." . $idThis . "=" . $tableModelClass->getTable() . "." . $idTableModel);
        }
        return $this;
    }

    /**
     *
     * @param array $data dados para serem inseridos nas propriedades
     */
    public function setFrom($data) {
        $this->values = $this->formatValuesFrom(array(
            'data' => $data,
            'onlyKeys' => false
        ));
    }

    /**
     * Formata os dados e coloca no padrão usado pela tabela
     * 
     * @param array $data dados para serem inseridos nas propriedades
     */
    public function formatValuesFrom($options) {
        $default = array(
            'data' => NULL,
            'onlyKeys' => false
        );
        $options = (object) array_merge($default, $options);

        if ($options->onlyKeys) {
            $newData = array();
            foreach ($options->data as $key => $value) {
                if ($value == 'id') {
                    $value = $this->primaryKey;
                }
                $value = $this->checkPrefix($value);
                $newData[$key] = $value;
            }
        } else {
            $newData = array();
            foreach ($options->data as $key => $value) {
                if ($key == 'id')
                    $key = $this->primaryKey;
                $key = $this->checkPrefix($key);
                $newData[$key] = $value;
            }
        }
        return $newData;
    }

    public function getInfo($key = null) {
        if ($key == null) {
            $infos = array(
                'name' => $this->_getName(),
                'primary_key' => $this->_getPrimaryKey(),
                'cols' => $this->_getCols()
            );
            return $infos;
        } else {
            switch ($key) {
                case 'name':
                    return $this->_getName();
                    break;
                case 'primary_key':
                    return $this->_getPrimaryKey();
                    break;
                case 'cols':
                    return $this->_getCols();
                    break;
                default:
                    throw new Exception("Key $key doesn't exist!");
            }
        }
    }

    private function _getName() {
        return $this->table;
    }

    private function _getCols() {
        $result = Helpers::getInstance()->Database()->query(array('sql' => "SHOW COLUMNS FROM " . $this->table));
        if ($result && mysql_num_rows($result) > 0) {
            $cols = array();
            while ($row = mysql_fetch_assoc($result)) {
                array_push($cols, $row);
            }
            return $cols;
        } else {
            return false;
        }
    }

    private function _getPrimaryKey() {
        if ($this->primaryKey != null) {
            return $this->prefix . $this->primaryKey;
        } else {
            $cols = $this->_getCols();
            $primary_key = array();
            foreach ($cols as $row) {
                foreach ($row as $field => $type) {
                    if ($type == 'PRI') {
                        return $row['Field'];
                    }
                }
            }
        }
    }

    public function toArray($object = null) {
        $object = $object == null ? $this->values : $object;
        if (!is_object($object) && !is_array($object)) {
            return $object;
        }
        if (is_object($object)) {
            $object = $object->values;
        }
        return array_map(array($this, 'toArray'), $object);
    }

}

?>