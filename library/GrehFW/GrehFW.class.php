<?php

/**
 * Núcleo funcional do CaraFW
 * 
 * @autor Angreh - angreh@gmail.com
 */
class GrehFW {

    //Atalho para reduzir linha de código no index.php
    public function __construct() {
        $this->init();
    }

    /**
     * O Init é a função que faz o framework rodar carregando todas as diretrizes
     * e chamando todas as funções necessárias
     */
    private function init() {

        // Includes Necessários
        require LIBRARY_PATH . 'GrehFW/helpers/Helpers.class.php';
        require LIBRARY_PATH . 'GrehFW/helpers/AutoLoader.class.php';

        // Instaciando Helpers para uso do sistema
        $helper = Helpers::getInstance();

        /**
         * Carregando Autoloader
         * 
         * O AutoLoader faz executa a função require para as classes 
         * automaticamente 
         */
        $helper->AutoLoader()->register();

        /**
         * Inicializa as configurações do banco de dados
         */



        $helper->Database()->init();

        $helper->Request()->init();


        // Instancia o controller e chama actions definidas pelo request
        $file_object = $helper->Request()->getControllerPath();
        if (file_exists($file_object)) {
            // Instacia a classe do controller;
            $class = $helper->Request()->getControllerClassName();
            $object = new $class($helper);

            // Carrega ação chamada;
            $action = $helper->Request()->getAction();
            $object->action($action . 'Action');
        } else {
            exit('Controller não encontrado!' . " ($file_object)");
        }
    }

}
