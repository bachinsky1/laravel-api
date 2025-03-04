<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\SendEmailJob;

/**
 * @OA\Tag(name="Emails", description="API для отправки писем")
 */
class MailController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/send-email",
     *     summary="Отправить email",
     *     tags={"Emails"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","subject","message"},
     *             @OA\Property(property="email", type="string", example="user@example.com"),
     *             @OA\Property(property="subject", type="string", example="Тема письма"),
     *             @OA\Property(property="message", type="string", example="Текст письма")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Письмо отправлено"),
     *     @OA\Response(response=401, description="Неавторизованный доступ")
     * )
     */
    public function send(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'subject' => 'required|string',
            'message' => 'required|string'
        ]);

        $details = [
            'email' => $request->email,
            'subject' => $request->subject,
            'message' => $request->message
        ];

        dispatch(new SendEmailJob($details));

        return response()->json(['message' => 'Email is being sent']);
    }
}
