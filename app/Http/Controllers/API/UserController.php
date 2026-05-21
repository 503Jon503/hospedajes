<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        if (!auth()->user()->esAdmin()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $users = User::paginate(15);
        return UserResource::collection($users);
    }

    public function show(User $user)
    {
        if (!auth()->user()->esAdmin() && auth()->id() !== $user->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        return new UserResource($user);
    }

    public function update(Request $request, User $user)
    {
        if (!auth()->user()->esAdmin() && auth()->id() !== $user->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $data = $request->validate([
            'nombre'   => 'sometimes|string|max:100',
            'apellido' => 'sometimes|string|max:100',
            'telefono' => 'sometimes|string|max:20',
            'password' => 'sometimes|string|min:8|confirmed',
        ]);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return response()->json([
            'message' => 'Usuario actualizado exitosamente',
            'user'    => new UserResource($user),
        ]);
    }

    public function destroy(User $user)
    {
        if (!auth()->user()->esAdmin()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'Usuario eliminado exitosamente']);
    }
}