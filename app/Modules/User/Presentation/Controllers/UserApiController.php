<?php

namespace App\Modules\User\Presentation\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\User\Application\UseCases\CreateUser;
use App\Modules\User\Application\UseCases\DeactivateUser;
use App\Modules\User\Application\UseCases\GetAllUsers;
use App\Modules\User\Application\UseCases\ResetPassword;
use App\Modules\User\Application\UseCases\UpdateIdentification;
use App\Modules\User\Application\UseCases\UpdateUser;
use App\Modules\User\Presentation\Requests\StoreUserRequest;
use App\Modules\User\Presentation\Requests\UpdateIdentificationRequest;
use App\Modules\User\Presentation\Requests\UpdateUserRequest;
use Illuminate\Http\JsonResponse;

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

    public function store(StoreUserRequest $request, CreateUser $useCase): JsonResponse
    {
        $user = $useCase->execute([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
        ]);

        return response()->json([
            'success' => true,
            'data' => $user,
        ], 201);
    }

    public function update(UpdateUserRequest $request, int $id, UpdateUser $useCase): JsonResponse
    {
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

    public function updateIdentification(UpdateIdentificationRequest $request, int $id, UpdateIdentification $useCase): JsonResponse
    {
        try {
            $user = $useCase->execute([
                'id' => $id,
                'identification' => $request->input('identification'),
            ]);

            return response()->json(['success' => true, 'data' => $user]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function resetPassword(int $id, ResetPassword $useCase): JsonResponse
    {
        $useCase->execute(['id' => $id]);

        return response()->json(['success' => true, 'message' => 'Contraseña restablecida correctamente.']);
    }
}
