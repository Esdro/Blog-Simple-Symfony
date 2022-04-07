<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class HomeController extends AbstractController
{
    private $repoArticle;
    private $repoCategory;
    private $manager;

   public  function __construct(  ArticleRepository $repoArticle, CategoryRepository $repoCategory, EntityManagerInterface $manager)
    {
        $this->repoArticle = $repoArticle;
        $this->repoCategory = $repoCategory;
        $this->manager =$manager;
        
    }
    
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
       $articles = $this->repoArticle->findAll();
       $categories = $this->repoCategory->findAll();
        return $this->render('home/home.html.twig',[
           'articles' => $articles,
           'categories' => $categories,
        ]);
    }

    /**
     * @Route("/article/{id}", name="article")
     */
    public function showarticle($id): Response
    {
        $article = $this->repoArticle->find($id);
        return $this->render('home/article.html.twig',[
            'article'=> $article,
        ]);
    }

    /**
     * @Route("/category/{id}", name="category")
     */
    public function showcategory($id): Response
    {
        $artCategory = $this->repoCategory->find($id);
        $categories = $this->repoCategory->findAll();
        return $this->render('home/category.html.twig',[
            'artCategory' => $artCategory,
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/register", name="security_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $user = new User();
       $register_form = $this->createForm(RegisterType::class, $user);

       $register_form->handleRequest($request);
       if ($register_form->isSubmitted() && $register_form->isValid()) {
           $hash = $encoder->encodePassword($user, $user->getPassword());
           $user->setPassword($hash);
          $this->manager->persist($user);
          $this->manager->flush();
        return $this->redirectToRoute('security_login');
       }else{
           return $this->render('security/register.html.twig',[
               'form' => $register_form->createView(),
               'controller_name'=>'Inscription',
           ]);
       }
    }

    /**
     * @Route("/login", name="security_login")
     */
    public function login(): Response
    {
        return $this->render('security/login.html.twig');
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout()
    {
      
    }
}
