<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller\Admin;

use App\Entity\Post;
use App\Entity\PostCard;
use App\Entity\User;
use App\Form\PostType;
use App\Form\CardPostType;
use App\Repository\PostRepository;
use App\Security\PostVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;

/**
 * Controller used to manage blog contents in the backend.
 *
 * Please note that the application backend is developed manually for learning
 * purposes. However, in your real Symfony application you should use any of the
 * existing bundles that let you generate ready-to-use backends without effort.
 * See https://symfony.com/bundles
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
#[Route('/admin/post')]
#[IsGranted('ROLE_ADMIN')]
class BlogController extends AbstractController
{
    /**
     * Lists all Post entities.
     *
     * This controller responds to two different routes with the same URL:
     *   * 'admin_post_index' is the route with a name that follows the same
     *     structure as the rest of the controllers of this class.
     *   * 'admin_index' is a nice shortcut to the backend homepage. This allows
     *     to create simpler links in the templates. Moreover, in the future we
     *     could move this annotation to any other controller while maintaining
     *     the route name and therefore, without breaking any existing link.
     */
    #[Route('/', name: 'admin_index', methods: ['GET'])]
    #[Route('/', name: 'admin_post_index', methods: ['GET'])]
    public function index(
        #[CurrentUser] User $user,
        PostRepository $posts,
    ): Response {   
        $authorPosts = $posts->findBy(['author' => $user], ['publishedAt' => 'DESC']);
        
        return $this->render('admin/blog/index.html.twig', ['posts' => $authorPosts]);
    }

    /**
     * Creates a new Post entity.
     *
     * NOTE: the Method annotation is optional, but it's a recommended practice
     * to constraint the HTTP methods each controller responds to (by default
     * it responds to all methods).
     */
    #[Route('/new', name: 'admin_post_new', methods: ['GET', 'POST'])]
    public function new(
        #[CurrentUser] User $user,
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response {
        $post = new Post();
        $post->setAuthor($user);
       
        // See https://symfony.com/doc/current/form/multiple_buttons.html
        $form = $this->createForm(PostType::class, $post)
            ->add('saveAndCreateNew', SubmitType::class)
        ;

        $form->handleRequest($request);

        $post_card = new PostCard();
        $card_form = $this->createForm(CardPostType::class, $post_card)
        ->add('saveAndCreateNew', SubmitType::class);

        $card_form->handleRequest($request);
        //gettitlecard
        
       
        // the isSubmitted() method is completely optional because the other
        // isValid() method already checks whether the form is submitted.
        // However, we explicitly add it to improve code readability.
        // See https://symfony.com/doc/current/forms.html#processing-forms

        /***code for card save data start */
        
        
        //if ($form->isSubmitted() && $form->isValid()) {
        if ($form->isSubmitted()) {
           
            $post->setContent('');
            $entityManager->persist($post);
            $entityManager->flush();
            
            $getpost_id = $post->getId();
           

            // Flash messages are used to notify the user about the result of the
            // actions. They are deleted automatically from the session as soon
            // as they are accessed.
            // See https://symfony.com/doc/current/controller.html#flash-messages
            $this->addFlash('success', 'post.created_successfully');

            /** @var SubmitButton $submit */
            $submit = $form->get('saveAndCreateNew'); 

			
            $cart_content_count = $request->request->get('card_data');
            //save card data to new table
            $cart_content_count = $request->request->get('card_data');
            if($getpost_id!=''){
                if($cart_content_count!=''){
                    $cart_content_count = $request->request->get('card_data');
                    for($ik = 1; $ik<=$cart_content_count;$ik++){

                        $card_title = $request->request->get('card_title'.$ik);
                        $card_image = $request->request->get('card_image'.$ik);
                        $card_paragraph = $request->request->get('card_paragraph'.$ik);
                        $card_template = $request->request->get('card_template'.$ik);
                        $create_para_ary = explode(',',$card_paragraph);
                    
                        $json_paragraph = json_encode($create_para_ary);

                        $post_card = new PostCard();
                        $post_card->setCardTitle($card_title);
                        $post_card->setcardImage($card_image);
                        $post_card->setparagraph($json_paragraph);
                        $post_card->setTemplate($card_template);
                        $post_card->setPostId($getpost_id);
                        $entityManager->persist($post_card);
                        $entityManager->flush();

                    }
                    
                }
            }
            /***code for card save data end */

            if ($submit->isClicked()) {
                return $this->redirectToRoute('admin_post_new');
            }

            return $this->redirectToRoute('admin_post_index');
        }

        return $this->render('admin/blog/new.html.twig', [
            'post' => $post,
            'form' => $form,
            'card_form' => $card_form
        ]);
    }

    /**
     * Finds and displays a Post entity.
     */
    #[Route('/{id<\d+>}', name: 'admin_post_show', methods: ['GET'])]
    public function show(Post $post, EntityManagerInterface $entityManager): Response
    {
        // This security check can also be performed
        // using a PHP attribute: #[IsGranted('show', subject: 'post', message: 'Posts can only be shown to their authors.')]
        $this->denyAccessUnlessGranted(PostVoter::SHOW, $post, 'Posts can only be shown to their authors.');

       //get postcard data and modify the array

        $repository = $entityManager->getRepository(PostCard::class);
       
        $postcard_data = $repository->findBy(['post_id' => $post->getId()],array('sorting_new' => 'ASC'));

        $card_array=array();
        
        if(!empty($postcard_data)){
            foreach($postcard_data as $podata){
                $card_id = $podata->getId();
                $data_json = json_decode($podata->getParagraph());
                $card_array[$card_id] = array("card_id"=>$podata->getId(),"card_title"=>$podata->getCardTitle(),"card_image"=>$podata->getCardImage(),"template"=>$podata->getTemplate(),'paragraph_n'=>$data_json);                     
            }
        }
       
         //get postcard data end
        return $this->render('admin/blog/show.html.twig', [ 
            'post' => $post,
            'card_data' => $card_array,
        ]);
    }

    /**
     * Displays a form to edit an existing Post entity.
     */
    #[Route('/{id<\d+>}/edit', name: 'admin_post_edit', methods: ['GET', 'POST'])]
    #[IsGranted('edit', subject: 'post', message: 'Posts can only be edited by their authors.')]
    public function edit(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        $postcard = new PostCard();
        $second_form = $this->createForm(CardPostType::class, $postcard);
        $second_form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'post.updated_successfully');

            return $this->redirectToRoute('admin_post_edit', ['id' => $post->getId()]);
        }

        
        
        $data = $second_form->getData();
        
        if ($second_form->isSubmitted()) {
            $paragraph = $_REQUEST['paragraph'];
            $post_id = $second_form["post_id"]->getData();
            $paragraph_new = json_encode($paragraph);
           
            $postcard->setPostId($post_id);
            
            $postcard->setParagraph($paragraph_new);  
            $entityManager->persist($postcard);
            $entityManager->flush();

            return $this->redirectToRoute('admin_post_edit', ['id' => $post->getId()]);
        }

        $repository = $entityManager->getRepository(PostCard::class);
        // get by postid
        $postcard_data = $repository->findBy(['post_id' => $post->getId()],array('sorting_new' => 'ASC'));
       
        $card_array=array();
        if(!empty($postcard_data)){
			foreach($postcard_data as $podata){
				$card_id = $podata->getId();
				$data_json = json_decode($podata->getParagraph());
				$card_array[$card_id] = array("card_id"=>$podata->getId(),"card_title"=>$podata->getCardTitle(),"card_image"=>$podata->getCardImage(),"template"=>$podata->getTemplate(),'paragraph_n'=>$data_json);                       
			}
		}
      
        return $this->render('admin/blog/edit.html.twig', [
            'post' => $post,
            'form' => $form,
            'card_form' => $second_form,
            'card_data' => $card_array
        ]);  

    }

    /**
     * Deletes a Post entity.
     */
    #[Route('/{id}/delete', name: 'admin_post_delete', methods: ['POST'])]
    #[IsGranted('delete', subject: 'post')]
    public function delete(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        /** @var string|null $token */
        $token = $request->request->get('token');

        if (!$this->isCsrfTokenValid('delete', $token)) {
            return $this->redirectToRoute('admin_post_index');
        }

        // Delete the tags associated with this blog post. This is done automatically
        // by Doctrine, except for SQLite (the database used in this application)
        // because foreign key support is not enabled by default in SQLite
        $post->getTags()->clear();

        $entityManager->remove($post);
        $entityManager->flush();

        $this->addFlash('success', 'post.deleted_successfully');

        return $this->redirectToRoute('admin_post_index');
    }

    #[Route('/{id}/deletepostcard', name: 'admin_post_card_delete', methods: ['GET', 'POST'])]
    public function deletePostCard(Request $request, Postcard $postcard, EntityManagerInterface $entityManager, PersistenceManagerRegistry $doctrine, $id): Response
    {     
        $entityManager = $doctrine->getManager();
        $postcard = $entityManager->getRepository(Postcard::class)->find($id);

        if (!$postcard) {
            throw $this->createNotFoundException('The post card does not exist');
            
        }
		
		
        $entityManager->remove($postcard);
        $entityManager->flush();

        return $this->redirectToRoute('admin_post_index');
    }

    #[Route('/{id}/editpostcard', name: 'admin_post_card_edit', methods: ['GET', 'POST'])]

    public function editPostCard(Request $request, PersistenceManagerRegistry $doctrine, $id)
    {
        $entityManager = $doctrine->getManager();
        $postcard = $entityManager->getRepository(Postcard::class)->find($id);

        $postcard_new = new PostCard();
        $second_form = $this->createForm(CardPostType::class, $postcard_new);
        $second_form->handleRequest($request);

        if (!$postcard) {
            throw $this->createNotFoundException('The postcard does not exist');
        }

        $card_title = $_REQUEST['cardTitle'];
        
        $paragraph = $_REQUEST['paragraph'];
        $paragraph_new = json_encode($paragraph);

       
        $card_image = $_REQUEST["cardImage"];
        $paragraph = $_REQUEST["paragraph"];
        $template = $_REQUEST["template"];
       
        $post_id = $_REQUEST["post_id"];
        $paragraph_new = json_encode($paragraph);
       
      
        $postcard->setCardTitle($card_title);
        $postcard->setcardImage($card_image);
        $postcard->setparagraph($paragraph_new);
        $postcard->setTemplate($template);
        $entityManager->flush(); 
       
       // return $this->redirectToRoute('admin/post/'.$post_id.'/edit', ['id' => $id]);   
        return $this->redirectToRoute('admin_post_edit', ['id' => $post_id]);    
    }

    #[Route('/{id}/editpostcardposition', name: 'admin_post_card_edit_position', methods: ['GET', 'POST'])]

    public function editPostCardPosition(Request $request, PersistenceManagerRegistry $doctrine, $id)
    {
        $card_ids = $_REQUEST['card_ids'];
        $entityManager = $doctrine->getManager();
        $key = 1;
        foreach($card_ids as $cids){
            $postcard = $entityManager->getRepository(Postcard::class)->find($cids); 
            echo $key;          
            $postcard->setSortingNew($key);
            $entityManager->flush(); 
            $key++;
        }
        die;
    }
	
	//delete paragraph of cards
    #[Route('/{id}/delete_card_para', name: 'admin_post_delete_card_para', methods: ['GET', 'POST'])]

    public function deleteCardPara(Request $request, PersistenceManagerRegistry $doctrine, $id)
    {
        $card_id = $request->request->get('card_id');
        $para_id = $request->request->get('para_id') - 1;
        $entityManager = $doctrine->getManager();
        $key = 1;
        $postcard = $entityManager->getRepository(Postcard::class)->find($card_id);
        $paragraph_new = json_decode($postcard->getParagraph());
        if(!empty($paragraph_new)){
            unset($paragraph_new[$para_id]);
        }
        $imp = implode(",", $paragraph_new);
        $exp = explode(',',$imp);
        $paragraph_new = json_encode($exp);
       
        $postcard->setparagraph($paragraph_new);
        $entityManager->flush(); 
		
		$return_arr = array("message" => 'success'); 
		 
		echo json_encode($return_arr);
		die;
    }
	
	//delete all paragraph of cards
    #[Route('/{id}/delete_card_allpara', name: 'admin_post_delete_card_allpara', methods: ['GET', 'POST'])]

    public function deleteCardAllpara(Request $request, PersistenceManagerRegistry $doctrine)
    {
		
        $card_id = $request->request->get('card_id');
      
        $entityManager = $doctrine->getManager();
       
        $postcard = $entityManager->getRepository(Postcard::class)->find($card_id);
       
        $postcard->setparagraph('');
        $entityManager->flush(); 
		
		$return_arr = array("message" => 'success'); 
		 
		echo json_encode($return_arr);
		die;
    }
   
}
    