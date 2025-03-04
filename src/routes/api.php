<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\PostController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\WeatherController;
use Illuminate\Support\Facades\Gate;


Route::post('/register', function (Request $request) {
    $request->validate([
        'name' => 'required|string',
        'email' => 'required|string|email|unique:users',
        'password' => 'required|string|min:6'
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);

    return response()->json([
        'token' => $user->createToken('auth-token')->plainTextToken
    ]);
});

Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|string|email',
        'password' => 'required|string'
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['Invalid credentials'],
        ]);
    }

    return response()->json([
        'token' => $user->createToken('auth-token')->plainTextToken
    ]);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->post('/logout', function (Request $request) {
    $request->user()->tokens()->delete();
    return response()->json(['message' => 'Logged out']);
});

Route::get('/admin', function () {
    if (Gate::allows('admin-only')) {
        return response()->json(['message' => 'Welcome, Admin']);
    }
    return response()->json(['error' => 'Access Denied'], 403);
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('posts', PostController::class);
});


Route::post('/send-email', [MailController::class, 'send']);
Route::get('/weather', [WeatherController::class, 'getWeather']);