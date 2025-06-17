<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    public function tasks(Request $request)
    {
        $response = [
            'success' => false,
            'isAllowed' => false,
            'message' => "",
        ];

        try {
            $tasks = Task::where('user_id', $request->user()->id)->get();

            $taskFormatted = [];
            foreach ($tasks as $task) {
                $taskFormatted[] = self::taskFormatter($task);
            }

            $response['success'] = true;
            $response['isAllowed'] = true;
            $response['message'] = count($tasks) . ' tasks retrieved successfully!';
            $response['totalTasks'] = count($tasks);
            $response['tasks'] = $taskFormatted;

            return response()->json($response, 200);

        } catch (Exception $e) {
            $response['message'] = 'Failed to fetch tasks. Please try again.';
            return response()->json($response, 500);
        }
    }

    public function create(Request $request)
    {
        $response = [
            "success" => false,
            "isAllowed" => true,
            "message" => "",
        ];

        try {
            $rules = [
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'dueDate' => 'required|date',
                'priority' => 'required|in:low,medium,high',
                'status' => 'required|in:todo,in_progress,completed',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $response["message"] = $validator->errors()->first();
                return response()->json($response, 422);
            }

            $task = Task::create([
                'user_id' => $request->user()->id,
                'name' => $request->name,
                'description' => $request->description,
                'due_date' => $request->dueDate,
                'priority' => $request->priority,
                'status' => $request->status
            ]);

            $response['success'] = true;
            $response['message'] = 'Task created successfully';
            $response['task'] = self::taskFormatter($task);

            return response()->json($response, 201);

        } catch (Exception $e) {
            $response['isAllowed'] = false;
            $response['message'] = 'Failed to create task. Please try again.';
            return response()->json($response, 500);
        }
    }

    public function update(Request $request, $id)
    {
        $response = [
            "success" => false,
            "isAllowed" => true,
            "message" => "",
        ];

        try {
            $rules = [
                'name' => 'sometimes|string|max:255',
                'description' => 'sometimes|string',
                'dueDate' => 'sometimes|date',
                'priority' => 'sometimes|in:low,medium,high',
                'status' => 'sometimes|in:todo,in_progress,completed',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $response["message"] = $validator->errors()->first();
                return response()->json($response, 422);
            }

            $task = Task::where('user_id', $request->user()->id)
                        ->where('id', $id)
                        ->first();

            if (!$task) {
                $response['message'] = 'Task not found';
                return response()->json($response, 404);
            }

           if ($request->has('name')) {
                $task->name = $request->name;
            }

            if ($request->has('description')) {
                $task->description = $request->description;
            }

            if ($request->has('dueDate')) {
                $task->due_date = $request->dueDate;
            }

            if ($request->has('priority')) {
                $task->priority = $request->priority;
            }

            if ($request->has('status')) {
                $task->status = $request->status;
            }

            $task->save();

            $response['success'] = true;
            $response['message'] = 'Task updated successfully';
            $response['task'] = self::taskFormatter($task);

            return response()->json($response, 200);

        } catch (Exception $e) {
            $response['isAllowed'] = false;
            $response['message'] = 'Failed to update task. Please try again.';
            return response()->json($response, 500);
        }
    }

    public function delete(Request $request, $id)
    {
        $response = [
            "success" => false,
            "isAllowed" => true,
            "message" => "",
        ];

        try {
            $task = Task::where('user_id', $request->user()->id)
                        ->where('id', $id)
                        ->first();

            if (!$task) {
                $response['message'] = 'Task not found';
                return response()->json($response, 404);
            }

            $task->delete();

            $response['success'] = true;
            $response['isAllowed'] = true;
            $response['message'] = 'Task deleted successfully';

            return response()->json($response, 200);

        } catch (Exception $e) {
            $response['isAllowed'] = false;
            $response['message'] = 'Failed to delete task. Please try again.';
            return response()->json($response, 500);
        }
    }

    protected function taskFormatter($task) {
            return  [
                "id" => $task->id,
                "name" => $task->name,
                "description" => $task->description,
                "dueDate" => formatDate($task->due_date),
                "priority" => formatText($task->priority),
                "status" => formatText($task->status),
                "created_at" => formatDate($task->created_at),
                "updatedAt" => formatDate($task->updated_at)
            ];
    }
}
