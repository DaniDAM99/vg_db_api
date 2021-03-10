<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Juego;
use App\Entity\Lista;
use DateTime;
use App\Security\JwtAuthenticator;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class JuegoController extends AbstractController {

    /**
     * @Route("/juegos", name="get_juegos", methods={"GET"})
     */
    public function get_juegos(): JsonResponse {
        $repositorio = $this->getDoctrine()->getRepository(Juego::class);
        $juegos = $repositorio->findAll();
        $data = [];
        foreach ($juegos as $juego) {
            if ($juego->getFechaLanzamiento()) {
                $fecha = $juego->getFechaLanzamiento()->format('d/m/Y');
            } else {
                $fecha = $juego->getFechaLanzamiento();
            }

            $data[] = [
                'id' => $juego->getId(),
                'nombre' => $juego->getNombre(),
                'fecha_lanzamiento' => $fecha,
                'genero' => $juego->getGenero(),
                'plataforma' => $juego->getPlataforma(),
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Route("/juegos/{id}", name="get_juego", methods={"GET"})
     */
    public function get_juego($id): JsonResponse {
        $juego = $this->getDoctrine()
                ->getRepository(Juego::class)
                ->find($id);

        if ($juego) {
            if ($juego->getFechaLanzamiento()) {
                $fecha = $juego->getFechaLanzamiento()->format('d/m/Y');
            } else {
                $fecha = $juego->getFechaLanzamiento();
            }

            $data = [
                'id' => $juego->getId(),
                'nombre' => $juego->getNombre(),
                'fecha_lanzamiento' => $fecha,
                'genero' => $juego->getGenero(),
                'plataforma' => $juego->getPlataforma(),
            ];

            return new JsonResponse($data, Response::HTTP_OK);
        }
        return new JsonResponse(['error' => 'No existe el juego con id ' . $id], Response::HTTP_NOT_FOUND);
    }

    /**
     * @Route("/juegos", name="add_juego", methods={"POST"})
     */
    public function add_juego(Request $request): JsonResponse {
        $data = $json_decode($request->getContent(), true);

        $nombre = $data['nombre'];
        $fecha_lanzamiento = $data['fecha_lanzamiento'];
        $genero = $data['genero'];
        $plataforma = $data['plataforma'];

        if (empty($nombre)) {
            return new JsonResponse(['error' => 'Faltan parámetros'], Response::HTTP_PARTIAL_CONTENT);
        }

        $juego = new Juego();
        $juego->setNombre($nombre);
        $juego->setGenero($genero);
        if ($fecha_lanzamiento) {
            $juego->setFechaLanzamiento(DateTime::createFromFormat('d/m/Y', $fecha_lanzamiento));
        }
        $juego->setPlataforma($plataforma);

        $em = $this->getDoctrine()->getManager();
        $em->persist($juego);
        $em->flush();

        return new JsonResponse(['respuesta' => 'Juego añadido correctamente'], Response::HTTP_OK);
    }

    /**
     * @Route("/juegos/{id}", name="delete_juego", methods={"DELETE"})
     */
    public function delete_juego($id): JsonResponse {
        $juego = $this->getDoctrine()
                ->getRepository(Juego::class)
                ->find($id);
        if ($juego) {
            $em = $this->getDoctrine()->getManager();

            $em->remove($juego);
            $em->flush();

            return new JsonResponse(['respuesta' => 'Juego borrado correctamente'], Response::HTTP_OK);
        }
        return new JsonResponse(['error' => 'No existe el juego con id ' . $id], Response::HTTP_NOT_FOUND);
    }

    /**
     * @Route("/juegos/add/{idJuego}/{idLista}", name="anadir_a_lista", methods={"PUT"})
     */
    public function anadir_a_lista($idJuego, $idLista, Request $request, ParameterBagInterface $params, UserProviderInterface $userProvider) {
        $em = $this->getDoctrine()->getManager();
        $auth = new JwtAuthenticator($em, $params);

        $credenciales = $auth->getCredentials($request);

        $usuario = $auth->getUser($credenciales, $userProvider);

        if ($usuario) {
            $juego = $this->getDoctrine()
                    ->getRepository(Juego::class)
                    ->find($idJuego);

            $lista = $this->getDoctrine()
                    ->getRepository(Lista::class)
                    ->find($idLista);

            $arr_juegos = $lista->getJuegos();

            foreach ($arr_juegos as $juegoEnLista) {
                if ($juegoEnLista == $juego) {
                    return new JsonResponse(['error' => 'El juego ya está añadido a la lista'], Response::HTTP_BAD_REQUEST);
                }
            }

            $lista->addJuego($juego);

            $em = $this->getDoctrine()->getManager();
            $em->persist($lista);
            $em->flush();

            return new JsonResponse(['respuesta' => 'Juego añadido correctamente a la lista'], Response::HTTP_OK);
        }
        return new JsonResponse(['error' => 'Usuario no logueado'], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @Route("/juegos/borrar/{idJuego}/{idLista}", name="eliminar_juego_lista", methods={"DELETE"})
     */
    public function eliminar_juego_lista($idJuego, $idLista, Request $request, ParameterBagInterface $params, UserProviderInterface $userProvider) {
        $em = $this->getDoctrine()->getManager();
        $auth = new JwtAuthenticator($em, $params);

        $credenciales = $auth->getCredentials($request);

        $usuario = $auth->getUser($credenciales, $userProvider);

        if ($usuario) {
            $juego = $this->getDoctrine()
                    ->getRepository(Juego::class)
                    ->find($idJuego);

            $lista = $this->getDoctrine()
                    ->getRepository(Lista::class)
                    ->find($idLista);

            if ($juego) {
                $lista->removeJuego($juego);

                $em = $this->getDoctrine()->getManager();
                $em->persist($lista);
                $em->flush();

                return new JsonResponse(['respuesta' => 'Juego borrado correctamente de la lista'], Response::HTTP_OK);
            }
            return new JsonResponse(['error' => 'No existe el juego'], Response::HTTP_NOT_FOUND);
        }
        return new JsonResponse(['error' => 'Usuario no logueado'], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @Route("/juegos/{id}", name="update_juego", methods={"PUT"})
     */
    public function update_juego($id, Request $request): JsonResponse {
        $juego = $this->getDoctrine()
                ->getRepository(Juego::class)
                ->find($id);
        if ($juego) {
            $data = json_decode($request->getContent(), true);

            $nombre = $data['nombre'];
            $fecha_lanzamiento = $data['fecha_lanzamiento'];
            $genero = $data['genero'];
            $plataforma = $data['plataforma'];

            if ($nombre) {
                $juego->setNombre($nombre);
            }
            if ($fecha_lanzamiento) {
                $juego->setFechaLanzamiento(DateTime::createFromFormat('d/m/Y', $fecha_lanzamiento));
            }
            if ($genero) {
                $juego->setGenero($genero);
            }
            if ($plataforma) {
                $juego->setPlataforma($plataforma);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($juego);
            $em->flush();

            return new JsonResponse(['respuesta' => 'Juego actualizado correctamente'], Response::HTTP_OK);
        }
        return new JsonResponse(['error' => 'No existe el juego con id ' . $id], Response::HTTP_NOT_FOUND);
    }

}
