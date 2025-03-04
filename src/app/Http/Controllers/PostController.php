<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * @OA\Tag(name="Posts", description="API для управления постами")
 */
class PostController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/posts",
     *     summary="Получить список постов",
     *     tags={"Posts"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Список постов")
     * )
     */
    public function index()
    {
        // Кэшируем список постов на 10 минут
        $posts = Cache::remember('posts', 600, function () {
            return Post::all();
        });

        return response()->json($posts);
    }

    /**
     * @OA\Post(
     *     path="/api/posts",
     *     summary="Создать новый пост",
     *     tags={"Posts"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title","content"},
     *             @OA\Property(property="title", type="string", example="Новый пост"),
     *             @OA\Property(property="content", type="string", example="Текст поста")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Пост создан")
     * )
     */
    public function store(Request $request)
    {
        $post = Post::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'content' => $request->content
        ]);
    
        Cache::forget('posts'); // Очищаем кэш списка постов
    
        return response()->json($post, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/posts/{id}",
     *     summary="Получить один пост",
     *     tags={"Posts"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Детали поста"),
     *     @OA\Response(response=404, description="Пост не найден")
     * )
     */
    public function show(Post $post)
    {
        return response()->json($post);
    }

    /**
     * @OA\Put(
     *     path="/api/posts/{id}",
     *     summary="Обновить пост",
     *     tags={"Posts"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title","content"},
     *             @OA\Property(property="title", type="string", example="Обновленный заголовок"),
     *             @OA\Property(property="content", type="string", example="Обновленный текст поста")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Пост обновлен"),
     *     @OA\Response(response=403, description="Нет прав"),
     *     @OA\Response(response=404, description="Пост не найден")
     * )
     */
    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);
    
        $post->update($request->all());
    
        Cache::forget('posts'); // Очищаем кэш списка постов
    
        return response()->json($post);
    }

    /**
     * @OA\Delete(
     *     path="/api/posts/{id}",
     *     summary="Удалить пост",
     *     tags={"Posts"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Пост удален"),
     *     @OA\Response(response=403, description="Нет прав"),
     *     @OA\Response(response=404, description="Пост не найден")
     * )
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);
    
        $post->delete();
    
        Cache::forget('posts'); // Очищаем кэш списка постов
    
        return response()->json(['message' => 'Post deleted']);
    }
}
