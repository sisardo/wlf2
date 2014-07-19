<?php

class Translator {

    /**
     * guarda a instancia estatica dessa classe
     */
    private static $_instance = null;

    /**
     * Pega a instancia ativa da classe ou cria uma caso nao exista e a retorna
     *
     * @return Translator
     */
    public static function getInstance() {
        if (self::$_instance == null)
            self::$_instance = new self();
        return self::$_instance;
    }

    public function getText($text) {
        $textAux = str_replace(' ', '_', strtolower($text));
        $filename = SYSTEM_PATH . 'configs/langs/' . Helpers::getInstance()->Request()->getModule() . '/' . LANGUAGE . '.xml';
        if (file_exists($filename)) {
            $xml = simplexml_load_file($filename);
            $opa = $xml->xpath('/resources/string[@name="' . $textAux . '"]');
            if (isset($opa[0])) {
                return $opa[0];
            } else {
                return $text;
            }
        } else {
            return $text;
        }
    }

}
