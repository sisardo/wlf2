<?php

/**
 * Responsável pelas operações no banco de dados usando as classes Models
 *
 * @author Angreh
 * @version 2.0
 */
class DefaultMapper {

    /**
     * Modelo (Model) usado para a classe
     * @var DefaultModel
     */
    protected $model;

    public function __construct($model = NULL) {
        if ($model == NULL) {
            // Transforma o nome da classe Mapper e uma Model referente
            // ex.: Evento_Admin_Mapper -> Evento_Admin_Model
            $auxClass = explode('_', get_class($this));
            $auxClass = $auxClass[0] . '_' . $auxClass[1] . '_Model';
            $this->model = new $auxClass();
        } else {
            $this->model = $model;
        }
    }

    /**
     * Insere linha na tabela do Model definido
     * 
     * options:
     * - 'array' set: Valores a serem inserido
     * - 'boolean' reset: Flag para resetar os dados do modelo
     * - 'boolean' debug: caso true retorna a query desejada
     * 
     * @param options
     * @return type o id da linha inserida ou false caso ocorra algum erro
     */
    public function insert($options = array()) {
        $default = array(
            'set' => NULL,
            'reset' => true,
            'debug' => false
        );

        $options = (object) array_merge($default, $options);

        // caso o 'set' de dados tenha sido passado pelos paramêtros ele passa
        // para o modelo definido
        if ($options->set !== NULL) {
            $this->model->setFrom($options->set);
        }

        $set = $this->model->values;
        if ($options->reset) {
            $this->model->reset();
        }

        return Helpers::getInstance()->Database()->insert(array(
                    'table' => $this->model->table,
                    'set' => $set,
                    'debug' => $options->debug
        ));
    }

    /**
     * Edita um registro no banco de dados, para isso acontecer o PRIMARY KEY do
     * registro deve estar contido dentro do 'set' na forma de 'id' ou o nome 
     * real da chave primária
     * 
     * options:
     * - 'array' set: Valores a serem editados, tem que conter a PRIMARY_KEY
     * - 'boolean' reset: Flag para resetar os dados do modelo
     * - 'boolean' debug: caso true retorna a query desejada
     * 
     * @param type $options
     * @return type
     */
    public function update($options = array()) {
        $default = array(
            'set' => NULL,
            'reset' => true,
            'debug' => false
        );
        $options = (object) array_merge($default, $options);


        // caso o 'set' de dados tenha sido passado pelos paramêtros ele passa
        // para o modelo definido
        if ($options->set !== NULL) {
            $this->model->setFrom($options->set);
        }
        $set = $this->model->values;

        // define valor de ID como condição para o método Database->update()
        $PrimaryKeyName = $this->model->getPrimaryKey();
        if ($this->model->$PrimaryKeyName == false) {
            trigger_error('Voce esqueceu de definir o ID do registro que deseja alterar', E_USER_WARNING);
        } else {
            $condition = array($PrimaryKeyName => $this->model->$PrimaryKeyName);
            unset($set[$PrimaryKeyName]);
        }

        if ($options->reset) {
            $this->model->reset();
        }

        return Helpers::getInstance()->Database()->update(array(
                    'condition' => $condition,
                    'table' => $this->model->table,
                    'set' => $set,
                    'debug' => $options->debug
        ));
    }

    /**
     * Edita um ou mais registros no banco de dados, é necessário passar uma 
     * condição 'condition'
     * 
     * options:
     * - 'array' set: Valores a serem inserido, tem que conter a PRIMARY_KEY
     * - 'array' condition: condição para o update
     * - 'boolean' reset: Flag para resetar os dados do modelo
     * - 'boolean' debug: caso true retorna a query desejada
     * 
     * @param type $options
     * @return type
     */
    public function updateCondition($options = array()) {
        $default = array(
            'set' => NULL,
            'condition' => NULL,
            'reset' => true,
            'debug' => false
        );
        $options = (object) array_merge($default, $options);


        // caso o 'set' de dados tenha sido passado pelos paramêtros ele passa
        // para o modelo definido
        if ($options->set !== NULL) {
            $this->model->setFrom($options->set);
        }
        $set = $this->model->values;

        if ($options->reset) {
            $this->model->reset();
        }

        if ($options->condition == NULL) {
            trigger_error('Voce esqueceu de passar uma condicao para esse metodo!', E_USER_WARNING);
        }

        // formata valores para o padrão da tabela
        $options->condition = $this->model->formatValuesFrom(array('data' => $options->condition));

        return Helpers::getInstance()->Database()->update(array(
                    'condition' => $options->condition,
                    'table' => $this->model->table,
                    'set' => $set,
                    'debug' => $options->debug
        ));
    }

