<?php

class Festivales_Admin_Controller extends DefaultController {

    protected $mapper;

    protected function init() {
        $this->mapper = new Festival_Admin_Mapper();
    }

    public function indexAction() {

        $this->viewInit();
        $festivales = $this->mapper->get(array(
            'condition' => array('borrado' => 0)
        ));
        foreach ($festivales as $festival) {
            $this->tpl->FESTIVAL_ID = $festival->id;
            $this->tpl->FESTIVAL_NOMBRE = $festival->nombre;
            $this->tpl->block('FESTIVALES_BLOCK');
        }
    }

    public function agregarAction() {
        if (empty($_POST)) {
            $this->viewInit();
        } else {
            //exit(var_dump($_POST));
            $this->mapper->insert(array(
                'set' => $_POST,
                'debug' => false
            ));
            $this->redirect('admin/festivales/agregar');
        }
    }

    public function modificarAction() {
        if (empty($_POST)) {
            $this->viewInit();
            $festival = $this->mapper->get(array(
                'condition' => array('id' => 1),
//             'debug'=>true
            ));
            $festival = $festival[0];
            $this->tpl->FESTIVAL_ID = $festival->id;
            $this->tpl->FESTIVAL_NOMBRE = $festival->nombre;
            $this->tpl->FESTIVAL_DESCRIPCION = $festival->descripcion;
            $this->tpl->FESTIVAL_FECHA = $festival->fechaInicio;
//         exit(var_dump($festival));   
        } else {
            $id = $_POST['id'];
            unset($_POST['id']);
            $this->mapper->updateCondition(array(
                'set' => $_POST,
                'condition' => array('id' => $id),
//                'debug' => true
            ));
            $this->redirect('admin/festivales/modificar');
        }
    }

    public function borrarAction() {
        $this->mapper->updateCondition(array(
            'set' => array('borrado' => 1),
            'condition' => array('id' => $this->helper->Request()->getDataValue('id')),
//            'debug' => true
        ));
        $this->redirect('admin/festivales');
    }

    public function listaEstiloAction() {
        $this->viewInit();
        $estiloMapper = new Estilo_Admin_Mapper();
        $estilos = $estiloMapper->get(array(
            'condition' => array('borrado' => 0)
        ));
        foreach ($estilos as $estiloslista) {
            $this->tpl->ESTILOS_ID = $estiloslista->id;
            $this->tpl->ESTILOS_NOMBRE = $estiloslista->nombre;
            $this->tpl->block('LISTAESTILOS_BLOCK');
        }
    }

    public function agregarEstiloAction() {
        if (empty($_POST)) {
            $this->viewInit();
        } else {
//            exit(var_dump($_POST));
            $estiloMapper = new Estilo_Admin_Mapper();
            $estiloMapper->insert(array(
                'set' => $_POST,
//                'debug' => true
            ));
            $this->redirect('admin/festivales/listaEstilo');
        }
    }

    public function borrarEstiloAction() {
        $estiloMapper = new Estilo_Admin_Mapper();
        $estiloMapper->updateCondition(array(
            'set' => array('borrado' => 1),
            'condition' => array('id' => $this->helper->Request()->getDataValue('id')),
//            'debug'=>true
        ));
        $this->redirect('admin/festivales/lista_estilo');
    }

    public function modificarEstiloAction() {

        if (empty($_POST)) {
            $this->viewInit();
            $estiloMapper = new Estilo_Admin_Mapper();
            $estilo = $estiloMapper->get(array(
                'condition' => array('id' => 1)
            ));
            $estilo = $estilo[0];
            $this->tpl->ESTILO_ID = $estilo->id;
            $this->tpl->ESTILO_NOMBRE = $estilo->nombre;
        } else {
            $id = $_POST['id'];
            unset($_POST['id']);
            $estiloMapper = new Estilo_Admin_Mapper();
            $estiloMapper->updateCondition(array(
                'set' => $_POST,
                'condition' => array('id' => $id)
            ));
            $this->redirect('admin/festivales/modificar_estilo');
        }
    }

}

?>
