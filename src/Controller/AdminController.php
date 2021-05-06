<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Tag;
use App\Entity\Article;
use App\Form\CategoryType;
use App\Form\TagType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use App\Repository\TagRepository;


/**
 * @Route("/admin/", name="admin_")
 */
class AdminController extends AbstractController
{
        /**
     * @Route("dashboard", name="dashboard")
     */
    public function dashboard(CategoryRepository $categoryRepository, TagRepository $tagRepository, UserRepository $userRepository): Response
    {
        return $this->render('admin/dashboard.html.twig', [
            'categories' => $categoryRepository->findAll(),
            'tags' => $tagRepository->findAll(),
            'users' => $userRepository->findAll()
        ]);
    }

    /**
     * @Route("dashboardpanel", name="dashboard_panel")
     */
    public function panel(Request $request, TagRepository $tagRepository, CategoryRepository $categoryRepository): Response
    {
        $tags = $tagRepository->findAll();
        $categories = $categoryRepository->findAll();

        return $this->render('admin/dashboardPanel.html.twig', [
            'tags' => $tags,
            'categories' => $categories,
        ]);
    }
}
    /*************************** CATEGORY ROUTES *************************/
    // déplacées dans le controleur category pour interface unique

    //     /**
    //  * @Route("category", name="category_index", methods={"GET"}) 
    //  */
    // public function index(CategoryRepository $categoryRepository): Response
    // {
    //     return $this->render('admin/category/index.html.twig', [
    //         'categories' => $categoryRepository->findAll(),
    //     ]);
    // }
    // /**
    //  * @Route("category/new", name="category_new", methods={"GET","POST"})
    //  */
    // public function newCategory(Request $request): Response
    // {
    //     $category = new Category();
    //     $form = $this->createForm(CategoryType::class, $category);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $entityManager = $this->getDoctrine()->getManager();
    //         $entityManager->persist($category);
    //         $entityManager->flush();

    //         return $this->redirectToRoute('admin_category_index');
    //     }

    //     return $this->render('admin/category/new.html.twig', [
    //         'category' => $category,
    //         'form' => $form->createView(),
    //     ]);
    // }

    // /**
    //  * @Route("category/{category}", name="category_show", methods={"GET"})
    //  */
    // public function showCategory(Category $category): Response
    // {
    //     return $this->render('admin/category/show.html.twig', [
    //         'category' => $category,
    //     ]);
    // }

    // /**
    //  * @Route("category/{id}/edit", name="category_edit", methods={"GET","POST"})
    //  */
    // public function editCategory(Request $request, Category $category): Response
    // {
    //     $form = $this->createForm(CategoryType::class, $category);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $this->getDoctrine()->getManager()->flush();

    //         return $this->redirectToRoute('admin_category_index');
    //     }

    //     return $this->render('admin/category/edit.html.twig', [
    //         'category' => $category,
    //         'form' => $form->createView(),
    //     ]);
    // }

    // /**
    //  * @Route("category/{id}", name="category_delete", methods={"DELETE"})
    //  */
    // public function deleteCategory(Request $request, Category $category): Response
    // {
    //     if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('_token'))) {
    //         $entityManager = $this->getDoctrine()->getManager();
    //         $entityManager->remove($category);
    //         $entityManager->flush();
    //     }

    //     return $this->redirectToRoute('admin_category_index');
    // }




    /*************************** TAG ROUTES *************************
     * déplacées dans le conroleur Tag pour unterface Unique

       /**
     * @Route("tag", name="tag_index", methods={"GET"})
     */
    // public function indexTag(TagRepository $tagRepository): Response
    // {
    //     return $this->render('admin/tag/index.html.twig', [
    //         'tags' => $tagRepository->findAll(),
    //     ]);
    // }

    // /**
    //  * @Route("tag/new", name="tag_new", methods={"GET","POST"})
    //  */
    // public function newTag(Request $request): Response
    // {
    //     $tag = new Tag();
    //     $form = $this->createForm(TagType::class, $tag);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $entityManager = $this->getDoctrine()->getManager();
    //         $entityManager->persist($tag);
    //         $entityManager->flush();

    //         return $this->redirectToRoute('admin_tag_index');
    //     }

    //     return $this->render('admin/tag/new.html.twig', [
    //         'tag' => $tag,
    //         'form' => $form->createView(),
    //     ]);
    // }

    // /**
    //  * @Route("tag/{tag}", name="tag_show", methods={"GET"})
    //  */
    // public function showTag(Tag $tag): Response
    // {
    //     return $this->render('admin/tag/show.html.twig', [
    //         'tag' => $tag,
    //     ]);
    // }

    // /**
    //  * @Route("tag/{id}/edit", name="tag_edit", methods={"GET","POST"})
    //  */
    // public function editTag(Request $request, Tag $tag): Response
    // {
    //     $form = $this->createForm(TagType::class, $tag);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $this->getDoctrine()->getManager()->flush();

    //         return $this->redirectToRoute('admin_tag_index');
    //     }

    //     return $this->render('admin/tag/edit.html.twig', [
    //         'tag' => $tag,
    //         'form' => $form->createView(),
    //     ]);
    // }

    // /**
    //  * @Route("tag/{id}", name="tag_delete", methods={"DELETE"})
    //  */
    // public function deleteTag(Request $request, Tag $tag): Response
    // {
    //     if ($this->isCsrfTokenValid('delete' . $tag->getId(), $request->request->get('_token'))) {
    //         $entityManager = $this->getDoctrine()->getManager();
    //         $entityManager->remove($tag);
    //         $entityManager->flush();
    //     }

    //     return $this->redirectToRoute('admin_tag_index');
    // }

