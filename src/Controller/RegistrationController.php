<?php

namespace App\Controller;

use App\Entity\User;
use App\Exception\RegisterValidationException;
use App\Exception\ReservationValidationException;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use App\Services\RegisterService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier, private SerializerInterface $serializer, private RegisterService $registerService)
    {
    }

    #[Route('/register', name: 'app_register', methods: [ 'POST'])]
    public function register(Request $request): Response
    {
        try{
            $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');
            $user = $this->registerService->store($user);
            return new JsonResponse($user, Response::HTTP_CREATED);
        }catch (RegisterValidationException $e) {
            return new JsonResponse([
                'error' => 'Validation failed',
                'form_errors' => $e->getErrors(),
            ], 400);
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Internal Server Error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            /** @var User $user */
            $user = $this->getUser();
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_register');
    }
}
