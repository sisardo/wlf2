<?php

class Alerts {

    /**
     * Instância da classe Alerts
     * @var Alerts 
     */
    private static $_instance = null;

    /**
     * Retorna uma instância de Alerts, caso não exista é criada
     * 
     * @return Alerts
     */
    public static function getInstance() {
        if (self::$_instance == null)
            self::$_instance = new self();
        return self::$_instance;
    }

    public function success() {
        $success = array();
        $num_args = func_num_args();
        if ($num_args > 0) {
            $args = func_get_args();
            foreach ($args as $arg) {
                $success[] = $arg;
            }
        }

        @ session_start();
        if (!isset($_SESSION['success'])) {
            $_SESSION['success'] = array();
        }
        $_SESSION['success'] = array_push($_SESSION['success'], $success);
    }

    public function warning() {
        $warning = array();
        $num_args = func_num_args();
        if ($num_args > 0) {
            $args = func_get_args();
            foreach ($args as $arg) {
                $warning[] = $arg;
            }
        }

        @ session_start();
        if (!isset($_SESSION['warning'])) {
            $_SESSION['warning'] = array();
        }
        $_SESSION['warning'] = array_push($_SESSION['warning'], $warning);
    }

    public function error() {
        $error = array();
        $num_args = func_num_args();
        if ($num_args > 0) {
            $args = func_get_args();
            foreach ($args as $arg) {
                $error[] = $arg;
            }
        }

        @ session_start();
        if (!isset($_SESSION['error'])) {
            $_SESSION['error'] = $error;
        } else {
            $_SESSION['error'] = array_merge($_SESSION['error'], $error);
        }
    }

    public function showErrors() {
        @ session_start();
        if (isset($_SESSION['error']) && !empty($_SESSION['error']) && gettype($_SESSION['error'])) {
            $errors = '<div class="alerts_error">';
            foreach ($_SESSION['error'] as $error) {
                $errors .= $error . '<br />';
            }
            $errors .= '</div>';

            unset($_SESSION['error']);

            return $errors;
        }
    }

    public function showWarnings() {
        @ session_start();
        if (isset($_SESSION['warning']) && !empty($_SESSION['warning']) && gettype($_SESSION['warning'])) {
            $warnings = '<div class="alerts_warning">';
            foreach ($_SESSION['warning'] as $warning) {
                $warnings .= $warning . '<br />';
            }
            $warnings .= '</div>';

            unset($_SESSION['warning']);

            return $warnings;
        }
    }

    public function showSuccess() {
        @ session_start();
        if (isset($_SESSION['success']) && !empty($_SESSION['success']) && gettype($_SESSION['success'])) {
            $successs = '<div class="alerts_success">';
            foreach ($_SESSION['success'] as $success) {
                $successs .= $success . '<br />';
            }
            $successs .= '</div>';

            unset($_SESSION['success']);

            return $successs;
        }
    }

    public function showAll() {
        return $this->showSuccess() . $this->showWarnings() . $this->showErrors();
    }

}
