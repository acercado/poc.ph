<?php
namespace Modules\Creditcards\Controllers;
use App\Controllers\AppController;

class ModuleController extends AppController{
	public function onConstruct(){
       $this->registry->page_name = 'credit-card';
    }
}
