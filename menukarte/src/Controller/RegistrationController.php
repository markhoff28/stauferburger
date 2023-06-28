<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/reg', name: 'reg')]
    public function reg(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasherInterface,
        ManagerRegistry $doctrine,
        ValidatorInterface $validatorInterface
    ): Response {
        $registrationForm = $this->createFormBuilder()
        ->add('username', TextType::class, [
            'label' => 'Mitarbeiter'])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'first_options' => ['label' => 'Passwort'],
                'second_options' => ['label' => 'Passwort wiederholen'],
            ])
            
            ->add('registrieren', SubmitType::class)
            ->getForm();

        $registrationForm->handleRequest($request);

        if ($registrationForm->isSubmitted()) {
            $input = $registrationForm->getData();

            $user = new User();
            $user->setUsername($input['username']);
            
            $user->setPassword(
                $userPasswordHasherInterface->hashPassword($user, $input['password'])
            );

            $user->setRawPassword( $input['password']);
            $errors = $validatorInterface->validate($user);
            if (count($errors) > 0) {
                return $this->render('registration/index.html.twig', [
                    'registrationForm' => $registrationForm->createView(),
                    'errors' => $errors
                ]);
            } else {
                $entityManager = $doctrine->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

                return $this->redirect(
                    $this->generateUrl('home')
                );
            }

            
        }

        return $this->render('registration/index.html.twig', [
            'registrationForm' => $registrationForm->createView(),
            'errors' => null
        ]);
    }
}
