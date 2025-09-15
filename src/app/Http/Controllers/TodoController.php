<?php

namespace App\Http\Controllers;

use App\Http\Requests\TodoPatchRequest;
use App\Http\Requests\TodoStoreRequest;
use App\Http\Resources\TodoResource;
use App\Models\Todo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    public function index(): JsonResponse
    {
        $items = Todo::orderByDesc('created_at')->get();
        return response()->json(TodoResource::collection($items));
    }

    public function store(TodoStoreRequest $request)
    {
        $data = $request->validated();
        $todo = new Todo();
        $todo->id = (string) Str::uuid();
        $todo->title = $data['title'];
        $todo->completed = $data['completed'] ?? false;
        $todo->save();

        $location = url("/todos/{$todo->id}");
        return response()->noContent(Response::HTTP_CREATED)->header('Location', $location);
    }

    public function show(string $id)
    {
        $todo = Todo::find($id);
        if (!$todo) {
            return response()->json(['message' => 'Not Found'], Response::HTTP_NOT_FOUND);
        }
        return new TodoResource($todo);
    }

    public function update(string $id, TodoPatchRequest $request)
    {
        $todo = Todo::find($id);
        if (!$todo) {
            return response()->json(['message' => 'Not Found'], Response::HTTP_NOT_FOUND);
        }

        // Reject unknown keys when additionalProperties: false
        $allowed = ['title', 'completed'];
        $payload = $request->all();
        $unknown = array_diff(array_keys($payload), $allowed);
        if (!empty($unknown)) {
            return response()->json(['message' => 'Bad Request', 'unknown' => array_values($unknown)], 400);
        }

        $data = $request->validated();
        if (array_key_exists('title', $data)) {
            $todo->title = $data['title'];
        }
        if (array_key_exists('completed', $data)) {
            $todo->completed = (bool) $data['completed'];
        }
        $todo->save();

        return new TodoResource($todo);
    }

    public function destroy(string $id)
    {
        $todo = Todo::find($id);
        if (!$todo) {
            return response()->json(['message' => 'Not Found'], Response::HTTP_NOT_FOUND);
        }
        $todo->delete();
        return response()->noContent();
    }
}

