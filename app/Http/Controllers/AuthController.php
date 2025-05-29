<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\Enterprise;
use App\Models\RoleUser;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{

    public function index()
    {
        $users = User::with(['roles'])->get();
        return new UserCollection($users);
    }


    public function login(LoginRequest $request): JsonResponse
    {
        if (!Auth::attempt($request->only(['email', 'password']))) {
            return response()->json([
                'errors' => 'Credenciales incorrectas.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = $request->user();

        // Verificar si el usuario tiene el status 1
        if ($user->status !== 1) {
            return response()->json([
                'errors' => 'Tu cuenta está desactivada. Contacta al administrador.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Verificar que el usuario tenga acceso a la empresa solicitada
        $enterpriseId = $request->enterprise_id;
        if (!$user->roles()->wherePivot('enterprise_id', $enterpriseId)->exists()) {
            return response()->json([
                'errors' => 'No tienes permisos para acceder a esta sucursal.'
            ], Response::HTTP_FORBIDDEN);
        }

        $user = $user->load(['roles' => function ($query) use ($enterpriseId) {
            $query->wherePivot('enterprise_id', $enterpriseId);
        }]);

        // $userToken = $user->createToken('AppToken')->plainTextToken;
        $userToken = $user->createToken('AppToken', ['enterprise_id:' . $enterpriseId])->plainTextToken;

        //Log::info($userToken);

        $enterprise = Enterprise::find($enterpriseId);

        return response()->json([
            'message' => 'Se ha iniciado sesión correctamente.',
            'token' => $userToken,
            'user' => new UserResource($user),
            'enterprise' => [
                'id' => $enterprise->id,
                'name' => $enterprise->name
            ]
        ], Response::HTTP_OK);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = User::create($request->all());

            $roleUser = RoleUser::create([
                'user_id' => $user->id,
                'role_id' => $request->role_id,
                'enterprise_id' => $request->enterprise_id
            ]);

            DB::commit();

            return response()->json([
                'status' => 201,
                'message' => 'Usuario creado correctamente',
                'usuario' => $roleUser
            ]);
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
                'error' => 'Error al crear al usuario'

            ], 500);
        }

        //  return new UserResource();
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        return new UserResource($user);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, $id)
    {
        $user = User::findOrFail($id);
        // Log::info($user);
        $user->update($request->all());
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
