<?php

namespace App\Http\Controllers;

use App\Http\Requests\TodoPatchRequest;
use App\Http\Requests\TodoStoreRequest;
use App\Http\Resources\TodoResource;
use App\Models\Todo;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class TodoController extends Controller
{
    public function index()
    {
        // 一覧はDBの正規化に依存する。データクリーンアップ後は全件返す。
        $items = Todo::orderByDesc('created_at')->get();
        return TodoResource::collection($items);
    }

    public function store(TodoStoreRequest $request)
    {
        $data = $request->validated();
        $todo = new Todo();
        $todo->id = (string) Str::uuid();
        $todo->title = $data['title'];
        $todo->completed = $data['completed'] ?? false;
        $todo->save();

        // API ルート名から相対URLを生成し、/api プレフィックスを含む Location を返す
        $location = route('todos.show', ['id' => $todo->id], false); // e.g. "/api/todos/{id}"
        return response()->noContent(Response::HTTP_CREATED)->header('Location', $location);
    }

    public function show(string $id)
    {
        // UUID でない場合も 404 として JSON を返す
        if (!\Illuminate\Support\Str::isUuid($id)) {
            return response()->json(['message' => 'Not Found'], Response::HTTP_NOT_FOUND);
        }
        $todo = Todo::find($id);
        if (!$todo) {
            return response()->json(['message' => 'Not Found'], Response::HTTP_NOT_FOUND);
        }
        return new TodoResource($todo);
    }

    public function update(string $id, TodoPatchRequest $request)
    {
        if (!\Illuminate\Support\Str::isUuid($id)) {
            return response()->json(['message' => 'Not Found'], Response::HTTP_NOT_FOUND);
        }
        $todo = Todo::find($id);
        if (!$todo) {
            return response()->json(['message' => 'Not Found'], Response::HTTP_NOT_FOUND);
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
        if (!\Illuminate\Support\Str::isUuid($id)) {
            return response()->json(['message' => 'Not Found'], Response::HTTP_NOT_FOUND);
        }
        $todo = Todo::find($id);
        if (!$todo) {
            return response()->json(['message' => 'Not Found'], Response::HTTP_NOT_FOUND);
        }
        $todo->delete();
        return response()->noContent();
    }
}
