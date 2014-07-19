<?php

class Grupos_Admin_Controller extends DefaultController {

    protected $mapper;

    protected function init() {
        $this->mapper = new Grupos_Admin_Mapper();
    }

    public function indexAction() {
        $this->viewInit();
        $grupos = $this->mapper->get(array(
            'condition'=>array('borrado'=>0)
        ));
        foreach ($grupos as $grupo) {
            $this->tpl->GRUPOS_ID = $grupo->id;
            $this->tpl->GRUPOS_NOMBRE = $grupo->nombre;
            $this->tpl->block('GRUPOS_BLOCK');
        }
    }

    public function agregarGrupoAction() {
        if (empty($_POST)) {
            $this->viewInit();
        } else {
            $this->mapper->insert(array(
                'set' => $_POST
            ));
            $this->redirect('admin/grupos/agregar_grupo');
        }
    }

    public function modificarGrupoAction() {
        if (empty($_POST)) {
            $this->viewInit();
            $id = $this->helper->Request()->getDataValue('id');
            $grupo = $this->mapper->get(array(
                'condition' => array('id' => $id)
            ));
            $grupo = $grupo[0];
            $this->tpl->GRUPO_ID = $grupo->id;
            $this->tpl->GRUPO_NOMBRE = $grupo->nombre;
            $this->tpl->GRUPO_DESCRIPCION = $grupo->descripcion;
        } else {
            $id = $_POST['id'];
            unset($_POST['id']);
            $this->mapper->updateCondition(array(
                'set' => $_POST,
                'condition' => array('id' => $id)
            ));
            $this->redirect('admin/grupos');
        }
    }
    public function borrarGrupoAction(){
        $this->mapper->updateCondition(array(
            'set'=>array('borrado'=>1),
            'condition'=>array('id'=>$this->helper->Request()->getDataValue('id'))
        ));
        $this->redirect('admin/grupos');
    }

}
