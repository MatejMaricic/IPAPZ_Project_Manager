<?php
/**
 * Created by PhpStorm.
 * User: matej
 * Date: 3/18/19
 * Time: 12:10 PM
 */

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\Discussion;
use App\Entity\Project;
use App\Entity\Subscriptions;
use App\Entity\Task;
use App\Form\CommentFormType;
use App\Form\TaskConvertFormType;
use App\Repository\CommentsRepository;
use App\Repository\SubscriptionsRepository;
use Doctrine\DBAL\Exception\ConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\DiscussionFormType;

class DiscussionController extends AbstractController
{
    /**
     * @Route("/single_discussion/{id}", name="discussion_view", methods={"POST", "GET"})
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @param Discussion $discussion
     * @param SubscriptionsRepository $subscriptionsRepository
     * @param CommentsRepository $commentsRepository
     * @return Response
     */
    public function singleDiscussionView(Discussion $discussion, Request $request, EntityManagerInterface $entityManager, SubscriptionsRepository $subscriptionsRepository, CommentsRepository $commentsRepository)
    {
        $projectId =$discussion->getProject()->getId();

        $commentForm = $this->createForm(CommentFormType::class);
        $taskForm = $this->createForm(TaskConvertFormType::class,$data = null, array('project_id' => $projectId));

        $commentForm->handleRequest($request);
        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $this->addDiscussionComment($discussion, $request, $entityManager, $commentForm);
            return $this->redirect($request->getUri());
        } else {
            $taskForm->handleRequest($request);
            if ($this->isGranted('ROLE_MANAGER') && $taskForm->isSubmitted() && $taskForm->isValid()) {
                $this->convertToTask($discussion, $request, $entityManager, $taskForm, $subscriptionsRepository, $commentsRepository);
                return $this->redirectToRoute('project_tasks', array('id' => $projectId));
            }
        }

        return $this->render('project/single_discussion.html.twig', [
            'discussion' => $discussion,
            'user' => $this->getUser(),
            'commentForm' => $commentForm->createView(),
            'taskForm' => $taskForm->createView(),
            'project' => $discussion->getProject()
        ]);
    }

    private function addDiscussionComment(Discussion $discussion, Request $request, EntityManagerInterface $entityManager, $commentForm)
    {

        /**@var Comments $comments */
        $comments = $commentForm->getData();

        $files = $request->files->get('comment_form')['images'];

        if (!empty($files)) {

            foreach ($files as $file) {
                $uploads_directory = $this->getParameter('uploads_directory');
                $filename = md5(uniqid()) . '.' . $file->guessExtension();
                $file->move(
                    $uploads_directory,
                    $filename

                );
                $images[] = $filename;
            }
            $comments->setImages($images);

        }
        $comments->setUser($this->getUser());
        $comments->setDiscussion($discussion);
        $entityManager->persist($comments);
        $entityManager->flush();
    }

    private function convertToTask(Discussion $discussion, Request $request, EntityManagerInterface $entityManager, $taskForm, SubscriptionsRepository $subscriptionsRepository, CommentsRepository $commentsRepository)
    {
        $subs = $subscriptionsRepository->findByDiscussion($discussion->getId());
        $comments = $commentsRepository->findCommentsByDiscussion($discussion);

        $task = new Task();
        $task = $taskForm->getData();
        $task->setProject($discussion->getProject());
        $task->setContent($discussion->getContent());
        $task->setName($discussion->getName());
        $task->setCompleted(false);



        $entityManager->persist($task);
        $entityManager->flush();

        foreach ($subs as $sub)
        {
            $sub->setDiscussionId(null);
            $sub->setTaskId($task->getId());
            $entityManager->persist($sub);
            $entityManager->flush();
        }

        foreach ($comments as $comment)
        {
            $comment->setTask($task);
            $discussion->removeComment($comment);

            $entityManager->persist($comment);
            $entityManager->persist($discussion);
            $entityManager->flush();
        }
        $entityManager->remove($discussion);
        $entityManager->flush();
    }

    /**
     * @Route("/project_discussions/{id}", name="project_discussions", methods={"POST", "GET"})
     * @param Project $project
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function showDiscussions(Project $project, Request $request, EntityManagerInterface $entityManager)
    {
        $discussionForm = $this->createForm(DiscussionFormType::class);

        $discussionForm->handleRequest($request);
        if ($this->isGranted('ROLE_MANAGER') && $discussionForm->isSubmitted() && $discussionForm->isValid()) {
            $this->newDiscussion( $entityManager, $project, $discussionForm);
            return $this->redirect($request->getUri());

        }

        return $this->render('project/project_discussions.html.twig', [
            'project' => $project,
            'user' => $this->getUser(),
            'discussionForm' => $discussionForm->createView(),

        ]);
    }

    private function newDiscussion(EntityManagerInterface $entityManager, Project $project, $discussionForm)
    {
        $discussion = $discussionForm->getData();
        $discussion->setProject($project);
        $discussion->setCreatedBy($this->getUser()->getFullName());

        $entityManager->persist($discussion);
        $entityManager->flush();

    }


    /**
     * @Route("/subscribe_to_discussion{id}", name="subscribe_to_discussion")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param SubscriptionsRepository $subscriptionsRepository
     * @param Discussion $discussion
     * @return Response
     */
    public function subscribeToDiscussion(Discussion $discussion, EntityManagerInterface $entityManager, Request $request, SubscriptionsRepository $subscriptionsRepository)
    {
        $subscription = new Subscriptions();
        $email = $this->getUser()->getEmail();
        $projectId = $discussion->getProject()->getId();

        try {
            $subscription->setUserEmail($email);
            $subscription->setDiscussionId($discussion->getId());
            $entityManager->persist($subscription);
            $entityManager->flush();
        } catch (ConstraintViolationException $constraintViolationException) {

        }


        return $this->redirectToRoute('project_view', array('id' => $projectId));
    }

    /**
     * @Route("/project_discussion/{id}/delete", name="discussion_delete", methods={"POST", "GET"})
     * @param Discussion $discussion
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    public function deleteDiscussion(Discussion $discussion, EntityManagerInterface $entityManager)
    {

        $discussionId = $discussion->getId();

        if (!$discussion) {
            return new JsonResponse([
                'msg' => 'Unable to delete'
            ]);
        }

        $entityManager->remove($discussion);
        $entityManager->flush();


        return new JsonResponse([
            'deletedDiscussion' => $discussionId
        ]);
    }


}