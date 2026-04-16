<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Models\User; // <-- Não se esqueça de importar o Model
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        $users = $this->userService->getAllUsers($request->all());
        return response()->json($users);
    }

    // Buscar 1 usuário
    public function show($id)
    {
        $user = $this->userService->getUserById($id);
        return response()->json($user);
    }

    // Atualizar usuário
    public function update(Request $request, $id)
    {
        try {
            $data = $request->except('foto_perfil');
            $photo = $request->file('foto_perfil');

            $user = $this->userService->updateUser($id, $data, $photo);
            
            return response()->json(['message' => 'Usuário atualizado com sucesso!', 'user' => $user], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao atualizar usuário.', 'error' => $e->getMessage()], 500);
        }
    }

    // NOVA FUNÇÃO PARA EXCLUIR USUÁRIO
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete(); // Exclui o usuário do banco
            
            return response()->json(['message' => 'Usuário excluído com sucesso!'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao excluir usuário.'], 500);
        }
    }

    public function mapStats()
    {
        return response()->json($this->userService->getMapStats());
    }
}