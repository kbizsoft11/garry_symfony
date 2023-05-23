<?php

namespace App\Controller\Admin;

// src/Controller/UserController.php
// namespace App\Controller;

use App\Entity\PostCard;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/admin/post')]
#[IsGranted('ROLE_ADMIN')]

class UserCrudController extends AbstractController
{

    private $em;


    /**
     * @Route("/users", name="user_index", methods={"GET"})
     */
    #[Route('/users', name: 'admin_user_list', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager, #[CurrentUser] User $user, ): Response
    {
        $repository = $entityManager->getRepository(User::class);
        $users = $repository->findAll();
        return $this->render('admin/user/index.html.twig', ['users' => $users]);
    }

    /* Add #[Route('/add_user', name: 'admin_user_list', methods: ['GET', 'POST'])] */


    /* Add new User */
    #[Route('/add_user', name: 'admin_add_user', methods: [ 'GET','POST']), IsGranted('ROLE_ADMIN')]

    public function admin_add_user(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, ): Response
    {
        // Create a new instance of the User entity
        $user = new User();

        $form = $this->createForm(\App\Form\UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $name = $user->getFullName();
            $email = $user->getEmail();
            $username = $user->getUsername();
            $plainPassword = $user->getPassword();

            // Set the properties of the user entity
            $user_new = new User();
            $user_new->setFullName($name);
            $user_new->setUsername($username);
            $user_new->setEmail($email);
            $user_new->setRoles(['ROLE_USER']);
            // See https://symfony.com/doc/5.4/security.html#registering-the-user-hashing-passwords
            $hashedPassword = $passwordHasher->hashPassword($user_new, $plainPassword);
            $user_new->setPassword($hashedPassword);

            $entityManager->persist($user_new);
            $entityManager->flush();

            $this->addFlash('success', 'User added successfully.');

            return $this->redirectToRoute('admin_user_list');
        }


        // Redirect to an appropriate page
        return $this->render('admin/user/add_user.html.twig', [
            'form' => $form,
        ]);

    }

    /**
     * Displays a form to edit an existing User entity.
     */
    #[Route('/{id<\d+>}/edit_user', name: 'admin_user_edit', methods: ['GET', 'POST']), IsGranted('ROLE_ADMIN')]
    public function admin_user_edit(Request $request, User $user, EntityManagerInterface $entityManager, ): Response
    {
        $form = $this->createForm(\App\Form\UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->flush();

            $this->addFlash('success', 'user.updated_successfully');

            return $this->redirectToRoute('admin_user_edit', ['id' => $user->getId()]);
        }

        return $this->render('admin/user/edit.html.twig', [
            'form' => $form ,
            'id' => $user->getId()
        ]);

    }

    #[Route('/{id<\d+>}/delete', name: 'admin_user_delete', methods: ['GET', 'POST'])]

    public function admin_user_delete(Request $request, User $user, EntityManagerInterface $entityManager, ): Response
    {	
		$entityManager->remove($user);
        $entityManager->flush();
		$repository = $entityManager->getRepository(User::class);
        $users = $repository->findAll();
        return $this->render('admin/user/index.html.twig', ['users' => $users]);
    }


}
