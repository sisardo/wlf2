<?php

class Form_Validator_Email {

    static function validate($label, $value) {
        $pattern = "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$";

        if (eregi($pattern, $value)) {
            return TRUE;
        } else {
            $failMessage = Helpers::getInstance()->Translator()->getText('O campo ');
            $failMessage .= Helpers::getInstance()->Translator()->getText($label);
            $failMessage .= Helpers::getInstance()->Translator()->getText(' não é um email válido.');
            Helpers::getInstance()->Alerts()->error($failMessage);
            
            return FALSE;
        }
    }

}
