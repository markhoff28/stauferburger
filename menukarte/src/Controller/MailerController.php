<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MailerController extends AbstractController
{
    #[Route('/mail', name: 'mail')]
    public function sendEmail(MailerInterface $mailer, Request $request): Response
    {

        $emailForm = $this->createFormBuilder()
            ->add('nachricht', TextareaType::class, [
                'attr' => array('rows' => '5')
            ])
            ->add('abschicken', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-outline-danger float-right'
                ]
            ])

            ->getForm();

        $emailForm->handleRequest($request);

        if ($emailForm->isSubmitted()) {
            $input = $emailForm->getData();
            $text = $input['nachricht'];
            $desk = 'tisch1';
            $email = (new TemplatedEmail())
                ->from('tisch1@menukarte.wip')
                ->to('kellner@menukarte.wip')
                ->subject('Nachricht')

                ->htmlTemplate('mailer/mail.html.twig')

                ->context([
                    'tisch' => $desk,
                    'text' => $text
                ]);

            $mailer->send($email);
            $this->addFlash('nachricht', 'Nachricht wurde versendet!');
            return $this->redirect($this->generateUrl('mail'));
        }

        return $this->render('mailer/index.html.twig', [
            'emailForm' => $emailForm->createView()
        ]);
    }
}
