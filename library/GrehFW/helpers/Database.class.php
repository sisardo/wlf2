<?php

/**
 * Responsável por todo acesso ao bando de dados e execução de suas funções
 */
class Database {

    /**
     * endereço do serviço do banco de dados
     * @var string 
     */
    private $host = null;

    /**
     * nome do usuário que vai acessar o banco de dados
     * @var string 
     */
    private $user = null;

    /**
     * senha necessária para acessar o banco de dados
     * @var string 
     */
    private $pass = null;

    /**
     * nome do banco de dados que vai ser utilizado
     * @var string
     */
    private $bd_db = null;

    /**
     * codificação
     * @var string 
     */
    private $charset = null;

    /**
     * link para conexão do banco de dados
     * @var type 
     */
    private $_con = '';

    /**
     * Instância da classe Database
     * @var Database 
     */
    private static $_instance = null;

    /**
     * Retorna uma instância de Database, caso não exista é criada
     * 
     * @return Database
     */
    public static function getInstance() {
        if (self::$_instance == null)
            self::$_instance = new self();
        return self::$_instance;
    }

    /**
     * Array contendo as informações necessárias para o acesso ao banco de dados 
     * bem como o tipo de codificação que será usado
     * @param array $config
     */
    public function init() {
        $filename = SYSTEM_PATH . 'configs/db.php';
        if (file_exists($filename)) {
            include $filename;
            try {
                $this->host = $db_config['BD_HOST'];
                $this->user = $db_config['BD_USER'];
                $this->pass = $db_config['BD_PASS'];
                $this->bd_db = $db_config['BD_DB'];
                $this->charset = $db_config['BD_CHARSET'];
            } catch (Exception $exc) {
                echo $exc->getTraceAsString('Configuração inválida!');
            }
        } else {
            exit(var_dump("Crie o arquivo de configuração do banco de dados ($filename)"));
        }
    }

