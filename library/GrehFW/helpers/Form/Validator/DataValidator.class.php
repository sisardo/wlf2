<?php

class DataValidator {
    
    public function validate($options = array()) {
        $default = array(
            'data' => array(),
            'returnAlert' => true
        );
        $options = (object) array_merge($default, $options);
        
        foreach ($options->data as $data => $value) {
            
        }
    }
    
}

