<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';
    private $flash;
    private UrlGeneratorInterface $urlGenerator;
    // private UserRepository $userRepository;
    // private CsrfTokenManagerInterface $csrfTokenManager;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        FlashBagInterface $flash
        // CsrfTokenManagerInterface $csrfTokenManager,
        // UserRepository $userRepository
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->flash = $flash;
        // $this->csrfTokenManager = $csrfTokenManager;
        // $this->userRepository = $userRepository;
    }

    public function authenticate(Request $request): PassportInterface
    {
        $email = $request->request->get('email', '');

        $request->getSession()->set(Security::LAST_USERNAME, $email);
        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->get('_csrf_token')),
            ]
        );
    }

    // public function getUser(Request $request)
    // {
    //     $token = new CsrfToken('authenticate', $request->get('_csrf_token'));
    //     if ($this->csrfTokenManager->isTokenValid($token)) {
    //         throw new InvalidCsrfTokenException;
    //     }
    //     $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $request->request->get('email', '')]);
    //     dd($user);
    //     return $user;
    // }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }
        // dd($token->getUser());
        // For example:
        // dd($request->get('email'));
        // $session->set('flashName', 'success');
        // $session->set('flashMessage', 'success');
        // dd($session);
        $this->flash->set('info', 'Logged In!');
        return new RedirectResponse($this->urlGenerator->generate('app_home'));

        throw new \Exception('TODO: provide a valid redirect inside ' . __FILE__);
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}