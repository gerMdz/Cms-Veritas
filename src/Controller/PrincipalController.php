<?php

namespace App\Controller;

use App\Entity\Principal;
use App\Form\PrincipalType;
use App\Form\SectionAddType;
use App\Repository\PrincipalRepository;
use App\Repository\SectionRepository;
use App\Service\UploaderHelper;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/admin/principal')]
class PrincipalController extends BaseController
{
    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    #[Route(path: '/', name: 'principal_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function index(PrincipalRepository $principalRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $bus = $request->get('busq');
        $queryPrincipales = $principalRepository->queryFindAllPrincipals($bus);
        $principales = $paginator->paginate(
            $queryPrincipales, /* query NOT result */
            $request->query->getInt('page', 1)/* page number */,
            15/* limit per page */
        );

        return $this->render('admin/principal/index.html.twig', [
            'principals' => $principales,
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route(path: '/new', name: 'principal_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, UploaderHelper $uploaderHelper, EntityManagerInterface $entityManager): Response
    {
        $principal = new Principal();
        $user = $this->getUser();
        $ahora = new DateTime('now');
        $principal->setAutor($user);
        $principal->setCreatedAt($ahora);
        $principal->setUpdatedAt($ahora);
        $form = $this->createForm(PrincipalType::class, $principal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Principal $principal */
            $principal = $form->getData();

            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form['imageFile']->getData();
            $linkRoute = $form['linkRoute']->getData();

            if ($uploadedFile) {
                $newFilename = $uploaderHelper->uploadEntradaImage($uploadedFile, false);
                $principal->setImageFilename($newFilename);
            }
            if (null !== $principal->getLinkRoute()) {
                $principal->setLinkRoute($principal->getLinkRoute());
            } else {
                $principal->setLinkRoute($principal->getTitulo());
            }
            $entityManager->persist($principal);
            $entityManager->flush();

            return $this->redirectToRoute('principal_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/principal/new.html.twig', [
            'principal' => $principal,
            'form' => $form,
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route(path: '/new-for-assistant', name: 'principal_new_assistant', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function newAssistant(Request $request, UploaderHelper $uploaderHelper, EntityManagerInterface $entityManager): Response
    {
        $principal = new Principal();
        $user = $this->getUser();
        $ahora = new DateTime('now');
        $principal->setAutor($user);
        $principal->setCreatedAt($ahora);
        $principal->setUpdatedAt($ahora);
        $form = $this->createForm(PrincipalType::class, $principal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Principal $principal */
            $principal = $form->getData();

            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form['imageFile']->getData();
            $linkRoute = $form['linkRoute']->getData();

            if ($uploadedFile) {
                $newFilename = $uploaderHelper->uploadEntradaImage($uploadedFile, false);
                $principal->setImageFilename($newFilename);
            }
            if (null !== $principal->getLinkRoute()) {
                $principal->setLinkRoute($principal->getLinkRoute());
            } else {
                $principal->setLinkRoute($principal->getTitulo());
            }
            $entityManager->persist($principal);
            $entityManager->flush();

            return $this->redirectToRoute('admin');
        }

        return $this->render('admin/principal/newAssistant.html.twig', [
            'principal' => $principal,
            'form' => $form,
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route(path: '/{id}/edit', name: 'principal_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Principal $principal, UploaderHelper $uploaderHelper,
        EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PrincipalType::class, $principal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form['imageFile']->getData();
            $linkRoute = $form['linkRoute']->getData();
            if ($uploadedFile) {
                $newFilename = $uploaderHelper->uploadEntradaImage($uploadedFile, $principal->getImageFilename());
                $principal->setImageFilename($newFilename);
            }

            if ($linkRoute) {
                $principal->setLinkRoute($linkRoute);
            } else {
                $principal->setLinkRoute($principal->getTitulo());
            }

            $entityManager->flush();

            return $this->redirectToRoute('principal_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/principal/edit.html.twig', [
            'principal' => $principal,
            'form' => $form,
        ]);
    }

    #[Route(path: '/{id}/show', name: 'principal_show', methods: ['GET'])]
    public function show(Principal $principal, PrincipalRepository $repository): Response
    {
        $brotes = $repository->findByPrincipalParent($principal);
        if (!$brotes) {
            $brotes = null;
        }

        return $this->render('admin/principal/show.html.twig', [
            'principal' => $principal,
            'brotes' => $brotes,
        ]);
    }

    #[Route(path: '/{id}', name: 'principal_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Principal $principal, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$principal->getId(), $request->request->get('_token'))) {
            $entityManager->remove($principal);
            $entityManager->flush();
        }

        return $this->redirectToRoute('principal_index');
    }

    #[Route(path: '/section/{id}', methods: 'GET', name: 'admin_principal_list_section')]
    public function getSectionPrincipal(Principal $principal): JsonResponse
    {
        return $this->json(
            $principal->getSecciones(),
            200,
            [],
            [
                'groups' => ['main'],
            ]
        );
    }

    /**
     * @param Request $request
     * @param Principal $principal
     * @param EntityManagerInterface $entityManager
     * @param SectionRepository $sectionRepository
     * @param PrincipalRepository $principalRepository
     * @return RedirectResponse|Response
     */
    #[Route(path: '/agregarSeccion/{id}', name: 'principal_agregar_seccion', methods: ['GET', 'POST'])]
    public function agregarSeccion(Request $request, Principal $principal, EntityManagerInterface $entityManager,
                                   SectionRepository $sectionRepository,
                                   PrincipalRepository $principalRepository): RedirectResponse|Response
    {
        $form = $this->createForm(SectionAddType::class);
        $form->handleRequest($request);
        $this->requestStack->getSession()->set('principal_id', $principal->getId());

        if ($form->isSubmitted() && $form->isValid()) {
            $id_section = $form->get('section')->getData();
            $seccion = $sectionRepository->find($id_section);
            $principal->addSeccione($seccion);
            $entityManager->persist($principal);
            $entityManager->flush();

            if ($this->requestStack->getSession()->get('principal_id')) {
                $this->requestStack->getSession()->remove('principal_id');
            }

            return $this->redirectToRoute('principal_show', [
                'id' => $principal->getId(),
            ]);
        }

        return $this->render('admin/principal/vistaAgregaSection.html.twig', [
            'principal' => $principal,
            'form' => $form,
        ]);
    }
}
