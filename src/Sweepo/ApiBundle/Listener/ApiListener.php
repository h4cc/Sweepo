<?php

namespace Sweepo\ApiBundle\Listener;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use Sweepo\ApiBundle\Response\ApiResponse;
use Sweepo\ApiBundle\Authentication\ApiLogin;
use Sweepo\CoreBundle\ErrorCode\ErrorCode;

class ApiListener
{
    /**
     * @var Sweepo\ApiBundle\Response\ApiResponse
     */
    private $apiResponse;

    /**
     * @var Sweepo\ApiBundle\Authentication\ApiLogin
     */
    private $apiLogin;

    /**
     * @var Symfony\Component\Security\Core\SecurityContextInterface
     */
    private $security;

    public function __construct(ApiResponse $apiResponse, ApiLogin $apiLogin, SecurityContextInterface $security)
    {
        $this->apiResponse = $apiResponse;
        $this->apiLogin = $apiLogin;
        $this->security = $security;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $route = $request->attributes->get('_route');

        if ($event->getRequestType() === HttpKernelInterface::MASTER_REQUEST && preg_match('/^api/', $route)) {
            $token = $request->query->get('token', null);

            // Check if we have a token for POST, PUT, DELETE methods
            if (!isset($token)) {
                return $event->setResponse($this->apiResponse->errorResponse('Token is required with API', ErrorCode::TOKEN_MISSING, 403));
            }

            // Here is our simple json decoder for POST params given in Request
            if (in_array($request->getMethod(), array('POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'))) {
                if (!count($request->request->all())) {
                    $request = $this->jsonDecode($request);
                }
            }

            // Check user
            if (false === $user = $this->apiLogin->checkToken($token)) {
                return $event->setResponse($this->apiResponse->errorResponse('User not found', ErrorCode::USER_NOT_FOUND, 200));
            }

            // Login
            $roles = $user->getRoles();
            $this->security->setToken(new UsernamePasswordToken($user, null, 'main', $roles));
        }
    }

    private function jsonDecode(Request $request)
    {
        $data = @json_decode($request->getContent(), true);

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $request->request->set($key, $value);
            }
        }

        $request->request->set('raw_content', $request->getContent());

        return $request;
    }
}