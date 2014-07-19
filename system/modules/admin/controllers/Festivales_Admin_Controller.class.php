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
//                'debug' => false
            ));
            $this->redirect('admin/festivales/agregar');
        }
    }

    public function modificarAction() {
        if (empty($_POST)) {
            $this->viewInit();
            $id = $this->helper->Request()->getDataValue('id');
            $festival = $this->mapper->get(array(
                'condition' => array('id' => $id),
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
            $this->redirect('admin/festivales/modificar/id/' . $id);
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
            $id = $this->helper->Request()->getDataValue('id');
            $estilo = $estiloMapper->get(array(
                'condition' => array('id' => $id)
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
            $this->redirect('admin/festivales/modificar_estilo/id/' . $id);
        }
    }

    public function agregarGrupoAction() {
        $this->viewInit();
        $gruposMapper = new Grupos_Admin_Mapper();
        $grupo = $gruposMapper->get(array(
            'debug' => false
        ));
        foreach ($grupo as $grupolista) {
            $this->tpl->GRUPOS_ID = $grupolista->id;
            $this->tpl->GRUPOS_NOMBRE = $grupolista->nombre;
            $this->tpl->block('GRUPOS_BLOCK');
        }
//        $agregarGrupoMapper = new FestivalGrupo_Admin_Mapper();
//        $listaGrupos = $agregarGrupoMapper->get(array(
//            'set',
//            'condition'
//        ));
        //para fazer uma query sem usar o sitema pode usar assim
        $id = $this->helper->Request()->getDataValue('id');
        $listaGrupo = $this->helper->Database()->query(array(
            'sql' => 'SELECT gr_nombre FROM welovefe_default.fes_grupo
                        inner join welovefe_default.fes_festivalgrupo on fes_grupo.gr_fesGrupoId = fes_festivalgrupo.fg_gr_fesGrupoId
                        inner join welovefe_default.fes_festival on fes_festivalgrupo.fg_fe_fesFestivalId = fes_festival.fe_fesFestivalId
                        where fes_festivalgrupo.fg_fe_fesFestivalId = ' . $id
        ));
        foreach ($listaGrupo as $value) {
            $this->tpl->LISTAGRUPO_NOMBRE = $value->nombre;
            $this->tpl->block('LISTAGRUPO_BLOCK');
        }
    }

}

