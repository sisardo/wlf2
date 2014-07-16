<?php

/**
 * Gerencia as instâncias das classe Helpers
 * 
 * @author Angreh (angreh@gmail.com)
 * @version 2.0
 */
class Helpers {

    /**
     * Instância da classe Helpers
     * @var Helpers 
     */
    private static $_instance = null;

    /**
     * Retorna uma instância de Helpers, caso não exista é criada
     * 
     * @return Helpers
     */
    public static function getInstance() {
        if (self::$_instance == null)
            self::$_instance = new self();
        return self::$_instance;
    }
    
    /**
     * Retorna uma instência de AutoLoader, para mais informações olhar o método 
     * _getHelperInstance. Para mais informações sobre a classe AutoLoader olhar
     * a classe em helpers/AutoLoader.class.php
     * 
     * @return AutoLoader
     */
    public function AutoLoader() {
        return $this->_getHelperInstance(__FUNCTION__);
    }    
    
    /**
     * Retorna uma instência de Database, para mais informações olhar o método 
     * _getHelperInstance. Para mais informações sobre a classe Database olhar
     * a classe em helpers/Database.class.php
     * 
     * @return Database
     */
    public function Database() {
        return $this->_getHelperInstance(__FUNCTION__);
    }

    /**
     * Retorna uma instência de Request, para mais informações olhar o método 
     * _getHelperInstance. Para mais informações sobre a classe Request olhar
     * a classe em helpers/Request.class.php
     * 
     * @return Request
     */
    public function Request() {
        return $this->_getHelperInstance(__FUNCTION__);
    }
    
    /**
     * Retorna uma instência de StringFormat, para mais informações olhar o método 
     * _getHelperInstance. Para mais informações sobre a classe StringFormat olhar
     * a classe em helpers/StringFormat.class.php
     * 
     * @return StringFormat
     */
    public function StringFormat() {
        return $this->_getHelperInstance(__FUNCTION__);
    }
    
    /**
     * Inclui o arquivo do helper solitidado através de $helpername e chama o 
     * método getInstance(), que retorna uma instância da classe solicitada
     * 
     * @param string $helperName nome da classe que vai ser instanciada
     * @return type instância da classe solicitada
     */
    private function _getHelperInstance($helperName) {

        require_once LIBRARY_PATH . "GrehFW/helpers/$helperName.class.php";
        return $helperName::getInstance();
    }


}
