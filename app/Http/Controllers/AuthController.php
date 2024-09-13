<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function index()
    {
        $users = User::all();
        return new UserCollection($users);
    }


    public function login(LoginRequest $request): JsonResponse
    {
        if (!Auth::attempt($request->validated())) {
            return response()->json([
                'errors' => 'Credenciales incorrectas.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        //$user = User::with('roles'));

        $user = $request->user()->load('roles'); // Cargar la relación 'roles'
        //$user = $request->user();
        $userToken = $user->createToken('AppToken')->plainTextToken;

        return response()->json([
            'message' => 'Se ha iniciado sesión correctamente.',
            'token' => $userToken,
            'user' => new UserResource($user)
        ], Response::HTTP_OK);
    }


    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create($request->validated());

        return response()->json([
            'message' => 'Usuario registrado exitosamente.'
        ], Response::HTTP_CREATED);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Se ha cerrado sesión correctamente.'
        ], Response::HTTP_OK);
    }

}
