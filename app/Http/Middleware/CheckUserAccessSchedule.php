<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;

class CheckUserAccessSchedule
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Log para verificar se o middleware está sendo executado
        Log::debug('CheckUserAccessSchedule: Middleware executado', [
            'url' => $request->url(),
            'method' => $request->method(),
            'user_id' => Auth::id(),
            'session_id' => $request->session()->getId()
        ]);

        $user = Auth::user();

        // Log para todos os usuários
        if ($user) {
            Log::debug('CheckUserAccessSchedule: Verificando usuário', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'is_admin' => $user->isAdmin(),
                'day' => strtolower(Carbon::now()->format('l')),
                'time' => Carbon::now()->format('H:i:s')
            ]);
        }

        // Bypass for admins
        if ($user && $user->isAdmin()) {
            return $next($request);
        }

        // Buscar configuração de horário diretamente do banco de dados
        if ($user) {
            $accessSchedule = DB::table('user_access_schedules')
                ->where('user_id', $user->id)
                ->first();

            // Se não tem configuração ou não está ativa, permite o acesso
            if (!$accessSchedule || !$accessSchedule->is_active) {
                return $next($request);
            }

            $now = Carbon::now();
            $currentTime = $now->format('H:i:s');
            $currentDay = strtolower($now->format('l')); // Usando format('l') para obter o nome do dia

            // Verificar se o dia existe como propriedade no objeto
            $dayAllowed = false;
            if (property_exists($accessSchedule, $currentDay)) {
                $dayAllowed = (bool) $accessSchedule->$currentDay;
            } else {
                // Fallback para dias em português ou outros formatos
                $dayMap = [
                    'monday' => 'monday',
                    'tuesday' => 'tuesday',
                    'wednesday' => 'wednesday',
                    'thursday' => 'thursday',
                    'friday' => 'friday',
                    'saturday' => 'saturday',
                    'sunday' => 'sunday',
                ];

                if (isset($dayMap[$currentDay]) && property_exists($accessSchedule, $dayMap[$currentDay])) {
                    $mappedDay = $dayMap[$currentDay];
                    $dayAllowed = (bool) $accessSchedule->$mappedDay;
                }
            }

            // Registrar informações para debug
            Log::debug('CheckUserAccessSchedule: Verificando acesso', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'current_day' => $currentDay,
                'current_time' => $currentTime,
                'schedule_active' => $accessSchedule->is_active,
                'day_allowed' => $dayAllowed,
                'day_property_exists' => property_exists($accessSchedule, $currentDay),
                'start_time' => $accessSchedule->start_time,
                'end_time' => $accessSchedule->end_time,
                'all_schedule_data' => json_encode((array) $accessSchedule)
            ]);

            // Verificar se o dia atual é permitido
            if (!$dayAllowed) {
                // Criar mensagem com os dias permitidos
                $diasPermitidos = [];
                if ($accessSchedule->monday) $diasPermitidos[] = 'Segunda-feira';
                if ($accessSchedule->tuesday) $diasPermitidos[] = 'Terça-feira';
                if ($accessSchedule->wednesday) $diasPermitidos[] = 'Quarta-feira';
                if ($accessSchedule->thursday) $diasPermitidos[] = 'Quinta-feira';
                if ($accessSchedule->friday) $diasPermitidos[] = 'Sexta-feira';
                if ($accessSchedule->saturday) $diasPermitidos[] = 'Sábado';
                if ($accessSchedule->sunday) $diasPermitidos[] = 'Domingo';

                // Obter o nome do dia atual em português
                $diasPtBr = [
                    'monday' => 'Segunda-feira',
                    'tuesday' => 'Terça-feira',
                    'wednesday' => 'Quarta-feira',
                    'thursday' => 'Quinta-feira',
                    'friday' => 'Sexta-feira',
                    'saturday' => 'Sábado',
                    'sunday' => 'Domingo'
                ];
                $diaAtual = $diasPtBr[$currentDay] ?? ucfirst($currentDay);

                $mensagemDias = 'Dias permitidos: ' . implode(', ', $diasPermitidos);
                $mensagemHorario = 'Horário permitido: ' . Carbon::parse($accessSchedule->start_time)->format('H:i') . ' às ' .
                    Carbon::parse($accessSchedule->end_time)->format('H:i');
                $mensagemAtual = 'Hoje é ' . $diaAtual . ', ' . Carbon::now()->format('H:i') . '.';

                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->with('error', 'Acesso não permitido neste dia da semana. ' . $mensagemAtual . ' ' . $mensagemDias . '. ' . $mensagemHorario . '.');
            }

            // Verificar se o horário atual está dentro do intervalo permitido
            $startTime = $accessSchedule->start_time;
            $endTime = $accessSchedule->end_time;

            // Converter para objetos Carbon para comparação correta
            $currentTimeObj = Carbon::createFromFormat('H:i:s', $currentTime);
            $startTimeObj = Carbon::createFromFormat('H:i:s', $startTime);
            $endTimeObj = Carbon::createFromFormat('H:i:s', $endTime);

            Log::debug('CheckUserAccessSchedule: Verificando horário', [
                'user_id' => $user->id,
                'current_time' => $currentTime,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'is_before_start' => $currentTimeObj->lt($startTimeObj),
                'is_after_end' => $currentTimeObj->gt($endTimeObj)
            ]);

            if ($currentTimeObj->lt($startTimeObj) || $currentTimeObj->gt($endTimeObj)) {
                // Criar mensagem com os dias permitidos
                $diasPermitidos = [];
                if ($accessSchedule->monday) $diasPermitidos[] = 'Segunda-feira';
                if ($accessSchedule->tuesday) $diasPermitidos[] = 'Terça-feira';
                if ($accessSchedule->wednesday) $diasPermitidos[] = 'Quarta-feira';
                if ($accessSchedule->thursday) $diasPermitidos[] = 'Quinta-feira';
                if ($accessSchedule->friday) $diasPermitidos[] = 'Sexta-feira';
                if ($accessSchedule->saturday) $diasPermitidos[] = 'Sábado';
                if ($accessSchedule->sunday) $diasPermitidos[] = 'Domingo';

                // Obter o nome do dia atual em português
                $diasPtBr = [
                    'monday' => 'Segunda-feira',
                    'tuesday' => 'Terça-feira',
                    'wednesday' => 'Quarta-feira',
                    'thursday' => 'Quinta-feira',
                    'friday' => 'Sexta-feira',
                    'saturday' => 'Sábado',
                    'sunday' => 'Domingo'
                ];
                $diaAtual = $diasPtBr[$currentDay] ?? ucfirst($currentDay);

                $mensagemDias = 'Dias permitidos: ' . implode(', ', $diasPermitidos);
                $mensagemHorario = 'Horário permitido: ' . Carbon::parse($startTime)->format('H:i') . ' às ' .
                    Carbon::parse($endTime)->format('H:i');
                $mensagemAtual = 'Hoje é ' . $diaAtual . ' às ' . Carbon::now()->format('H:i') . '. ';

                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->with('error', 'Acesso não permitido neste horário. ' . $mensagemAtual . ' ' . $mensagemDias . '. ' . $mensagemHorario . '.');
            }
        }

        // Se chegou aqui, o acesso está permitido
        if ($user && $accessSchedule && $accessSchedule->is_active) {
            Log::debug('CheckUserAccessSchedule: Acesso permitido', [
                'user_id' => $user->id,
                'user_name' => $user->name
            ]);
        }

        return $next($request);
    }
}
