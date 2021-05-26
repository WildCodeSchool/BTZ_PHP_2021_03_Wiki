<?php

namespace App\Controller;

use App\Entity\Version;
use App\Form\VersionType;
use App\Repository\VersionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/version")
 */
class VersionController extends AbstractController
{
    /**
     * @Route("/delete/{id}", name="version_delete",requirements={"id":"\d+"}, methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, Version $version): Response
    {
        $article = $version->getArticle();
        if ($this->isCsrfTokenValid('delete'.$version->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($version);
            $entityManager->flush();
        }


        return $this->redirectToRoute('article_versions', ['id'=>$article->getId()]);
    }

    /**
     * @Route("/validation/{id}", name="version_validation" ,requirements={"id":"\d+"}, methods={"GET"})
     * @IsGranted("ROLE_MODERATOR")
     */
    public function manageValidation(Version $version): Response
    {
        if ($version->getIsValidated()) {
            $version->setIsValidated(false);
        } else {
            $version->setIsValidated(true);
        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();
        return $this->redirectToRoute('unvalidated_articles');
    }

    /**
    * @Route("/publish/{id}", name="version_publish", requirements={"id":"\d+"}, methods={"GET"})
    * @IsGranted("ROLE_MODERATOR")
    */
    public function publishVersion(Version $version): Response
    {
        $article = $version->getArticle();
        $article->setCurrentVersion($version->getId());
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();
        return $this->redirectToRoute('article_show', ['id' => $article->getId()]);
    }
}
