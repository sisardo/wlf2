<?php

/**
 * Formata string na mais diversas formas
 */
class StringFormat {

    /**
     * guarda a instancia estatica dessa classe
     */
    private static $_instance = null;

    /**
     * Pega a instancia ativa da classe ou cria uma caso nao exista e a retorna
     *
     * @return StringFormat
     */
    public static function getInstance() {
        if (self::$_instance == null)
            self::$_instance = new self();
        return self::$_instance;
    }

    /**
     * Transforma a data passada pelo paramentro $date para o formato aceito pelo
     * BD. Ex.: '23/03/2014' -> '2014-03-23'
     * 
     * 
     * @param string $string valor a ser formatado
     * @return string data formatada
     */
    public function datePreDB($options) {
        $default = array(
            'date' => ''
        );
        $options = (object) array_merge($default, $options);

        $auxDate = explode('/', $options->date);
        return $auxDate[2] . '-' . $auxDate[1] . '-' . $auxDate[0];
    }

    /**
     * Transforma data numeral e extenso
     */
    public function dateFormat($options) {
        $defaults = array(
            'type' => '',
            'data' => ''
        );
        $options = (object) array_merge($defaults, $options);

        switch ($options->type) {
            case 'm':
                switch ((int) $options->data) {
                    case 1:
                        return 'jan';
                    case 2:
                        return 'fev';
                    case 3:
                        return 'mar';
                    case 4:
                        return 'abr';
                    case 5:
                        return 'mai';
                    case 6:
                        return 'jun';
                    case 7:
                        return 'jul';
                    case 8:
                        return 'ago';
                    case 9:
                        return 'set';
                    case 10:
                        return 'out';
                    case 11:
                        return 'nov';
                    case 12:
                        return 'dez';
                    default:
                        return FALSE;
                }
        }
    }

    /**
     * Formata a hora passada pelo parametro $time e transmorma no padrão aceito
     * pelo DB
     * 
     * @param string $time hora se formatação
     * @return string hora formatada
     */
    public function timePreDB($options) {
        $default = array(
            'time' => ''
        );
        $options = (object) array_merge($default, $options);
        $count_parts = count_chars($options->time, 1);
        $count_parts = $count_parts[58];
        if ($count_parts == 1) {
            return $options->time . ':00';
        }
    }

    /**
     * Formata o parametro $number para um valor aceito pelo banco de dados
     * 
     * @param string $number valor a ser formatado
     * @return float valor formatado
     */
    public function numberFormat($options) {
        $default = array(
            'number' => ''
        );
        $options = (object) array_merge($default, $options);
        $number = str_replace('.', '', $options->number);
        $number = (float) str_replace(',', '.', $number);
        return number_format($number, 2, '.', '');
    }

}
