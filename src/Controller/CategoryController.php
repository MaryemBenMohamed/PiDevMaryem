<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;



/**
 * @Route("/category")
 */
class CategoryController extends AbstractController
{

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/stat", name="stat_app")
     */
    public function stat(CategoryRepository $repo): Response
    {
        $categorys=$repo->findAll();
       
        $label=[];
        $count=[];
        foreach($categorys as $category ){
        $label[]=$category->getName();
        $count[]=count($category->getProducts());
        }
        return $this->render('category/stat.html.twig', [
            'label'=>json_encode($label),
            'count'=>json_encode($count),
        ]);
    }
    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/", name="category_index", methods={"GET"})
     */
    public function index(Request $request, CategoryRepository $categoryRepository, PaginatorInterface $paginator, TranslatorInterface $translator): Response
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        $categories2 = $paginator->paginate($categories,$request->query->getInt('page', 1),3);

        return $this->render('category/index.html.twig', [
            'categories' => $categories2,
            
        ]);
        
       
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/new", name="category_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();

            $message = $translator->trans('Added Successfully!');
            $this->addFlash('success', $message );

            return $this->redirectToRoute('category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('category/new.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/{id}", name="category_show", methods={"GET"})
     */
    public function show(Category $category, TranslatorInterface $translator): Response
    {
        return $this->render('category/show.html.twig', [
            'category' => $category,
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/{id}/edit", name="category_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Category $category, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     *  @Route("/{id}/delete", name="category_delete", methods={"GET","POST"})
     */
    public function delete(CategoryRepository $set,$id): Response
    {
        $category=$this->getDoctrine()->getRepository(category::class)->find($id);
        $em=$this->getDoctrine()->getManager();
        $em->remove($category);
        $em->flush();
        return $this-> redirectToRoute('category_index');
    }



     

    
    
    
  /* public function indexChart(): Response
    {
        $pieChart = new PieChart();
        $pieChart->getData()->setArrayToDataTable(
        [['Accessoires', 'Number'],
        ['PC Gamer',     11],
        ['Jeux et consoles',      2]
    ]
    );
        $data = [['Category','Nombre de produits']];
        foreach($category as $categories)
        {
        $data[] = array(
            $category->getName(), count($category->getProducts()),
        );
        }
        $pieChart = new PieChart();
        $pieChart->getData()->setArrayToDataTable(
        $data
        );
        return $this->render('chart/index.html.twig', [
            'chart' => $chart,
        ]);


    }
    **/


    


}
