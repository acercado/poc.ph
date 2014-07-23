<?php

namespace App\Lib\Network;

use Phalcon\Mvc\User\Component,
    Phalcon\Mvc\View,
    Swift_Mailer,
    Swift_Message,
    Swift_SmtpTransport;

/**
 *
 * Sends e-mails based on pre-defined templates
 */
class Mail extends Component
{
    protected $transport;

    /**
     * Applies a template to be used in the e-mail
     *
     * @param string $name
     * @param array $params
     */
    /*public function getTemplate($path, $title, $params){
        return $this->view->partial($path, array('content' => $params, 'title' => $title));
    }*/

    public function getTemplate($name, $params){
        $parameters['content'] = $params;

        return $this->view->getRender('emailtemplate', $name, $parameters, function($view){
            $view->setViewsDir(VIEWS_PATH);
            $view->setRenderLevel(View::LEVEL_LAYOUT);
        });
    }

    /**
     * Sends e-mails via gmail based on predefined templates
     *
     * @param array $to
     * @param string $subject
     * @param string $path
     * @param array $params
     */
    public function send($to, $subject, $name, $params){
        //Settings
        $mailSettings = $this->config->project->mail;

        $template = $this->getTemplate($name, $params);
        // $template = $this->getTemplate($path, $subject, $params);

        // Create the message
        $message = Swift_Message::newInstance()
            ->setSubject($subject)
            ->setTo($to)
            ->setFrom(array(
                $mailSettings->fromEmail => $mailSettings->fromName
            ))
            ->setBody($template, 'text/html');
            if (!$this->transport) {
                $this->transport = Swift_SmtpTransport::newInstance(
                    $mailSettings->smtp->server,
                    $mailSettings->smtp->port//,
                    //$mailSettings->smtp->security
                )->setUsername($mailSettings->smtp->username)->setPassword($mailSettings->smtp->password);
            }

            // Create the Mailer using your created Transport
            $mailer = Swift_Mailer::newInstance($this->transport);

            return $mailer->send($message);
    }

}