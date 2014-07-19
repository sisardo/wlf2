<?php

class DefaultForm {

    const ALERT = 'alert';

    protected $fields = array();
    private $submitValue = ''; /* não foi implementado ainda */

    public function __construct() {
        $this->init();
    }

    protected function init() {
        
    }

    public function validate($options = array()) {
        $default = array(
            'errotType' => self::ALERT, /* não está em uso ainda */
            'data' => array()
        );
        $options = (object) array_merge($default, $options);

        foreach ($options->data as $key => $value) {
            $fail = $this->fields[$key]->validade($value);
            if (!$fail) {
                return FALSE;
            }
        }
    }

    public function generate($options = array()) {
        $default = array(
            'edit' => FALSE,
        );
        $options = (object) array_merge($default, $options);

        $form = '';
        $form .= '<form method="POST">';
        $form .= $this->generateFields($options->edit);
        $form .= $this->generateSubmit();
        $form .= '</form>';

        return $form;
    }

    protected function generateFields($edit = FALSE) {
        if ($edit === FALSE) {
            $fields = '';
            foreach ($this->fields as $field) {
                $fields .= $field->generate(array('edit' => FALSE));
            }
        }
        return $fields;
//        exit(var_dump($fields,  $this->fields));
    }

    protected function addField($data) {
        if (gettype($data) == 'array') {
            $field = new Form_Input($data);
            $this->fields[$field->getName()] = $field;
        } else {
            $this->fields[$data['name']] = $data;
        }
    }

    protected function generateSubmit() {
        return '<input type="submit" />';
    }

}
