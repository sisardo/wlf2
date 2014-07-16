<?php

class Usuario_Admin_Controller extends DefaultController {

    protected $mapper;

    protected function init() {
        $this->mapper = new Usuario_Admin_Mapper();
    }

    public function indexAction() {
        $this->viewInit();
        $usuarios = $this->mapper->get(array(
            'condition' => array('borrado' => 0)
//            'debug'=>true
        ));

        foreach ($usuarios as $usuario) {
            $this->tpl->USUARIO_ID = $usuario->id;
            $this->tpl->USUARIO_NOMBRE = $usuario->nombre;
//            $this->tpl->USUARIO_APELLIDO=$usuario->apellido;
            $this->tpl->block('USUARIOS_BLOCK');
        }
    }

    public function modificarUsuarioAction() {
        if (empty($_POST)) {
            $this->viewInit();
             $id = $this->helper->Request()->getDataValue('id');
            $usuario = $this->mapper->get(array(
                'condition' => array('id' => $id)
            ));
            $usuario = $usuario[0];
            $this->tpl->USUARIOS_ID = $usuario->id;
            $this->tpl->USUARIOS_NOMBRE = $usuario->nombre;
            $this->tpl->USUARIOS_APELLIDO = $usuario->apellido;
        } else {
            $id = $_POST['id'];
            unset($_POST['id']);
            $this->mapper->updateCondition(array(
                'set' => $_POST,
                'condition' => array('id' => $id),
                'debug' => false
            ));
            $this->redirect('admin/usuario/modificar_usuario/id/'.$id);
        }
    }

    public function borrarUsuarioAction() {
        $this->mapper->updateCondition(array(
            'set' => array('borrado' => 1),
            'condition' => array('id' => $this->helper->Request()->getDataValue('id'))
        ));
        $this->redirect('admin/usuario');
    }

}