    /**
     * Conecta ao banco de dados e define $this->_con como link para conexão
     * 
     * @return boolean true se foi conectado com sucesso
     * @throws Exception
     */
    public function connect() {
        if ($this->host == null) {
            throw new Exception('Banco de dados não configurado!');
        }
        $this->_con = mysql_connect($this->host, $this->user, $this->pass);
        if ($this->_con && mysql_select_db($this->bd_db, $this->_con)) {
            mysql_query("SET character_set_results = '" . $this->charset . "', character_set_client = '" . $this->charset . "', character_set_connection = '" . $this->charset . "', character_set_database = '" . $this->charset . "', character_set_server = '" . $this->charset . "'", $this->_con);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Retorna link para conexão
     * 
     * @return type
     */
    public function con() {
        if (!empty($this->_con)) {
            return $this->_con;
        }
    }

    /**
     * Fecha conexão com o banco de dados
     */
    public function close() {
        if (!empty($this->_con)) {
            mysql_close($this->_con);
        }
    }

    /**
     * Limpa a string para que seja usada pelo banco de dados de forma segura
     * 
     * options:
     * - 'string' string: string a ser limpa
     * - 'boolean' connect: informa se é necessário a conexão com o banco de dados
     * 
     * @param array $options 
     * @return string string segura
     */
    public function anti_injection($options = array()) {
        $default = array(
            'string' => '',
            'connect' => true
        );

        $options = (object) array_merge($default, $options);
//        if ($conect)
//            $this->connect();
//        $string = mysql_real_escape_string($string);
        $string = str_replace("'", "\'", $options->string);
//        if ($conect)
//            $this->close();
//        $string = preg_replace("/(from|select|insert|delete|where|drop table|show tables|#|\*|--|\\\\)/i", "", $string);
//        $string = trim($string);
//        $string = strip_tags($string);
//        $string = addslashes($string);
        return $string;
    }

    /**
     * Prepara a string para uso do banco de dados, caso se um string ele faz um
     * anti-injection
     * 
     * options: 
     * - 'string' string: string a ser preparada
     * - 'boolean' connect: flag que informa se será necessário a conexão com o banco
     * 
     * @param array $options
     * @return string
     */
    public function string_clear($options = array()) {
        $default = array(
            'string' => '',
            'connect' => false
        );

        $options = (object) array_merge($default, $options);

        if (is_numeric($options->string) || $options->string == 'NOW()') {
            return $options->string;
        } elseif (empty($options->string)) {
            return 'NULL';
        } else {
            return "'" . $this->anti_injection(array('string' => $options->string, 'connect' => $options->connect)) . "'";
        }
    }

    /**
     *  Deixa o array em um formato strindo para inserção no banco de dados
     */
    private function data_clear($set) {
        $dados = '';

        foreach ($set as $key => $value) {
            $dados .= $key . '=' . $this->string_clear(array('string' => $value, 'connect' => false));
            $dados = $dados . ',';
        }
        unset($key, $value);
        $dados = preg_replace('/,$/', '', $dados);

        return $dados;
    }

    /**
     * Insere array de valores em uma linha do banco e retorna o id da linha inserida
     * ou false caso haja falha
     * 
     * options:
     * - 'string' table: nome da tabela
     * - 'array' set: dados para inserção na tabela
     * - 'boolean' debug: caso true mostra a string da consulta
     * 
     * @param type $options
     * @return mixed
     */
    public function insert($options = array()) {
        $default = array(
            'table' => '',
            'set' => array(),
            'debug' => false
        );

        $options = (object) array_merge($default, $options);

        $this->connect();

        $query = "INSERT INTO $options->table SET " . $this->data_clear($options->set);
        if ($options->debug) {
            exit(var_dump($query));
        }

        if (mysql_query($query, $this->_con)) {
            $result = mysql_insert_id($this->_con);
        } else {
            $result = false;
        }

        $this->close();

        return $result;
    }

    /**
     * ESSA FUNÇÃO PRECISA SER CORRIGIDA
     * 
     * Altera valores de uma linha da tabela
     * 
     * options:
     * - 'string' table: nome da tabela
     * - 'array' condition: combinação id e valor do próprio ou outras condições no mesmo padrão (ex.: array('id_cli',2))
     * - 'array' set: dados que devem ser alterados no registro
     * - 'boolean' debug: caso true mostra a string da consulta
     *  
     * @param array $options opções
     * @return mixed número de linha afetadas ou false caso haja erro
     */
    public function update($options = array()) {
        $default = array(
            'table' => '',
            'set' => array(),
            'condition' => NULL,
            'debug' => false
        );

        $options = (object) array_merge($default, $options);

        $condition = $this->formatCondition($options->condition);

        $this->connect();

        $query = "UPDATE $options->table SET " . $this->data_clear($options->set) . "$condition";

        if ($options->debug) {
            exit(var_dump($query));
        }

        if (mysql_query($query, $this->_con)) {
            $return = mysql_affected_rows();
        } else {
            $return = false;
        }

        $this->close();

        return $return;
    }

    /**
     * Deleta registro(s) do bando de dados
     * 
     * options:
     * - 'array' condition: combinação de condições para a clausula delete
     * - 'string' table: tabela alvo
     * - 'boolean' debug: caso true retorna a query desejada
     */
    public function delete($options = array()) {

        $default = array(
            'condition' => NULL,
            'table' => NULL,
            'debug' => false
        );
        $options = (object) array_merge($default, $options);

        $condition = $this->formatCondition($options->condition);

        //$query = "DELETE FROM $table WHERE $key IN ( $value )";
        $query = "DELETE FROM $options->table$condition";

        if ($options->debug == true) {
            exit(var_dump($query));
        }

        $this->connect();

        if (mysql_query($query, $this->_con))
            $return = mysql_affected_rows();
        else
            $return = false;

        $this->close();

        return $return;
    }

    /**
     * Faz um select simples na tabela desejada
     * 
     * options:
     * - 'string' table: nome da tabela
     * - '?' condition: ?
     * - 'array' columns: colunas a serem selecionadas
     * - 'int' limit: quantidade de linha máxima que a consulta de retornar
     * - '?' join: ?
     * - '?' order: ?
     * - 'boolean' debug: caso true mostra a string da consulta
     * 
     * @param string $options opções
     * @return mixed resultado da consulta
     */
    public function select($options) {

        $default = array(
            'table' => NULL,
            'condition' => NULL,
            'columns' => NULL,
            'limit' => NULL,
            'join' => NULL,
            'order' => NULL,
            'debug' => false
        );

        $options = (object) array_merge($default, $options);

        if ($options->table == NULL)
            throw new Exception('Database::select without $options->table');

        $condition = $this->formatCondition($options->condition);

        if ($options->order == NULL) {
            $order = '';
        } else {
            if (is_array($options->order)) {
                $options->order = implode(',', $options->order);
            }
            $order = ' ORDER BY ' . $options->order;
        }

        if ($options->limit == NULL) {
            $limit = '';
        } else {
            $limit = ' LIMIT ' . $options->limit;
        }

        if ($options->join == NULL) {
            $join = '';
        } else {
            $join = " $options->join";
        }

        if ($options->columns == NULL || !is_array($options->columns)) {
            $columns_aux = '*';
        } else {
            $columns_aux = '';
            foreach ($options->columns as $value) {
                $columns_aux .= $value . ',';
            }
            $columns_aux = substr($columns_aux, 0, -1);
        }
        $sql = "SELECT $columns_aux FROM $options->table" . $join . $condition . $order . $limit;

        if ($options->debug == true)
            exit($sql);

        return $this->query(array('sql' => $sql));
    }

    /**
     * Faz uma consulta escrita pelo usuário ao banco de dados
     * 
     * options: 
     * - 'string' sql: consulta a ser feita
     * - 'boolean' debug: se true interrompe a execução e mostra a consulta
     * - 'boolean' connect: verifica a necessidade de conexão ao banco de dados
     * 
     * @param array $options opções
     * @return type 
     */
    public function query($options) {

        $default = array(
            'sql' => NULL,
            'debug' => FALSE,
            'connect' => TRUE
        );

        $options = (object) array_merge($default, $options);

        if ($options->sql === NULL)
            return false;

        if ($options->debug == true) {
            exit($options->sql);
        }

        if ($options->connect) {
            $this->connect();
        }
        $result = mysql_query($options->sql, $this->_con);
        if ($options->connect) {
            $this->close();
        }
        return $result;
    }

    /**
     * FUNÇÃO PRECISA SER CORRIGIDA
     */
    public function num_rows($result) {
        return mysql_num_rows($result);
    }

    /**
     * FUNÇÃO PRECISA SER CORRIGIDA
     */
    public function fetch($result, $padrao = false) {
        if ($padrao) {
            return mysql_fetch_array($result);
        } else {
            return mysql_fetch_array($result, MYSQL_ASSOC);
        }
    }

    /**
     * FUNÇÃO PRECISA SER CORRIGIDA
     */
    public function fetchOne($result) {
        if ($result) {
            $val = mysql_fetch_array($result, MYSQL_NUM);
            return $val[0];
        }
    }

    /**
     * FUNÇÃO PRECISA SER CORRIGIDA
     */
    public function fetchAll($result, $object = false, $className = null) {
        if ($result) {
            $all = array();
            if ($object) {
                while ($row = $this->fetchObject($result, $className)) {
                    $all[] = $row;
                }
            } else {
                while ($row = mysql_fetch_assoc($result)) {
                    $all[] = $row;
                }
            }
            return $all;
        }
    }

    /**
     * FUNÇÃO PRECISA SER CORRIGIDA
     */
    public function fetchPairs($result) {
        if ($result) {
            $all = array();
            while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
                $all[$row[0]] = $row[1];
            }
            return $all;
        }
    }

    /**
     * ?????
     */
    public function result($options = array()) {
        $defaults = array(
            'result' => NULL,
            'row' => 0,
            'field' => NULL
        );
        $options = (object) array_merge($defaults, $options);
        if ($this->num_rows($options->result)) {
            if ($options->field === NULL) {
                return mysql_result($options->result, $options->row);
            } else {
                return mysql_result($options->result, $options->row, $options->field);
            }
        }
    }

    /**
     * FUNÇÃO PRECISA SER CORRIGIDA
     * 
     * Volta o ponteiro interno para o início
     * 
     * @param type $result Resource mysql
     */
    public function reset($result) {
        mysql_data_seek($result, 0);
    }

    /**
     * Retorna o ultimo id inserido no banco
     */
    public function last_insert_id() {
        return mysql_insert_id();
    }

    /**
     * FUNÇÃO PRECISA SER CORRIGIDA
     */
    public function fetchObject($result, $className = null) {
        return mysql_fetch_object($result, $className);
    }

    private function formatCondition($condition) {
        if ($condition == NULL) {
            $stringCondition = '';
        } else {
            if (is_array($condition)) {
                $stringCondition = array();
                foreach ($condition as $key => $value) {
                    if ($value === null) {
                        $separator = ' IS ';
                        $value = 'NULL';
                    } elseif (is_array($value)) {
                        $separator = ' IN ';
                        foreach ($value as $keyValue => $val) {
                            $value[$keyValue] = $this->string_clear(array('string' => $val, 'connect' => true));
                        }
                        $value = '(' . implode(',', $value) . ')';
                    } else {
                        $separator = '=';
                        $value = $this->string_clear(array('string' => $value, 'connect' => true));
                    }
                    array_push($stringCondition, "$key$separator$value");
                }
                $stringCondition = implode(' AND ', $stringCondition);
            } else if (is_string($condition)) {
                $stringCondition = $condition;
            }
            $stringCondition = ' WHERE ' . $stringCondition;
        }
        return $stringCondition;
    }

}

?>
