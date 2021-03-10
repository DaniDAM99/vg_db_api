<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Usuario;
use App\Entity\Juego;
use App\Entity\Lista;

use App\Security\JwtAuthenticator;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ListaController extends AbstractController {

    /**
     * @Route("/listas", name="crear_lista", methods={"POST"})
     */
    public function crear_lista(Request $request, ParameterBagInterface $params, UserProviderInterface $userProvider): JsonResponse {
        $em = $this->getDoctrine()->getManager();
        $auth = new JwtAuthenticator($em, $params);

        $credenciales = $auth->getCredentials($request);

        $usuario = $auth->getUser($credenciales, $userProvider);

        if ($usuario) {
            $data = json_decode($request->getContent(), true);
            $titulo = $data['titulo'];

            if (empty($titulo)) {
                return new JsonResponse(['error' => 'Faltan parámetros'], Response::HTTP_PARTIAL_CONTENT);
            }

            $lista = new Lista();

            $lista->setTitulo($titulo);
            $lista->setUsuario($usuario);


            $em->persist($lista);
            $em->flush();

            return new JsonResponse(['respuesta' => 'Lista creada correctamente'], Response::HTTP_OK);
        }
        return new JsonResponse(['error' => 'Usuario no logueado'], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @Route("/listas", name="obtener_listas", methods={"GET"})
     */
    public function obtener_listas(Request $request, ParameterBagInterface $params, UserProviderInterface $userProvider): JsonResponse {
        $em = $this->getDoctrine()->getManager();
        $auth = new JwtAuthenticator($em, $params);

        $credenciales = $auth->getCredentials($request);

        $usuario = $auth->getUser($credenciales, $userProvider);

        if ($usuario) {

            $data = $this->getListasUsuario($usuario);
            return new JsonResponse($data, Response::HTTP_OK);
        }

        return new JsonResponse(['error' => 'Usuario no logueado'], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @Route("/listas/{id}", name="obtener_lista", methods={"GET"})
     */
    public function obtener_lista($id, Request $request, ParameterBagInterface $params, UserProviderInterface $userProvider): JsonResponse {
        $em = $this->getDoctrine()->getManager();
        $auth = new JwtAuthenticator($em, $params);

        $credenciales = $auth->getCredentials($request);

        $usuario = $auth->getUser($credenciales, $userProvider);

        if ($usuario) {
            $data = $this->getListaUsuario($id);

            return new JsonResponse($data, Response::HTTP_OK);
        }
        return new JsonResponse(['error' => 'Usuario no logueado'], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @Route("/listas/{id}", name="eliminar_lista", methods={"DELETE"})
     */
    public function eliminar_lista($id, Request $request, ParameterBagInterface $params, UserProviderInterface $userProvider): JsonResponse {
        $em = $this->getDoctrine()->getManager();
        $auth = new JwtAuthenticator($em, $params);

        $credenciales = $auth->getCredentials($request);

        $usuario = $auth->getUser($credenciales, $userProvider);

        if ($usuario) {
            $lista = $this->getDoctrine()
                    ->getRepository(Lista::class)
                    ->find($id);
            if ($lista) {
                $em = $this->getDoctrine()->getManager();

                $em->remove($lista);
                $em->flush();
                return new JsonResponse(['respuesta' => 'Lista eliminada correctamente'], Response::HTTP_OK);
            }
            return new JsonResponse(['error' => 'La lista no existe'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(['error' => 'Usuario no logueado'], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @Route("/listas/{id}", name="editar_lista", methods={"PUT"})
     */
    public function editar_lista($id, Request $request, ParameterBagInterface $params, UserProviderInterface $userProvider): JsonResponse {
        $em = $this->getDoctrine()->getManager();
        $auth = new JwtAuthenticator($em, $params);

        $credenciales = $auth->getCredentials($request);

        $usuario = $auth->getUser($credenciales, $userProvider);

        if ($usuario) {
            $lista = $this->getDoctrine()
                    ->getRepository(Lista::class)
                    ->find($id);
            if ($lista) {
                $data = json_decode($request->getContent(), true);
                $titulo = $data['titulo'];

                if (empty($titulo)) {
                    return new JsonResponse(['error' => 'Faltan parámetros'], Response::HTTP_PARTIAL_CONTENT);
                }

                $lista->setTitulo($titulo);

                $em = $this->getDoctrine()->getManager();
                $em->persist($lista);
                $em->flush();
                return new JsonResponse(['respuesta' => 'Lista editada correctamente'], Response::HTTP_OK);
            }
            return new JsonResponse(['error' => 'No existe la lista'], Response::HTTP_NOT_FOUND);
        }
        return new JsonResponse(['error' => 'Usuario no logueado'], Response::HTTP_UNAUTHORIZED);
    }

    private function getListasUsuario(Usuario $usuario) {
        $listas = $usuario->getListas();
        $data = [];
        foreach ($listas as $list) {
            $juegosEnLista = $list->getJuegos();

            $juegos = [];
            foreach ($juegosEnLista as $juego) {
                if ($juego->getFechaLanzamiento()) {
                    $fecha = $juego->getFechaLanzamiento()->format('d/m/Y');
                } else {
                    $fecha = $juego->getFechaLanzamiento();
                }

                $juegos[] = [
                    'id' => $juego->getId(),
                    'nombre' => $juego->getNombre(),
                    'fecha_lanzamiento' => $fecha,
                    'genero' => $juego->getGenero(),
                    'plataforma' => $juego->getPlataforma()
                ];
            }

            $lista = [
                'id' => $list->getId(),
                'titulo' => $list->getTitulo(),
                'juegos' => $juegos
            ];

            $data[] = [
                $lista['id'] => $lista
            ];
        }
        return $data;
    }

    private function getListaUsuario($id) {
        $list = $this->getDoctrine()
                ->getRepository(Lista::class)
                ->find($id);

        $juegosEnLista = $list->getJuegos();

        $juegos = [];
        foreach ($juegosEnLista as $juego) {
            if ($juego->getFechaLanzamiento()) {
                $fecha = $juego->getFechaLanzamiento()->format('d/m/Y');
            } else {
                $fecha = $juego->getFechaLanzamiento();
            }

            $juegos[] = [
                'id' => $juego->getId(),
                'nombre' => $juego->getNombre(),
                'fecha_lanzamiento' => $fecha,
                'genero' => $juego->getGenero(),
                'plataforma' => $juego->getPlataforma()
            ];
        }
        $lista = [
            'id' => $list->getId(),
            'titulo' => $list->getTitulo(),
            'juegos' => $juegos
        ];

        return $lista;
    }

}
