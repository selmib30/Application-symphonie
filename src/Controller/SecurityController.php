<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $isFirstUser = $entityManager->getRepository(User::class)->count([]) === 0;

            if ($entityManager->getRepository(User::class)->findOneBy(['email' => $user->getEmail()])) {
                $form->get('email')->addError(new FormError('Cet email est deja utilise.'));
            } elseif ($entityManager->getRepository(User::class)->findOneBy(['username' => $user->getUsername()])) {
                $form->get('username')->addError(new FormError('Ce nom d utilisateur est deja pris.'));
            } else {
                $user->setRoles($isFirstUser ? ['ROLE_ADMIN'] : ['ROLE_USER']);
                $user->setPassword(
                    $passwordHasher->hashPassword($user, (string) $form->get('plainPassword')->getData())
                );

                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Compte cree. Tu peux maintenant te connecter.');

                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/connexion', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        return $this->render('security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    #[Route('/deconnexion', name: 'app_logout')]
    public function logout(): never
    {
        throw new \LogicException('This should never be reached.');
    }
}
