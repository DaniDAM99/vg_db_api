<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Firebase\JWT\JWT;
use App\Entity\Usuario;
use App\Security\JwtAuthenticator;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class AuthController extends AbstractController {

    /**
     * @Route("/usuarios/registrar", name="registrar", methods={"POST"})
     */
    public function registrar(Request $request) {
        $data = json_decode($request->getContent(), true);

        $username = $data['username'];
        $email = $data['email'];
        $password = $data['password'];

        if (empty($username) || empty($email) || empty($password)) {
            return new JsonResponse(['error' => 'Faltan parámetros'], Response::HTTP_PARTIAL_CONTENT);
        }

        $usuario = new Usuario();
        $usuario->setEmail($email);
        $usuario->setUsername($username);
        $usuario->setPassword(password_hash($password, PASSWORD_BCRYPT));

        $em = $this->getDoctrine()->getManager();
        $em->persist($usuario);
        $em->flush();
        return new JsonResponse(['respuesta' => 'Usuario añadido correctamente'], Response::HTTP_OK);
    }

    /**
     * @Route("/usuarios", name="obtener_usuario", methods={"GET"})
     */
    public function get_usuario(Request $request, ParameterBagInterface $params, UserProviderInterface $userProvider) {
        $em = $this->getDoctrine()->getManager();
        $auth = new JwtAuthenticator($em, $params);
                
        $credenciales = $auth->getCredentials($request);
        
        $usuario = $auth->getUser($credenciales, $userProvider);
        if ($usuario) {

            $data = [
                'id' => $usuario->getId(),
                'username' => $usuario->getUsername(),
                'email' => $usuario->getEmail()
            ];

            return new JsonResponse($data, Response::HTTP_OK);
        }
        return new JsonResponse(['error' => 'Usuario no logueado'], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @Route("/usuarios/login", name="login", methods={"POST"})
     */
    public function login(Request $request) {
        $data = json_decode($request->getContent(), true);

        $email = $data['email'];
        $pwd = $data['password'];

        if ($email && $pwd) {
            $usuario = $this->getDoctrine()
                    ->getRepository(Usuario::class)
                    ->findOneBy(['email' => $email]);

            if ($usuario) {
                if (password_verify($pwd, $usuario->getPassword())) {
                    // Creamos el JWT
                    $payload = [
                        "usuario" => $usuario->getEmail(),
                        "exp" => (new \DateTime())->modify("+3 day")->getTimestamp()
                    ];

                    $jwt = JWT::encode($payload, $this->getParameter('jwt_secret'), 'HS256');
                    $data = [
                        'repuesta' => 'Se ha iniciado sesion',
                        'token' => $jwt
                    ];

                    return new JsonResponse($data, Response::HTTP_OK);
                }
                return new JsonResponse(['error' => 'Credenciales inválidas'], Response::HTTP_NOT_FOUND);
            }
            return new JsonResponse(['error' => 'Credenciales inválidas'], Response::HTTP_NOT_FOUND);
        }
        return new JsonResponse(['error' => 'Faltan parámetros'], Response::HTTP_PARTIAL_CONTENT);
    }

}
