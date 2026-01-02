<?php

namespace Controllers;

class PageController extends BaseController {
    
    public function home() {
        $this->render('home', [
            'title' => t('app_name') . ' - ' . t('nav_home')
        ]);
    }
}