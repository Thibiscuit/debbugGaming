<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Bug;
use App\Entity\ContactMessage;
use App\Form\CommentFormType;
use App\Form\ContactMessageFormType;
use App\Form\SearchFormType;
use App\Repository\BugRepository;
use App\Repository\BugFixRepository;
use App\Repository\BugSolutionRepository;
use App\Repository\CommentRepository;
use App\Repository\EditorRepository;
use App\Repository\GameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GeneralController extends AbstractController
{
    /**
     * page d'accueil
     * @Route("/", name="homePage")
     */
    public function home(BugRepository $bugRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $bugs = $bugRepository->findAllPublishedByRecent();
        $bugs = $paginator->paginate(
            $bugs,
            $request->query->getInt('page', 1),
            3
        );
        return $this->render('pages/index.html.twig', [
            'pageTitle' => 'Accueil',
            'listBugs' => $bugs
        ]);
    }

    /**
     * page de recherche de bug
     * @Route("/bugSearch", name="bugSearchPage")
     */
    public function bugSearch(BugRepository $bugRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $searchBugData = new Bug();
        $bugSearchForm = $this->createForm(SearchFormType::class, $searchBugData);
        $bugSearchForm->handleRequest($request);
        $bugs = $bugRepository->findAllPublishedByRecent();
        if($bugSearchForm->isSubmitted() && $bugSearchForm->isValid()){
            $bugs = $bugRepository->searchDatas($searchBugData);

            if ($bugs == null) {
                $this->addFlash('erreur', 'Aucun article contenant ce mot clé dans le titre n\'a été trouvé, essayez en un autre.');

            }
        }

        $bugs = $paginator->paginate(
            $bugs,
            $request->query->getInt('page', 1),
            6
        );
        return $this->render('pages/searchBug.html.twig', [
            'pageTitle' => 'Rechercher un bug',
            'listBugs' => $bugs,
            'searchBugForm' => $bugSearchForm->createView()
        ]);
    }

    /**
     * page de bug
     * @Route("/bug/{id<\d+>}", name="bugPage")
     */
    public function bugPage($id, Request $request, CommentRepository $commentsRepo, BugRepository $bugRepository,
                            BugSolutionRepository $bugSolutionRepository, EditorRepository $editorRepository, EntityManagerInterface $manager): Response
    {
        $bug = $bugRepository->find($id);
        $bugSolutions = $bugSolutionRepository->findByBugId($bug);
        $bugFix = $bug->getIdBugFix();
        $game = $bug->getIdGame();
        $editor =$editorRepository->find($game->getIdEditor());
        $allComments = $commentsRepo->findByBugId($bug);
        $comment = new Comment();
        $commentsForm = $this->createForm(CommentFormType::class, $comment);
        $commentsForm->handleRequest($request);
        if($commentsForm->isSubmitted() && $commentsForm->isValid()){
            /** @var \App\Entity\User $user */
            $user = $this->getUser();
            $comment->setDate(new \DateTime())
                ->setIdBug($bug)
                ->setIdUser($user);

            $manager->persist($comment);
            $manager->flush();
            return $this->redirectToRoute('bugPage',[
                'id'=> $bug->getId()
            ]);
        }

        return $this->render('pages/bugPage.html.twig', [
            'pageTitle' => 'Page du bug',
            'bug' => $bug,
            'game' => $game,
            'editor' => $editor,
            'bugSolutions' => $bugSolutions,
            'bugFix' => $bugFix,
            'comments' => $allComments,
            'commentsForm' => $commentsForm->createView()
        ]);
    }

    /**
     * page de contact
     * @Route("/contact", name="contactPage")
     */
    public function contactPage(Request $request): Response
    {
        $contactMessage = new ContactMessage();
        $contactForm = $this->createForm(ContactMessageFormType::class, $contactMessage);

        $contactForm->handleRequest($request);
        if(($contactForm->isSubmitted() && $contactForm->isValid())){

            $contactMessage = $contactForm->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($contactMessage);
            $entityManager->flush();

            return $this->redirectToRoute('homePage', []);
        }

        return $this->render('pages/contact.html.twig', [
            'pageTitle' => 'Contact',
            'contactForm' => $contactForm->createView()
        ]);
    }



}