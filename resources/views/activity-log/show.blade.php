<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detalhes do Log de Atividade') }}
        </h2>
    </x-slot>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>{{ __('Detalhes do Log de Atividade') }}</span>
                        <a href="{{ route('activity-log.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Informações Gerais</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 30%">ID</th>
                                    <td>{{ $activity->id }}</td>
                                </tr>
                                <tr>
                                    <th>Descrição</th>
                                    <td>{{ $activity->description }}</td>
                                </tr>
                                <tr>
                                    <th>Nome do Log</th>
                                    <td>{{ $activity->log_name }}</td>
                                </tr>
                                <tr>
                                    <th>Evento</th>
                                    <td>
                                        <span class="badge {{ $activity->event == 'created' ? 'bg-success' : ($activity->event == 'updated' ? 'bg-primary' : ($activity->event == 'deleted' ? 'bg-danger' : 'bg-secondary')) }}">
                                            {{ $activity->event }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Data</th>
                                    <td>{{ $activity->created_at->format('d/m/Y H:i:s') }}</td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h5>Objeto Afetado</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 30%">Tipo</th>
                                    <td>{{ $activity->subject_type ? class_basename($activity->subject_type) : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>ID</th>
                                    <td>{{ $activity->subject_id ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Usuário</th>
                                    <td>
                                        @if($activity->causer)
                                            {{ $activity->causer->name ?? class_basename($activity->causer_type) . ' #' . $activity->causer_id }}
                                        @else
                                            Sistema
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Tipo de Usuário</th>
                                    <td>{{ $activity->causer_type ? class_basename($activity->causer_type) : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>ID do Usuário</th>
                                    <td>{{ $activity->causer_id ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Propriedades</h5>
                            
                            @if($activity->properties && count($activity->properties) > 0)
                                <div class="card">
                                    <div class="card-header">
                                        <ul class="nav nav-tabs card-header-tabs" id="propertiesTabs" role="tablist">
                                            @if($activity->properties->has('attributes'))
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link active" id="attributes-tab" data-bs-toggle="tab" data-bs-target="#attributes" type="button" role="tab" aria-controls="attributes" aria-selected="true">
                                                        Atributos
                                                    </button>
                                                </li>
                                            @endif
                                            
                                            @if($activity->properties->has('old'))
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link {{ !$activity->properties->has('attributes') ? 'active' : '' }}" id="old-tab" data-bs-toggle="tab" data-bs-target="#old" type="button" role="tab" aria-controls="old" aria-selected="{{ !$activity->properties->has('attributes') }}">
                                                        Valores Anteriores
                                                    </button>
                                                </li>
                                            @endif
                                            
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link {{ !$activity->properties->has('attributes') && !$activity->properties->has('old') ? 'active' : '' }}" id="raw-tab" data-bs-toggle="tab" data-bs-target="#raw" type="button" role="tab" aria-controls="raw" aria-selected="{{ !$activity->properties->has('attributes') && !$activity->properties->has('old') }}">
                                                    JSON Completo
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                    
                                    <div class="card-body">
                                        <div class="tab-content" id="propertiesTabsContent">
                                            @if($activity->properties->has('attributes'))
                                                <div class="tab-pane fade show active" id="attributes" role="tabpanel" aria-labelledby="attributes-tab">
                                                    <table class="table table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>Campo</th>
                                                                <th>Valor</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($activity->properties['attributes'] as $key => $value)
                                                                <tr>
                                                                    <td><strong>{{ $key }}</strong></td>
                                                                    <td>
                                                                        @if(is_array($value) || is_object($value))
                                                                            <pre>{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                                                        @elseif(is_bool($value))
                                                                            {{ $value ? 'Sim' : 'Não' }}
                                                                        @elseif(is_null($value))
                                                                            <em>Nulo</em>
                                                                        @else
                                                                            {{ $value }}
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @endif
                                            
                                            @if($activity->properties->has('old'))
                                                <div class="tab-pane fade {{ !$activity->properties->has('attributes') ? 'show active' : '' }}" id="old" role="tabpanel" aria-labelledby="old-tab">
                                                    <table class="table table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>Campo</th>
                                                                <th>Valor Anterior</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($activity->properties['old'] as $key => $value)
                                                                <tr>
                                                                    <td><strong>{{ $key }}</strong></td>
                                                                    <td>
                                                                        @if(is_array($value) || is_object($value))
                                                                            <pre>{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                                                        @elseif(is_bool($value))
                                                                            {{ $value ? 'Sim' : 'Não' }}
                                                                        @elseif(is_null($value))
                                                                            <em>Nulo</em>
                                                                        @else
                                                                            {{ $value }}
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @endif
                                            
                                            <div class="tab-pane fade {{ !$activity->properties->has('attributes') && !$activity->properties->has('old') ? 'show active' : '' }}" id="raw" role="tabpanel" aria-labelledby="raw-tab">
                                                <pre>{{ json_encode($activity->properties, JSON_PRETTY_PRINT) }}</pre>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    Nenhuma propriedade registrada para esta atividade.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
