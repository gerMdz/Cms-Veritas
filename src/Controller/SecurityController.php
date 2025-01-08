<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Model\UserRegistrationFormModel;
use App\Form\Model\VoluntarioReservaRegistrationFormModel;
use App\Form\UserRegistrationFormType;
use App\Form\VoluntarioReservaRegistrationFormType;
use App\Repository\UserRepository;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class SecurityController extends AbstractController
{

    public function __construct(
        readonly CsrfTokenManagerInterface $csrfTokenManager)
    {
    }

    #[Route(path: '/admin/ingreso', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {

        try {
            $this->container->get('doctrine');
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface) {
        }
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('admin/security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'csrf_token' => $this->container->get('security.csrf.token_manager')->getToken('authenticate'),
        ]);
    }

    #[Route(path: '/admin/logout', name: 'app_logout')]
    public function logout(): never
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @param Request $request
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @param UserAuthenticatorInterface $userAuthenticator
     * @param LoginFormAuthenticator $formAuthenticator
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route(path: '/admin/registro', name: 'app_registro')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        UserAuthenticatorInterface $userAuthenticator,
        LoginFormAuthenticator $formAuthenticator,
        EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserRegistrationFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UserRegistrationFormModel $userModel */
            $userModel = $form->getData();
            $user = new User();
            $user->setEmail($userModel->email);
            $user->setPassword(
                $userPasswordHasher->hashPassword($user, $userModel->plainPassword)
            );
            $user->setRoles(['ROLE_USER']);
            if ($form['roles']->getData()) {
                $user->setRoles([$form['roles']->getData()]);
            }
            $user->setRoles(['ROLE_USER']);
            $user->setPrimerNombre($userModel->primerNombre);
            if (true === $userModel->aceptaTerminos) {
                $user->aceptaTerminos();
            }

            $entityManager->persist($user);
            $entityManager->flush();

            return $userAuthenticator->authenticateUser(
                $user,
                $formAuthenticator,
                $request
            );
        }

        return $this->render('security/register.html.twig', [
            'registroForm' => $form,
        ]);
    }

    /**
     * @param Request $request
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route(path: '/admin/registro_voluntario_reserva', name: 'app_registro_voluntario_reserva')]
    public function registerVoluntarioReserva(Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(VoluntarioReservaRegistrationFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var VoluntarioReservaRegistrationFormModel $userModel */
            $userModel = $form->getData();
            $user = new User();
            $email = strtolower((string) $userModel->primerNombre).'@alameda.ar';
            $isUser = $userRepository->findBy(['email' => $email]);
            if ($isUser) {
                $this->addFlash('success', sprintf('El usuario %s ya existe', $user->getEmail()));

                return $this->redirectToRoute('app_registro_voluntario_reserva');
            }
            $user->setEmail($email);
            $user->setPassword(
                $userPasswordHasher->hashPassword($user, 'Alameda2020!')
            );
            $user->setRoles(['ROLE_USER']);

            $user->setRoles(['ROLE_RESERVA']);
            $user->setPrimerNombre($userModel->primerNombre);

            $user->aceptaTerminos();

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Se agregó correctamente al usuario '.$user->getEmail());

            return $this->redirectToRoute('app_registro_voluntario_reserva');
        }

        return $this->render('security/registerVoluntarioReserva.html.twig', [
            'registroForm' => $form,
        ]);
    }

    #[Route(path: '/test/route/index', name: 'app_test_route')]
    public function testRoute(Request $request)
    {
        // Establecer una variable de sesión
        $request->getSession()->set('test', 'Hello, session!');

        // Obtener la variable de sesión
        $sessionValue = $request->getSession()->get('test');

        return new Response($sessionValue); // Deberías ver 'Hello, session!' en esta ruta
    }
}
