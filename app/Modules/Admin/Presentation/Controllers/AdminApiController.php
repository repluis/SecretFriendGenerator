<?php

namespace App\Modules\Admin\Presentation\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Auth\Infrastructure\Persistence\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminApiController extends Controller
{
    /**
     * Actualiza el nombre de un usuario.
     *
     * @param Request $request - Solicitud con el nuevo nombre.
     * @param int $id - ID del usuario.
     * @return JsonResponse - Respuesta JSON con el resultado.
     */
    public function updateName(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser texto.',
            'name.max' => 'El nombre no puede superar 255 caracteres.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $user = User::findOrFail($id);
        $user->name = $request->input('name');
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Nombre actualizado correctamente.',
            'data' => $user,
        ]);
    }

    /**
     * Actualiza el email de un usuario.
     *
     * @param Request $request - Solicitud con el nuevo email.
     * @param int $id - ID del usuario.
     * @return JsonResponse - Respuesta JSON con el resultado.
     */
    public function updateEmail(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255|unique:users,email,' . $id,
        ], [
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'El email debe ser válido.',
            'email.max' => 'El email no puede superar 255 caracteres.',
            'email.unique' => 'Este email ya está en uso.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $user = User::findOrFail($id);
        $user->email = $request->input('email');
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Email actualizado correctamente.',
            'data' => $user,
        ]);
    }

    /**
     * Actualiza los roles de un usuario (puede tener múltiples roles).
     *
     * @param Request $request - Solicitud con los IDs de roles.
     * @param int $id - ID del usuario.
     * @return JsonResponse - Respuesta JSON con el resultado.
     */
    public function updateRoles(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'role_ids' => 'required|array|min:1',
            'role_ids.*' => 'exists:roles,id',
        ], [
            'role_ids.required' => 'Debe seleccionar al menos un rol.',
            'role_ids.array' => 'Los roles deben ser un array.',
            'role_ids.min' => 'Debe seleccionar al menos un rol.',
            'role_ids.*.exists' => 'Uno o más roles no existen.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $user = User::findOrFail($id);
        
        // Sincronizar con múltiples roles
        $user->roles()->sync($request->input('role_ids'));

        // Recargar roles para retornar
        $user->load('roles');

        return response()->json([
            'success' => true,
            'message' => 'Roles actualizados correctamente.',
            'data' => $user,
        ]);
    }

    /**
     * Crea un nuevo rol.
     *
     * @param Request $request - Solicitud con el nombre del rol.
     * @return JsonResponse - Respuesta JSON con el resultado.
     */
    public function createRole(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50|unique:roles,name|regex:/^[a-z_]+$/',
        ], [
            'name.required' => 'El nombre del rol es obligatorio.',
            'name.string' => 'El nombre debe ser texto.',
            'name.max' => 'El nombre no puede superar 50 caracteres.',
            'name.unique' => 'Este rol ya existe.',
            'name.regex' => 'El nombre solo puede contener letras minúsculas y guiones bajos.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $role = Role::create([
            'name' => $request->input('name'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Rol creado correctamente.',
            'data' => $role,
        ]);
    }

    /**
     * Elimina un rol.
     *
     * @param int $id - ID del rol.
     * @return JsonResponse - Respuesta JSON con el resultado.
     */
    public function deleteRole(int $id): JsonResponse
    {
        $role = Role::findOrFail($id);

        // Verificar que no sea un rol del sistema
        if (in_array($role->name, ['admin', 'finance', 'user'])) {
            return response()->json([
                'success' => false,
                'message' => 'No se pueden eliminar los roles del sistema.',
            ], 403);
        }

        // Contar usuarios con este rol
        $usersCount = $role->users()->count();

        $role->delete();

        return response()->json([
            'success' => true,
            'message' => "Rol eliminado correctamente. {$usersCount} usuario(s) afectado(s).",
        ]);
    }

    /**
     * Restablece la contraseña de un usuario a su nombre de usuario.
     *
     * @param int $id - ID del usuario.
     * @return JsonResponse - Respuesta JSON con el resultado.
     */
    public function resetPassword(int $id): JsonResponse
    {
        $user = User::findOrFail($id);
        
        // Restablecer contraseña al nombre del usuario
        $user->password = Hash::make($user->name);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Contraseña restablecida correctamente. Nueva contraseña: ' . $user->name,
        ]);
    }

    /**
     * Actualiza los permisos de un rol.
     *
     * @param Request $request - Solicitud con los permisos.
     * @param int $id - ID del rol.
     * @return JsonResponse - Respuesta JSON con el resultado.
     */
    public function updateRolePermissions(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'permissions' => 'required|array',
            'permissions.*' => 'string|in:dashboard,juego,pagos,recaudaciones,usuarios,admin',
        ], [
            'permissions.required' => 'Debe seleccionar al menos un permiso.',
            'permissions.array' => 'Los permisos deben ser un array.',
            'permissions.*.in' => 'Uno o más permisos no son válidos.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $role = Role::findOrFail($id);
        
        // Si es admin, siempre tiene todos los permisos
        if ($role->name === 'admin') {
            $role->permissions = ['*'];
        } else {
            $role->permissions = $request->input('permissions');
        }
        
        $role->save();

        return response()->json([
            'success' => true,
            'message' => 'Permisos actualizados correctamente.',
            'data' => $role,
        ]);
    }
}
