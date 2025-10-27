<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use Carbon\Carbon;

class ActivityLogController extends Controller
{
    /**
     * Exibe a lista de logs de atividade
     */
    public function index(Request $request)
    {
        $query = Activity::with(['causer', 'subject']);
        
        // Filtros
        if ($request->has('log_name') && $request->log_name) {
            $query->where('log_name', $request->log_name);
        }
        
        if ($request->has('event') && $request->event) {
            $query->where('event', $request->event);
        }
        
        if ($request->has('subject_type') && $request->subject_type) {
            $query->where('subject_type', $request->subject_type);
        }
        
        if ($request->has('causer_type') && $request->causer_type) {
            $query->where('causer_type', $request->causer_type);
        }
        
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', Carbon::parse($request->date_from));
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', Carbon::parse($request->date_to));
        }
        
        // Ordenação
        $query->orderBy('created_at', 'desc');
        
        // Paginação
        $activities = $query->paginate(15);
        
        // Obter valores únicos para os filtros
        $logNames = Activity::distinct()->pluck('log_name')->filter();
        $events = Activity::distinct()->pluck('event')->filter();
        $subjectTypes = Activity::distinct()->pluck('subject_type')->filter()->map(function($type) {
            return [
                'value' => $type,
                'label' => class_basename($type)
            ];
        });
        $causerTypes = Activity::distinct()->pluck('causer_type')->filter()->map(function($type) {
            return [
                'value' => $type,
                'label' => class_basename($type)
            ];
        });
        
        return view('activity-log.index', compact(
            'activities', 
            'logNames', 
            'events', 
            'subjectTypes', 
            'causerTypes'
        ));
    }
    
    /**
     * Exibe os detalhes de um log de atividade específico
     */
    public function show($id)
    {
        $activity = Activity::with(['causer', 'subject'])->findOrFail($id);
        
        return view('activity-log.show', compact('activity'));
    }
}
