<?php

namespace App\Http\Controllers\Api;


use App\Library\Handler;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskApiController extends ApiController
{
    /**
     * Display the specified resource.
     *
     * @param  Task  $task
     *
     * @return JsonResponse
     */
    public function show(Task $task): JsonResponse
    {
        if ($task->getAttribute('is_active')) {
            $handler = new Handler($task->getKey(), $task->code);
            $handler->findOrGenerate();

            return $this->success(__('crud.shown'), [
                'task' => $task,
                'handler' => $handler,
            ]);
        } else {
            return $this->error(__('crud.not_found'), 404);
        }
    }

    /**
     * Проверить решение
     *
     * @param  Task  $task
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function check (Task $task, Request $request): JsonResponse
    {
        if ($task->getAttribute('is_active')) {
            $request->validate([
                'answers' => 'required|array'
            ]);

            $handler = new Handler($task->getKey(), $task->code);
            $handler->findOrGenerate();

            $isCorrect = $handler->check($request->get('answers'));

            if ($isCorrect)
                $handler->generate();

            return $this->success(__('crud.data_received'), [
                'isCorrect' => $isCorrect,
            ]);
        } else {
            return $this->error(__('crud.not_found'), 404);
        }
    }
}