    /**
     * Deleta um registro do banco de dados, é necessário passar o id do registro 
     * 
     * options:
     * - 'string' id: id do registro a ser apagado
     * - 'boolean' debug: caso true retorna a query desejada
     * 
     * @param type $options
     * @return type
     */
    public function delete($options = array()) {
        $default = array(
            'id' => NULL,
            'debug' => false
        );
        $options = (object) array_merge($default, $options);

        if ($options->id == NULL) {
            $options->id = $this->model->id;
        }

        if ($options->id == NULL) {
            trigger_error('O id do registro não foi informado.', E_USER_WARNING);
        }

        $condition = $this->model->formatValuesFrom(array(
            'data' => array(
                'id' => $options->id
            )
        ));

        return Helpers::getInstance()->Database()->delete(array(
                    'condition' => $condition,
                    'table' => $this->model->table,
                    'debug' => $options->debug
        ));
    }

    /**
     * Deleta um ou mais registro do banco de dados, é necessário passar um
     * array de condições
     * 
     * options:
     * - 'array' condition: combinação de registros
     * - 'boolean' debug: caso true retorna a query desejada
     * 
     * @param type $options
     * @return type
     */
    public function deleteCondition($options = array()) {
        $default = array(
            'condition' => NULL,
            'debug' => false
        );
        $options = (object) array_merge($default, $options);

        if ($options->condition !== NULL) {
            $this->model->setFrom($options->condition);
        }
        $condition = $this->model->values;

        return Helpers::getInstance()->Database()->delete(array(
                    'condition' => $condition,
                    'table' => $this->model->table,
                    'debug' => $options->debug
        ));
    }

    /**
     * PRECISA SER AJUSTADA AINDA ESSE MÉTODO 
     * a parte de relations, joins e references
     * 
     * Busca valores dentro da tabela e retorna um array com a linha em formato
     *  stdClass
     * 
     * @return array linha do resultado
     */
    public function get($options = array()) {

        $default = array(
            'values' => array(),
            'references' => false,
            'relations' => NULL,
            'columns' => NULL,
            'condition' => NULL,
            'limit' => 10,
            'order' => NULL,
            'debug' => false,
            'reset' => true,
        );

        $options = (object) array_merge($default, $options);

        if ($options->columns != NULL) {
            $options->columns = $this->model->formatValuesFrom(array(
                'data' => $options->columns,
                'onlyKeys' => true
            ));
//            exit(var_dump($options->columns));
        }

        if ($options->order != NULL) {
            $options->order = $this->model->formatValuesFrom(array(
                'data' => $options->order,
                'onlyKeys' => true
            ));
        }

        if (empty($options->condition)) {
            $options->condition = $this->model->values;
        } else {
            $options->condition = $this->model->formatValuesFrom(array('data' => $options->condition));
        }

        if ($options->references) {
            if (sizeof($this->model->references) > 0) {
                foreach ($this->model->references as $key => $value) {
                    $this->model->join($key);
                }
            }
        }

        $joins = $this->model->getJoins();

        $result = Helpers::getInstance()->Database()->select(array(
            'table' => $this->model->table,
            'condition' => $options->condition,
            'limit' => $options->limit,
            'join' => $joins,
            'order' => $options->order,
            'debug' => $options->debug,
            'columns' => $options->columns
        ));

        if (!empty($result) && Helpers::getInstance()->Database()->num_rows($result)) {
            $itens_arr = array();
            while ($item = Helpers::getInstance()->Database()->fetchObject($result, get_class($this->model))) {
                if ($options->relations === true) {
                    if (sizeof($this->model->relations) > 0) {
                        foreach ($this->model->relations as $key => $value) {
                            $ext = new $value[0]();
                            $ext->{$value[1]} = $item->{$this->model->getPrimaryKey()};
                            $item->{$key} = $ext->get(
                                    array(
                                        'references' => $options->references,
                                        'relations' => $options->relations,
                                        'columns' => $options->columns,
                                        'debug' => $options->debug
                                    )
                            );
                        }
                    }
                } else if ($options->relations == 'COUNT') {
                    if (sizeof($this->model->relations) > 0) {
                        foreach ($this->model->relations as $key => $value) {
                            $ext = new $value[0]();
                            $ext->{$value[1]} = $item->{$this->model->getPrimaryKey()};
                            $item->{$key . '_count'} = $ext->getTotalRows();
                        }
                    }
                }
                array_push($itens_arr, $item);
            }
            return $itens_arr;
        } else {
            return false;
        }
    }

    /**
     * Retorna o total de linha da tabela mapeada pelo modelo
     * 
     * @return int total de linhas
     */
    public function getTotalRows() {
        $result = Helpers::getInstance()->Database()->query(array(
            'sql' => "SELECT COUNT(" . $this->model->getPrimaryKey() . ") AS total FROM " . $this->model->table
        ));
        return Helpers::getInstance()->Database()->result(array('result' => $result));
    }

    /* olders */

    public function addList() {
        /* Não aplicado ainda */
    }

}

?>
