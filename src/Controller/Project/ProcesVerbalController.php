<?php

/*
 * This file is part of the Incipio package.
 *
 * (c) Florian Lefevre
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller\Project;

use App\Entity\Project\Etude;
use App\Entity\Project\ProcesVerbal;
use App\Form\Project\ProcesVerbalSubType;
use App\Service\Project\DocTypeManager;
use App\Service\Project\EtudePermissionChecker;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ProcesVerbalController extends AbstractController
{
    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     * @Route(name="project_procesverbal_ajouter", path="/suivi/procesverbal/ajouter/{id}/{type}", methods={"GET","HEAD","POST"})
     *
     * @param string $type PVRF or PVRI
     *
     * @return RedirectResponse|Response
     */
    public function add(
        Request $request,
        Etude $etude,
        $type,
        EtudePermissionChecker $permChecker,
        DocTypeManager $docTypeManager
    ) {
        $em = $this->getDoctrine()->getManager();

        if ($permChecker->confidentielRefus($etude, $this->getUser())) {
            throw new AccessDeniedException('Cette étude est confidentielle');
        }

        if ((null === $etude->getPvr() && 'pvr' === $type) || 'pvi' === $type) {
            $proces = new ProcesVerbal();

            if ('pvr' == $type) {
                $etude->setPvr($proces);
            } elseif ('pvi' == $type) {
                $etude->addPvi($proces);
            }

            $proces->setType($type);
        } else {
            $proces = $etude->getPvr();
        }

        $form = $this->createForm(
            ProcesVerbalSubType::class,
            $proces,
            ['type' => $type, 'etude' => $etude, 'prospect' => $etude->getProspect()]
        );
        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $docTypeManager->checkSaveNewEmploye($proces);
                $em->persist($proces);
                $em->flush();
                $this->addFlash('success', 'PV ajouté');

                return $this->redirectToRoute('project_etude_voir', ['nom' => $etude->getNom(), '_fragment' => 'tab3']);
            }
        }

        return $this->render('Project/ProcesVerbal/ajouter.html.twig', [
            'etude' => $etude,
            'form' => $form->createView(),
            'type' => $type,
        ]);
    }

    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     * @Route(name="project_procesverbal_modifier", path="/suivi/procesverbal/modifier/{id}", methods={"GET","HEAD","POST"})
     *
     * @return RedirectResponse|Response
     */
    public function modifier(
        Request $request,
        ProcesVerbal $procesverbal,
        EtudePermissionChecker $permChecker,
        DocTypeManager $docTypeManager
    ) {
        $em = $this->getDoctrine()->getManager();

        $etude = $procesverbal->getEtude();

        if ($permChecker->confidentielRefus($etude, $this->getUser())) {
            throw new AccessDeniedException('Cette étude est confidentielle');
        }

        $form = $this->createForm(
            ProcesVerbalSubType::class,
            $procesverbal,
            ['type' => $procesverbal->getType(), 'etude' => $etude, 'prospect' => $etude->getProspect()]
        );
        $deleteForm = $this->createDeleteForm($procesverbal->getId());
        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $docTypeManager->checkSaveNewEmploye($procesverbal);
                $em->persist($procesverbal);
                $em->flush();
                $this->addFlash('success', 'PV modifié');

                return $this->redirectToRoute('project_etude_voir', ['nom' => $etude->getNom(), '_fragment' => 'tab3']);
            }
        }

        return $this->render('Project/ProcesVerbal/modifier.html.twig', [
            'form' => $form->createView(),
            'delete_form' => $deleteForm->createView(),
            'etude' => $procesverbal->getEtude(),
            'type' => $procesverbal->getType(),
            'procesverbal' => $procesverbal,
        ]);
    }

    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     * @Route(name="project_procesverbal_supprimer", path="/suivi/procesverbal/supprimer/{id}", methods={"GET","HEAD","POST"})
     *
     * @return RedirectResponse
     */
    public function delete(Request $request, ProcesVerbal $procesVerbal, EtudePermissionChecker $permChecker)
    {
        $form = $this->createDeleteForm($procesVerbal->getId());
        $form->handleRequest($request);
        $etude = $procesVerbal->getEtude();

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if ($permChecker->confidentielRefus($etude, $this->getUser())) {
                throw new AccessDeniedException('Cette étude est confidentielle');
            }

            $em->remove($procesVerbal);
            $em->flush();
            $this->addFlash('success', 'PV supprimé');
        } else {
            $this->addFlash('danger', 'Le formulaire contient des erreurs.');
        }

        return $this->redirectToRoute('project_etude_voir', ['nom' => $etude->getNom(), '_fragment' => 'tab3']);
    }

    private function createDeleteForm($id_pv)
    {
        return $this->createFormBuilder(['id' => $id_pv])
            ->add('id', HiddenType::class)
            ->getForm();
    }
}
