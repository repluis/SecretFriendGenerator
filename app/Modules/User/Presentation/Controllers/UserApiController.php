<?php

namespace App\Modules\User\Presentation\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\User\Application\UseCases\CreateUser;
use App\Modules\User\Application\UseCases\DeactivateUser;
use App\Modules\User\Application\UseCases\GetAllUsers;
use App\Modules\User\Application\UseCases\UpdateUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserApiController extends Controller
{
    public function index(GetAllUsers $useCase): JsonResponse
    {
        $users = $useCase->execute();

        return response()->json([
            'success' => true,
            'data' => $users,
        ]);
    }

    public function store(Request $request, CreateUser $useCase): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users,email',
        ]);

        $user = $useCase->execute([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
        ]);

        return response()->json([
            'success' => true,
            'data' => $user,
        ], 201);
    }

    public function update(Request $request, int $id, UpdateUser $useCase): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = $useCase->execute([
            'id' => $id,
            'name' => $request->input('name'),
        ]);

        return response()->json([
            'success' => true,
            'data' => $user,
        ]);
    }

    public function toggleActive(int $id, DeactivateUser $useCase): JsonResponse
    {
        $user = $useCase->execute(['id' => $id]);

        return response()->json([
            'success' => true,
            'data' => $user,
            'message' => $user->active ? 'Usuario activado' : 'Usuario desactivado',
        ]);
    }
}
