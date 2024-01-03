<?php

namespace App\Controller;

use App\Entity\IndexAlameda;
use App\Entity\Section;
use App\Form\IndexAlamedaType;
use App\Form\IndexSectionType;
use App\Repository\IndexAlamedaRepository;
use App\Repository\SectionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/index/alameda")
 *
 * @IsGranted("ROLE_ADMIN")
 */
class IndexAlamedaController extends AbstractController
{
    /**
     * @Route("/", name="index_alameda_index", methods={"GET"})
     */
    public function index(IndexAlamedaRepository $indexAlamedaRepository): Response
    {
        return $this->render('index_alameda/index.html.twig', [
            'index_alamedas' => $indexAlamedaRepository->findBy(['base' => 'index']),
//            'datosIndex' => $indexAlamedaRepository->findAll()[0],
        ]);
    }

    /**
     * @Route("/new", name="index_alameda_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $indexAlameda = new IndexAlameda();
        $form = $this->createForm(IndexAlamedaType::class, $indexAlameda);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($indexAlameda);
            $entityManager->flush();

            return $this->redirectToRoute('index_alameda_index');
        }

        return $this->render('index_alameda/new.html.twig', [
            'index_alameda' => $indexAlameda,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="index_alameda_show", methods={"GET"})
     */
    public function show(IndexAlameda $indexAlameda): Response
    {
        return $this->render('index_alameda/show.html.twig', [
            'index_alameda' => $indexAlameda,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="index_alameda_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, IndexAlameda $indexAlameda): Response
    {
        $form = $this->createForm(IndexAlamedaType::class, $indexAlameda);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->container->get('doctrine')->getManager()->flush();

            return $this->redirectToRoute('index_alameda_index');
        }

        return $this->render('index_alameda/edit.html.twig', [
            'index_alameda' => $indexAlameda,
            'datosIndex' => $indexAlameda,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="index_alameda_delete", methods={"DELETE"})
     */
    public function delete(Request $request, IndexAlameda $indexAlameda): Response
    {
        if ($this->isCsrfTokenValid('delete'.$indexAlameda->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($indexAlameda);
            $entityManager->flush();
        }

        return $this->redirectToRoute('index_alameda_index');
    }

    /**
     * @Route("/admin/index/section/{id}", methods="GET", name="admin_index_list_section")
     */
    public function getSectionPrincipal(IndexAlameda $indexAlameda): JsonResponse
    {
        return $this->json(
            $indexAlameda->getSection(),
            200,
            [],
            [
                'groups' => ['main'],
            ]
        );
    }

    /**
     * @Route("/admin/index/section/{id}/reorder", methods="POST", name="admin_principal_reorder_section")
     */
    public function reorderPrincipalSections(IndexAlameda $indexAlameda, EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        $orderedIds = json_decode($request->getContent(), true);

        if (null === $orderedIds) {
            return $this->json(['detail' => 'Datos Inválidos'], 400);
        }

        // from (position)=>(id) to (id)=>(position)
        $orderedIds = array_flip($orderedIds);

        foreach ($indexAlameda->getSection() as $reference) {
            $reference->setOrden($orderedIds[$reference->getId()]);
        }

        $entityManager->flush();

        return $this->json(
            $indexAlameda->getSection(),
            200,
            [],
            [
                'groups' => ['main'],
            ]
        );
    }

    /**
     * @Route("/agregarSeccion/{id}", name="index_agregar_seccion", methods={"GET", "POST"})
     *
     * @return RedirectResponse|Response
     */
    public function agregarSeccion(Request $request, IndexAlameda $indexAlameda, EntityManagerInterface $entityManager, SectionRepository $sectionRepository, IndexAlamedaRepository $indexAlamedaRepository)
    {
        $form = $this->createForm(IndexSectionType::class, $indexAlameda);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $id_section = $form->get('section')->getData();
            $seccion = $sectionRepository->find($id_section);
            $indexAlameda->addSection($seccion);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($indexAlameda);
            $entityManager->flush();

            return $this->redirectToRoute('index_alameda_index', [
                'index_alamedas' => $indexAlamedaRepository->findAll(),
                'datosIndex' => $indexAlamedaRepository->findAll()[0],
            ]);
        }

        return $this->render('index_alameda/vistaAgregaSection.html.twig', [
            'index' => $indexAlameda,
            'form' => $form->createView(),
        ]);
    }
}
