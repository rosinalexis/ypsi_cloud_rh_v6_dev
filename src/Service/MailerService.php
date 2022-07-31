<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class MailerService
{
    private MailerInterface $mailer;
    private Environment $twig;

    public function __construct(MailerInterface $mailer,Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    /**
     * @throws InternalErrorException
     */
    public function sendEmail(
        $to = 'test@ypsi.fr',
        $subject ='Default subject',
        $content = '<p>See Twig integration for better HTML integration!</p>'
    ) :void
    {
        $email = (new Email())
            ->from('noreply-cloud-rh-@ypsi.com')
            ->to($to)
            ->subject($subject)
            ->html($content,'text/html');

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            throw new InternalErrorException("The server can't send email.Please check your admin.");
        }
    }

    /**
     * @throws SyntaxError
     * @throws InternalErrorException
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function sendAccountConfirmationEmail(User $user): void
    {
        $to = $user->getEmail();
        $subject = "Demande de confirmation de compte";
        $content = $this->twig->render('email/default_account_confirmation.html.twig',['user' => $user]);

        try {
            $this->sendEmail($to, $subject, $content);
        } catch (InternalErrorException $e) {
            throw new InternalErrorException("The server can't send  account confirmation email .Please check your admin.");
        }
    }

}