<?php
/**
 * Created by PhpStorm.
 * User: Rokas
 * Date: 29/07/16
 * Time: 15:54
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class TaskController extends Controller
{
    /**
     * @Route("/create", name="create")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addTaskAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $task = new Task();

        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:User');

        $users = $repository->findAll();

        $form = $this->createFormBuilder($task)
            ->add('title', TextType::class)
            ->add('userId', TextType::class, array('required' => false))
            ->add('save', SubmitType::class, array('label' => 'Create Task'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {

            $task = $form->getData();

            $task->setStatus(false);

            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $em->flush();

            $this->addFlash(
                'notice',
                'You just added a new task!'
            );

            return $this->redirectToRoute('list_all');
        }

        return $this->render('task/create.html.twig', array(
            'form' => $form->createView(),
            'users' => $users,
        ));
    }

    /**
     * @Route("/listall", name="list_all")
     */
    public function listAllTasksAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:Task');

        $tasks = $repository->findAll();

        return $this->render(
            'task/listAll.html.twig', array('tasks' => $tasks)
        );
    }

    /**
     * @Route("/list", name="list")
     */
    public function listTasksAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            throw $this->createAccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT f
            FROM AppBundle:Task f
            WHERE f.userId = :userId'
        )->setParameter('userId', $this->get('security.token_storage')->getToken()->getUser());

        $tasks = $query->getResult();

        return $this->render(
            'task/list.html.twig', array('tasks' => $tasks)
        );
    }

    /**
     * @Route("/edit/{id}", name="edit")
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction($id, Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $task = $this->getDoctrine()->getRepository('AppBundle:Task')->find($id);

        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:User');

        $users = $repository->findAll();

        $task->setTitle($task->getTitle());
        $task->setUserId($task->getUserId());
        $task->setStatus($task->getStatus());

        $form = $this->createFormBuilder($task)
            ->add('title', TextType::class)
            ->add('userId', TextType::class, array('required' => false))
            ->add('status', CheckboxType::class, array(
                'label'    => 'Task completed',
                'required' => false))
            ->add('save', SubmitType::class, array('label' => 'Edit Task'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $em->flush();

            $this->addFlash(
                'notice',
                'You just edited a task!'
            );

            return $this->redirectToRoute('list_all');
        }

        return $this->render(
            'task/edit.html.twig',
            array('form' => $form->createView(), 'users' => $users)
        );
    }

    /**
     * @Route("/delete/{id}", name="delete")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($id)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();
        $task = $em->getRepository('AppBundle:Task')->find($id);

        $em->remove($task);
        $em->flush();

        $this->addFlash(
            'notice',
            'You just deleted a task!'
        );

        return $this->redirectToRoute('list_all');
    }

    /**
     * @Route("/done/{id}", name="done")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @internal param Request $request
     */
    public function completeAction($id)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            throw $this->createAccessDeniedException();
        }

        $task = $this->getDoctrine()->getRepository('AppBundle:Task')->find($id);

        $user = $this->getUser();
        $userId = $user->getId();

        if($userId == $task->getUserId()) {

            $task->setStatus(true);

            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $em->flush();

            $this->addFlash(
                'notice',
                'You just completed a task!'
            );

            return $this->redirectToRoute('list');
        }
        else
            throw $this->createAccessDeniedException();
    }
}