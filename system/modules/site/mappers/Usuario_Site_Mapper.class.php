<?php


class Usuario_Site_Mapper extends DefaultMapper {

    public function hola() {

        $teste = parent::get();
        exit(var_dump($teste));
    }

}
