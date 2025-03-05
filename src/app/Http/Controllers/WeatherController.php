<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * @OA\Tag(name="Weather", description="API для получения данных о погоде")
 */
class WeatherController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
    
    /**
     * @OA\Get(
     *     path="/api/weather",
     *     summary="Получить погоду по городу",
     *     description="Возвращает текущую погоду в указанном городе",
     *     operationId="getWeather",
     *     tags={"Weather"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="city",
     *         in="query",
     *         description="Название города",
     *         required=true,
     *         @OA\Schema(type="string", example="Kyiv")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешный ответ",
     *         @OA\JsonContent(
     *             @OA\Property(property="temperature", type="string", example="10°C"),
     *             @OA\Property(property="humidity", type="string", example="60%"),
     *             @OA\Property(property="weather_description", type="string", example="Partly cloudy")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Город не найден",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="City not found")
     *         )
     *     )
     * )
     */
    public function getWeather(Request $request)
    {
        $request->validate([
            'city' => 'required|string'
        ]);

        $city = urlencode($request->city);

        // Запрос к API погоды
        $response = Http::get("https://wttr.in/{$city}?format=j1");

        if ($response->failed()) {
            return response()->json(['error' => 'City not found'], 404);
        }

        return response()->json($response->json());
    }
}
