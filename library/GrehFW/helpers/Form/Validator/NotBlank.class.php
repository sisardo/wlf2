<?php

class Form_Validator_NotBlank {

    static function validate($label,$value) {
        if($value == ''){
            $failMessage = Helpers::getInstance()->Translator()->getText('O campo ');
            $failMessage .= Helpers::getInstance()->Translator()->getText($label);
            $failMessage .= Helpers::getInstance()->Translator()->getText(' não pode ser deixado em branco.');
            Helpers::getInstance()->Alerts()->error($failMessage);
            
            return FALSE;
        } else {
            return TRUE;
        }
    }

}
