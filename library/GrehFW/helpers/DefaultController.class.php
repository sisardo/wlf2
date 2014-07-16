<?php

/**
 * Responsável por chamar a funções do controlador e contruir as páginas através
 * da classe template
 * 
 * @author Angreh (angreh@gmail.com)
 * @version 2.0
 */
abstract class DefaultController {

    /**
     * Armazena a instancia do helper, para maiores informações veja a 
     * classe Helpers.class.php
     * @var Helpers 
     */
    protected $helper;

    /**
     * Instância da classe template
     * @var Template 
     */
    protected $tpl;

    /**
     *
     * @var type 
     */
    protected $tdr;

    /**
     * Classe 
     * @var DefaultMapper
     */
    protected $mapper;

    /**
     * Flag que diz se o template vai ser carregado
     * @var boolean 
     */
    private $tplStarted = false;

    /**
     * Armazena o nome do arquivo de layout html que vai ser carregado com a 
     * página ou (boolean)false caso seja necessário desabilitar o layout
     * @var mixed 
     */
    private $tplLayout = 'index';

    /**
     * Instancia o helper e chama a função init
     * @param Helpers $helper
     */
    public function __construct($helper = NULL) {
        if ($helper != NULL) {
            $this->helper = $helper;
        }
        $this->init();
    }

    /**
     * método criado para ser reescrito caso seja necessário alguma inicialização
     * pré-action
     */
    protected function init() {
        
    }

    /**
     * Verifica se o método chamado existe e em caso positivo o chama, verifica 
     * também se foi ativado a classe template e a carrega
     * 
     * @param string $action
     * @throws Exception
     */
    public function action($action) {
        if (method_exists($this, $action)) {
            $this->$action();
        } else {
            $this->viewInit();
        }
        if ($this->tplStarted) {
            $this->viewRender();
        }
    }

    /**
     * Instancia a classe template dentro do controller, e carrega os arquivos de
     * layout e views referente a atual requisição
     * 
     * Marca a variável $this->tplStarted para que seja carregado o método 
     * show() da classe template
     * 
     * @throws Exception
     */
    protected function viewInit() {
        $this->tplStarted = true;

        //$this->preView();
        //$this->helper->Plugins()->preView();
        $modulePath = $this->helper->Request()->getModulePath();

        if ($this->tplLayout === false) {
            $template = LIBRARY_PATH . 'GrehFW/auxFiles/layout_disabled.html';
        } else {
            $template = $modulePath . 'layouts/' . $this->tplLayout . '.html';
        }

        if (file_exists($template)) {

            require LIBRARY_PATH . 'GrehFW/helpers/Template.class.php';
            $this->tpl = Template::getInstance($template);

            $view_template = $this->helper->Request()->getViewPath();
            if (file_exists($view_template)) {
                $this->tpl->addFile('CONTENT', $view_template);
                //$this->inView();
                //$this->helper->Plugins()->inView();
            } else {
                exit(var_dump('Não foi possível carrecar a View. (' . $view_template . ')'));
            }
        } else {
            throw new Exception('Não foi possível carregar o Layout. (' . $template . ')');
        }
    }

    public function viewRender() {
        header('Content-type: text/html; charset=' . UNICODE_CHARSET);
        $this->tpl->show();
    }

    public function disableLayout() {
        $this->tplLayout = false;
    }

    public function setLayout($layout) {
        $this->tplLayout = $layout;
    }

    protected function redirect($url = '') {
        header('Location: ' . BASE_URL . $url);
        exit;
    }

    protected function gettext($text) {
        $textAux = str_replace(' ', '_', $text);
        $filename = SYSTEM_PATH . 'configs/langs/'. $this->helper->Request()->getModule() .'/' . LANGUAGE . '.xml';
        if (file_exists($filename)) {
            $xml = simplexml_load_file($filename);
            $opa = $xml->xpath('/resources/string[@name="' . $textAux . '"]');
            if (isset($opa[0])) {
                return $opa[0];
            } else {
                return $text;
            }
        }
    }

}

?>