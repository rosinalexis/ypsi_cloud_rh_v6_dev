<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;
use Twig\TemplateWrapper;

class MailerService
{
    private MailerInterface $mailer;
    private const EMAIL_SENDER = 'noreply-cloud-rh-@ypsi.com';

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
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
            ->from(self::EMAIL_SENDER)
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
     * @param string $to
     * @param string $subject
     * @param string|TemplateWrapper $templatePath The template name
     * @param array $context
     * @throws InternalErrorException
     */
    public function sendEmailWithTemplate(
        string                 $to = 'test@ypsi.fr',
        string                 $subject ='Default subject',
        TemplateWrapper|string $templatePath ='',
        array $context = []
    ): void
    {
        $email = (new TemplatedEmail())
            ->from(self::EMAIL_SENDER)
            ->to($to)
            ->subject($subject)
            ->htmlTemplate($templatePath)
            ->context($context);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            throw new InternalErrorException("The server can't send the template email.Please check your admin.");
        }
    }

    /**
     * @throws InternalErrorException
     */
    public function sendAccountConfirmationEmail(User $user): void
    {
        $to = $user->getEmail();
        $subject = "Demande de confirmation de compte";
        $template ="email/default_account_confirmation.html.twig";
        $context =['user' => $user];

        $this->sendEmailWithTemplate($to,$subject,$template,$context);
    }

}