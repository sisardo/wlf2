<?php

class Form_Input {

    public $id;
    public $name;
    public $label;
    public $type;
    public $validators;
    private $displayLabel;
    private $editShow;

    /**
     * Types
     */
    const TEXT = 'text';
    const PASS = 'password';

    /**
     * Validators
     */
    const NOT_BLANK = 'Form_Validator_NotBlank';
    const EMAIL = 'Form_Validator_Email';

    public function __construct($options = array()) {
        $default = array(
            'id' => '',
            'name' => '',
            'label' => '',
            'displayLabel' => TRUE,
            'type' => self::TEXT,
            'validators' => array(),
            'editShow' => TRUE,
        );

        $options = (object) array_merge($default, $options);

        //name
        if ($options->name == '') {
            exit(var_dump('Voce precisa definir um nome para o input!'));
        }
        $this->name = $options->name;

        //id
        if ($options->id == '') {
            $this->id = $options->name;
        } else {
            $this->id = $options->id;
        }

        //label
        if ($options->label == '' && $options->displayLabel == TRUE) {
            $this->label = $options->name;
        } else {
            $this->label = $options->label;
        }

        $this->type = $options->type;
        $this->displayLabel = $options->displayLabel;
        $this->validators = $options->validators;
    }

    public function generate($options = array()) {
        $default = array(
            'edit' => FALSE,
        );
        $options = (object) array_merge($default, $options);

        if (!$options->edit && $this->editShow) {
            return '';
        }

        $input = '';

        if ($this->displayLabel) {
            $input .= $this->label . '<br />';
        }

        switch ($this->type) {
            case self::TEXT:
                $input .= "<input id='$this->id' name='$this->name' type='text' />";

                break;
            case self::PASS:
                $input .= "<input id='$this->id' name='$this->name' type='password' />";

                break;

            default:
                break;
        }
        return $input . '<br />';
    }

    public function validade($value) {
        foreach ($this->validators as $validator) {
            $fail = $validator::validate($this->label,$value);
            if(!$fail){
                return FALSE;
            }
        }
    }

    public function getName() {
        return $this->name;
    }

}
