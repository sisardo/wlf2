<?php


class Home_Site_Controller extends DefaultController {

    public function indexAction() {
        $this->viewInit();
        $model = new Usuario_Site_Mapper();
        $model->hola();
    }

}

