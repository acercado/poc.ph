<?php
namespace Modules\Common\Controllers;

use \Phalcon\Validation,
    \Phalcon\Validation\Validator\PresenceOf,
    \Phalcon\Validation\Validator\Email,
    \Phalcon\Validation\Message,
    App\Lib\Network\Mail,
    Modules\Common\Models\ContactModel;

class ContactController extends ModuleController{
    public function IndexAction($alias=null){
    	$this->registry->alias = $alias;
        $page = ContactModel::findByAliasFixtures($alias, $this->dispatcher->getModuleName());
        $this->checkPageData($page);

        $this->registry->breadcrumbs->add('page', ltrim($this->router->getRewriteUri(), '/'), $page['title']);

    	$this->view->setVars(array(
            'widget_list' => array('header', 'menu', 'seotext', 'subfooter', 'footer')
        ));

        if ($this->request->isPost() == true) {
        	$validation 		= new \Phalcon\Validation();

        	$contact = array();
            $contact['name'] 	= $this->request->getPost('name', array('striptags', 'string'));
            $contact['email'] 	= $this->request->getPost('email', 'email');
            $contact['msg'] 	= $this->request->getPost('msg', array('striptags', 'string'));
            $contact['creadted']= date("Y-m-d H:i:s");

        	$validation->add('name', new PresenceOf(array( 'message' => 'The '.$this->translations->common_5.' is required' )));
			$validation->add('email', new PresenceOf(array( 'message' => 'The '.$this->translations->common_6.' is required' )));
			$validation->add('email', new Email(array( 'message' => 'The '.$this->translations->common_6.' is not valid' )));
			$validation->add('msg', new PresenceOf(array( 'msg' => 'The '.$this->translations->common_7.' is required' )));

			$messages = $validation->validate($_POST);

			$error = '';
			if ( count($messages) ) {
				foreach ($messages as $value) {
					$error .= '<p>'.$value->getMessage().'</p>';
				}

				$this->view->setVars(array(
		            'name' 	=> $contact['name'],
		            'email' => $contact['email'],
		            'msg' 	=> $contact['msg']
		        ));
				$this->flash->error( $error );
			}else{

				$mail = new Mail();
				$mail->send(array($this->config->project->mail->recipentEmail => $this->config->project->mail->recipentName), $this->config->project->name.' | Contact Us', 'contactus', $contact);
				$this->flash->success('<p>Message Sent</p>');
	        }
        }

        $this->tag->prependTitle('Contact Us');
    }
}