<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Mailer;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class Mailer implements MailerInterface
{
    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @var UrlGeneratorInterface
     */
    protected $router;

    /**
     * @var EngineInterface
     */
    protected $templating;

    /**
     * @var array
     */
    protected $parameters;

    /**
     * Mailer constructor.
     *
     * @param \Swift_Mailer         $mailer
     * @param UrlGeneratorInterface $router
     * @param EngineInterface       $templating
     * @param array                 $parameters
     */
    public function __construct($mailer, UrlGeneratorInterface  $router, EngineInterface $templating, array $parameters)
    {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->templating = $templating;
        $this->parameters = $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function sendConfirmationEmailMessage(UserInterface $user)
    {
        $template = $this->parameters['confirmation.template'];
        $url = $this->router->generate('fos_user_registration_confirm', array('token' => $user->getConfirmationToken()), UrlGeneratorInterface::ABSOLUTE_URL);
        $rendered = $this->templating->render('FOSUserBundle:Registration:email.html.twig', array(
            'user' => $user,
            'confirmationUrl' => $url,
        ));
        $subject = 'Welcome '.$user->getName();
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: Quickbooks Connector <support@thegenielab.com>". "\r\n";
        $headers .= 'Bcc: Quickbooks Connector <wish@thegenielab.com>' . "\r\n";
        $to = (string) $user->getEmail();
        $mail = mail($to,$subject,$rendered,$headers);

        if($mail){
            return true;
        } else{
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function sendResettingEmailMessage(UserInterface $user)
    {
        $template = $this->parameters['resetting.template'];
        $url = $this->router->generate('fos_user_resetting_reset', array('token' => $user->getConfirmationToken()), UrlGeneratorInterface::ABSOLUTE_URL);
        $rendered = $this->templating->render('FOSUserBundle:Resetting:email.html.twig', array(
            'user' => $user,
            'confirmationUrl' => $url,
        ));
        $subject = 'Reset Password';
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: Quickbooks Connector <support@thegenielab.com>";
        $headers .= 'Bcc: Quickbooks Connector <wish@thegenielab.com>' . "\r\n";
        $to = (string) $user->getEmail();
        $mail = mail($to,$subject,$rendered,$headers);

        if($mail){
            return true;
        } else{
            return false;
        }
    }

    /**
     * @param string $renderedTemplate
     * @param string $fromEmail
     * @param string $toEmail
     */
    protected function sendEmailMessage($renderedTemplate, $fromEmail, $toEmail,$subject)
    {
        // Render the email, use the first line as the subject, and the rest as the body
        $renderedLines = explode("\n", trim($renderedTemplate));
        $subject = array_shift($renderedLines);
        $body = implode("\n", $renderedLines);

        /*$message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($fromEmail)
            ->setTo($toEmail)
            ->setBody($body);

        $this->mailer->send($message);*/
        $fromEmailText = '';
        foreach($fromEmail as $key => $value){
                $fromEmailText = $key;
            }
        
        $to = $toEmail;
        $subject = $subject;
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: Quickbooks Connector ".$fromEmailText;
        $mail = mail($to,$subject,$body,$headers);

        if($mail){
            return true;
        } else{
            return false;
        }
        
    }
}
