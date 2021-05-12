<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{



    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/validation", name="user_validation", methods={"GET","POST"})
     */
    public function listValidation(Request $request, UserRepository $userRepository): Response
    {
        //If there's something in $_POST, we update the given user (id)
        if (isset($_POST) && !empty($_POST)) {
            $user = $userRepository->findOneBy(['id' => $_POST['id']]);
            $user->setValidated($_POST['form']['validated']);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('user_validation');
        }

        $users = $userRepository->findBy(['validated' => 0]);  //Within the DB, '0' means 'false'

        $forms = array();

        foreach ($users as $user) {
            //Create a form for each user and keep its ID as key
            $forms[$user->getId()] = $this->createFormBuilder($user)
                ->add('validated')
                ->getForm()
                ->handleRequest($request)
                ->createView();
        }

        return $this->render(
            'user/validation.html.twig',
            [
                'users' => $users,
                'forms' => $forms,
            ],
        );
    }

    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $user->setValidated(false);
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $encodedPassword = $passwordEncoder->encodePassword(
                $user,
                $form->get('plainPassword')->getData()
            );

            $user->setPassword($encodedPassword);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", requirements={"id"="\d+"}, name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/edit/{id}", requirements={"id"="\d+"}, name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, UserPasswordEncoderInterface $passwordEncoder, User $user): Response
    {
        //Create a custom form without the password field (to keep the last untouched)
        //The rest of fields are the same as in UserType
        $form = $this->createFormBuilder($user)
            ->add('email')
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Utilisateur' => 'ROLE_USER',
                    'Moderateur' => 'ROLE_MODERATOR',
                    'Administrateur' => 'ROLE_ADMIN'
                ],
                'expanded' => true,
                'multiple' => true,
                'label' => 'Rôles'
            ])
            ->add('firstname')
            ->add('lastname')
            ->add('cityAgency')
            ->add('validated')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}/", requirements={"id"="\d+"}, name="user_delete", methods={"POST"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }
}
