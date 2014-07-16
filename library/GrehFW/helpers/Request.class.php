<?php

/**
 * Essa é classe responsável por separarar o que vem do endereço do site e 
 * dizer queal das partes é modulo, controller, action e variáveis retornando seu 
 * nome devidamente formatado
 * 
 * Responsavél também por retornar os nome e localizações dos arquivos dos modulos,
 * controllers e etc ...
 * 
 * @author Angreh (angreh@gmail.com)
 * @version 2.0 
 */
class Request {

    /**
     * guarda a instancia estatica dessa classe
     */
    private static $_instance = null;

    /**
     * guarda a requisição original do servidor
     * 
     * @var string 
     */
    private $resquest;

    /**
     * 
     * @var string módulo
     */
    private $module;

    /**
     * 
     * @var string controler
     */
    private $controller;

    /**
     * 
     * @var string action
     */
    private $action;

    /**
     * guarda as variaveis da requisição
     * @var array 
     */
    private $vars;

    /**
     * guarda as rotas de endereço
     * @var array 
     */
    private $routes = array();

    /**
     * guarda o nome da classe do controller
     * 
     * @var string 
     */
    private $controllerClassName;

    /**
     * Pega a instancia ativa da classe ou cria uma caso nao exista e a retorna
     *
     * @return Request
     */
    public static function getInstance() {
        if (self::$_instance == null)
            self::$_instance = new self();
        return self::$_instance;
    }

    /**
     * Define todas as variavéis de modulos, controlers, ações e variáveis
     */
    public function init($options = array()) {

        $default = array(
            'defaultModule' => 'site',
            'defaultController' => 'home',
            'defaultAction' => 'index'
        );

        $options = (object) array_merge($default, $options);

        $filename = SYSTEM_PATH . 'configs/routes.php';
        if (file_exists($filename)) {
            include $filename;
            $this->routes = $routes;
        }

//Remove a primeira '/' do request
        $request = substr($_SERVER['REQUEST_URI'], 1);
        if (isset($this->routes[$request])) {
            $this->resquest = $this->routes[$request];
        } else {
            $this->resquest = (string) $request;
        }

//trasnforma em array
        $request = explode('/', $this->resquest);

//Define modulo
        if (isset($request[0]) && $request[0] != '') {
            $this->module = $request[0];
        } else {
            $this->module = strtolower($options->defaultModule);
        }

//define controller
        if (isset($request[1])) {
            $this->controller = $request[1];
        } else {
            $this->controller = strtolower($options->defaultController);
        }

//define o nome da classe do controller
        $this->controllerClassName = str_replace(' ', '', ucwords(str_replace('_', ' ', $this->controller))) . '_' . ucfirst($this->module) . '_Controller';

//define action
        if (isset($request[2])) {
            if (strpos($request[2], '_') === false) {
                $this->action = $request[2];
            } else {
                $actionArr = explode('_', $request[2]);
                $actionArrLenght = sizeof($actionArr);
                for ($i = 1; $i < $actionArrLenght; $i++) {
                    $actionArr[$i] = ucfirst(strtolower($actionArr[$i]));
                }
                $action = implode('', $actionArr);
                $this->action = $action;
            }
        } else {
            $this->action = strtolower($options->defaultAction);
        }

//define variáveis
        $requestParamsCount = count($request);
        if ($requestParamsCount > 4) {
            $vars = array_slice($request, 3);
            foreach ($vars as $key => $value) {
//se for par salva a chave e se for impar atribui o valor a chave previamente definida 
                if (!( $key & 1 )) {
                    $auxKey = $value;
                } else {
                    $this->vars[$auxKey] = $value;
                }
            }
        }
//        exit(var_dump($this->vars));
    }

    /**
     * Retorna o caminho do controlador com o nome do arquivo incluso na string
     * 
     * @return string endereço do arquivo do controller
     */
    public function getControllerPath() {
        return SYSTEM_PATH . 'modules/' . $this->module . '/controllers/' . $this->controllerClassName . '.class.php';
    }

    /**
     * Retorna o nome real da classe controller ativa
     * 
     * @return string nome da classe do controller
     */
    public function getControllerClassName() {
        return $this->controllerClassName;
    }

    /**
     * Retorna o nome da action ativa
     * 
     * @return string action
     */
    public function getAction() {
        return $this->action;
    }

    /**
     * Retorna o caminho do módulo ativo
     * 
     * @return string caminho do module
     */
    public function getModulePath() {
        return SYSTEM_PATH . 'modules/' . $this->module . '/';
    }

    /**
     * Retorna o caminho para o arquivo do view referente a action ativa
     * 
     * @return string caminho até o view
     */
    public function getViewPath() {
        return SYSTEM_PATH . 'modules/' . $this->module . '/views/' . $this->controller . '/' . $this->action . '.html';
    }

    /**
     * Retorna o nome do Módulo ativo
     * 
     * @return string module
     */
    public function getModule() {
        return $this->module;
    }

    /**
     * DESUSO
     * @param type $routes
     */
    public function setRoutes($routes) {
        $this->routes = $routes;
    }

    PUBLIC FUNCTION getData() {
        return $this->vars;
    }
    PUBLIC FUNCTION getDataValue($variavel){
        return $this->vars[$variavel];
    }

}
