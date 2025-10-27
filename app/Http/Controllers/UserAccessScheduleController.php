<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserAccessSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserAccessScheduleController extends Controller
{
    /**
     * Display the access schedule form for a user.
     */
    public function edit($userId)
    {
        // Check if user has permission
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Acesso não autorizado.');
        }

        $user = User::findOrFail($userId);
        $accessSchedule = $user->accessSchedule ?? new UserAccessSchedule(['user_id' => $user->id]);

        return view('user-access-schedules.edit', compact('user', 'accessSchedule'));
    }

    /**
     * Update the access schedule for a user.
     */
    public function update(Request $request, $userId)
    {
        // Check if user has permission
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Acesso não autorizado.');
        }

        $user = User::findOrFail($userId);
        
        // Simplificar o processo de validação
        $start_time = $request->input('start_time') . ':00';
        $end_time = $request->input('end_time') . ':00';
        
        // Preparar os dados para inserção/atualização
        $data = [
            'user_id' => $userId,
            'monday' => $request->has('monday') ? 1 : 0,
            'tuesday' => $request->has('tuesday') ? 1 : 0,
            'wednesday' => $request->has('wednesday') ? 1 : 0,
            'thursday' => $request->has('thursday') ? 1 : 0,
            'friday' => $request->has('friday') ? 1 : 0,
            'saturday' => $request->has('saturday') ? 1 : 0,
            'sunday' => $request->has('sunday') ? 1 : 0,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'is_active' => $request->has('is_active') ? 1 : 0,
            'updated_at' => now()
        ];
        
        try {
            // Verificar se já existe um registro para este usuário
            $existingSchedule = DB::table('user_access_schedules')
                ->where('user_id', $userId)
                ->first();
                
            if ($existingSchedule) {
                // Atualizar registro existente
                DB::table('user_access_schedules')
                    ->where('user_id', $userId)
                    ->update($data);
            } else {
                // Inserir novo registro
                $data['created_at'] = now();
                DB::table('user_access_schedules')->insert($data);
            }
            
            // Verificar se o registro foi salvo
            $check = DB::table('user_access_schedules')
                ->where('user_id', $userId)
                ->first();
                
            if (!$check) {
                throw new \Exception('Falha ao salvar os dados.');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao salvar horário de acesso: ' . $e->getMessage());
        }

        return redirect()->route('users.edit', $user->id)
            ->with('success', 'Horário de acesso atualizado com sucesso.');
    }
}
