<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    #[Route('/task-list', methods: ['GET', 'POST'], name: 'task_list')]
    public function index(Request $request, SessionInterface $session)
    {
        // Handle task addition
        if ($request->isMethod('POST') && $request->request->has('task')) {
            $task = htmlspecialchars($request->request->get('task'));
            $tasks = $session->get('tasks', []);
            $tasks[] = ['task' => $task, 'completed' => false];
            $session->set('tasks', $tasks);
        }

        // Handle task deletion
        if ($request->query->has('delete')) {
            $index = (int) $request->query->get('delete');
            $tasks = $session->get('tasks', []);
            if (isset($tasks[$index])) {
                unset($tasks[$index]);
            }
            $session->set('tasks', array_values($tasks)); // Reindex the array
        }

        // Handle task completion toggle
        if ($request->query->has('toggle')) {
            $index = (int) $request->query->get('toggle');
            $tasks = $session->get('tasks', []);
            if (isset($tasks[$index])) {
                $tasks[$index]['completed'] = !$tasks[$index]['completed'];
            }
            $session->set('tasks', $tasks);
        }

        return $this->render('task/index.html.twig', [
            'tasks' => $session->get('tasks', []),
        ]);
    }
}