<?php
/**
 * Created by PhpStorm.
 * User: matej
 * Date: 3/18/19
 * Time: 12:10 PM
 */

namespace App\Controller;

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
use App\Form\DiscussionFormType;
use App\Services\Fetcher;

class DiscussionController extends AbstractController
{
    /**
     * @Symfony\Component\Routing\Annotation\Route
     * (
     *     "/single_discussion/{id}",
     *     name="discussion_view",
     *     methods={"POST", "GET"}
     * )
     * @param                            EntityManagerInterface $entityManager
     * @param                            Request $request
     * @param                            Discussion $discussion
     * @param                            SubscriptionsRepository $subscriptionsRepository
     * @param                            CommentsRepository $commentsRepository
     * @param                            Fetcher $fetcher
     * @return                           \Symfony\Component\HttpFoundation\Response
     */
    public function singleDiscussionView(
        Discussion $discussion,
        Request $request,
        EntityManagerInterface $entityManager,
        SubscriptionsRepository $subscriptionsRepository,
        CommentsRepository $commentsRepository,
        Fetcher $fetcher
    )
    {
        $projectId = $discussion->getProject()->getId();

        $commentForm = $this->createForm(CommentFormType::class);
        $taskForm = $this->createForm(TaskConvertFormType::class, $data = null, array('project_id' => $projectId));

        $commentForm->handleRequest($request);
        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $this->addDiscussionComment(
                $discussion,
                $request,
                $entityManager,
                $commentForm
            );
            return $this->redirect($request->getUri());
        } else {
            $taskForm->handleRequest($request);
            if ($this->isGranted('ROLE_MANAGER') && $taskForm->isSubmitted() && $taskForm->isValid()) {
                $this->convertToTask(
                    $discussion,
                    $entityManager,
                    $taskForm,
                    $subscriptionsRepository,
                    $commentsRepository
                );
                return $this->redirectToRoute('project_tasks', array('id' => $projectId));
            }
        }

        if ($fetcher->checkSubscription() == 0) {
            return $this->redirectToRoute('index_page');
        }

        return $this->render(
            'project/single_discussion.html.twig',
            [
                'discussion' => $discussion,
                'user' => $this->getUser(),
                'commentForm' => $commentForm->createView(),
                'taskForm' => $taskForm->createView(),
                'project' => $discussion->getProject()
            ]
        );
    }

    private function addDiscussionComment(
        Discussion $discussion,
        Request $request,
        EntityManagerInterface $entityManager,
        $commentForm
    )
    {

        /**
         * @var \App\Entity\Comments $comments
         */
        $comments = $commentForm->getData();

        $files = $request->files->get('comment_form')['images'];

        if (!empty($files)) {
            foreach ($files as $file) {
                $uploadDirectory = $this->getParameter('uploads_directory');
                $filename = md5(uniqid()) . '.' . $file->guessExtension();
                $file->move(
                    $uploadDirectory,
                    $filename
                );
                $images[] = $filename;
            }

            $comments->setImages($images);
        }

        try {
            $comments->setUser($this->getUser());
            $comments->setDiscussion($discussion);
            $entityManager->persist($comments);
            $entityManager->flush();
        } catch (\Exception $exception) {
            $this->addFlash('warning', 'Please add content before submitting');
        }
    }

    private function convertToTask(
        Discussion $discussion,
        EntityManagerInterface $entityManager,
        $taskForm,
        SubscriptionsRepository $subscriptionsRepository,
        CommentsRepository $commentsRepository
    )
    {
        $subs = $subscriptionsRepository->findByDiscussion($discussion->getId());
        $comments = $commentsRepository->findCommentsByDiscussion($discussion);

        try {
            $task = new Task();
            $task = $taskForm->getData();
            $task->setProject($discussion->getProject());
            $task->setContent($discussion->getContent());
            $task->setName($discussion->getName());
            $task->setCompleted(false);


            $entityManager->persist($task);
            $entityManager->flush();

            foreach ($subs as $sub) {
                $sub->setDiscussionId(null);
                $sub->setTaskId($task->getId());
                $entityManager->persist($sub);
                $entityManager->flush();
            }

            foreach ($comments as $comment) {
                $comment->setTask($task);
                $discussion->removeComment($comment);

                $entityManager->persist($comment);
                $entityManager->persist($discussion);
                $entityManager->flush();
            }

            $entityManager->remove($discussion);
            $entityManager->flush();
        } catch (\Exception $exception) {
            $this->addFlash('warning', 'All Fields Are Required');
        }
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route
     * (
     *     "/project_discussions/{id}",
     *      name="project_discussions",
     *     methods={"POST", "GET"}
     * )
     * @param                              Project $project
     * @param                              Request $request
     * @param                              EntityManagerInterface $entityManager
     * @param                              Fetcher $fetcher
     * @return                             \Symfony\Component\HttpFoundation\Response
     */
    public function showDiscussions(
        Project $project,
        Request $request,
        EntityManagerInterface $entityManager,
        Fetcher $fetcher
    )
    {
        $discussionForm = $this->createForm(DiscussionFormType::class);

        $discussionForm->handleRequest($request);
        if ($this->isGranted('ROLE_MANAGER') && $discussionForm->isSubmitted() && $discussionForm->isValid()) {
            $this->newDiscussion($entityManager, $project, $discussionForm);
            return $this->redirect($request->getUri());
        }

        if ($fetcher->checkSubscription() == 0) {
            return $this->redirectToRoute('index_page');
        }

        return $this->render(
            'project/project_discussions.html.twig',
            [
                'project' => $project,
                'user' => $this->getUser(),
                'discussionForm' => $discussionForm->createView(),

            ]
        );
    }

    private function newDiscussion(
        EntityManagerInterface $entityManager,
        Project $project,
        $discussionForm
    )
    {
        $discussion = $discussionForm->getData();
        $discussion->setProject($project);
        $discussion->setCreatedBy($this->getUser()->getFullName());

        $entityManager->persist($discussion);
        $entityManager->flush();
    }


    /**
     * @Symfony\Component\Routing\Annotation\Route("/subscribe_to_discussion{id}", name="subscribe_to_discussion")
     * @param                                 EntityManagerInterface $entityManager
     * @param                                 Discussion $discussion
     * @return                                \Symfony\Component\HttpFoundation\Response
     */
    public function subscribeToDiscussion(
        Discussion $discussion,
        EntityManagerInterface $entityManager
    )
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
     * @Symfony\Component\Routing\Annotation\Route
     * (
     *     "/project_discussion/{id}/delete",
     *     name="discussion_delete",
     *     methods={"POST", "GET"}
     * )
     * @param                                    Discussion $discussion
     * @param                                    EntityManagerInterface $entityManager
     * @return                                   JsonResponse
     */
    public function deleteDiscussion(
        Discussion $discussion,
        EntityManagerInterface $entityManager
    )
    {

        $discussionId = $discussion->getId();

        if (!$discussion) {
            return new JsonResponse(
                [
                    'msg' => 'Unable to delete'
                ]
            );
        }

        $entityManager->remove($discussion);
        $entityManager->flush();


        return new JsonResponse(
            [
                'deletedDiscussion' => $discussionId
            ]
        );
    }
}
