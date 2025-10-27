@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Criar Novo Grupo</span>
                        <a href="{{ route('groups.index') }}" class="btn btn-sm btn-secondary">Voltar</a>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('groups.store') }}" method="POST">
                        @csrf

                        <div class="form-group mb-3">
                            <label for="name">Nome (identificador único)</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="display_name">Nome de Exibição</label>
                            <input type="text" class="form-control @error('display_name') is-invalid @enderror" id="display_name" name="display_name" value="{{ old('display_name') }}" required>
                            @error('display_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="description">Descrição</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Ativo
                            </label>
                        </div>

                        <div class="form-group mb-4">
                            <label>Permissões</label>
                            
                            <div class="accordion" id="permissionsAccordion">
                                @foreach($modules as $module)
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading{{ Str::slug($module) }}">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ Str::slug($module) }}" aria-expanded="false" aria-controls="collapse{{ Str::slug($module) }}">
                                                {{ $module }}
                                            </button>
                                        </h2>
                                        <div id="collapse{{ Str::slug($module) }}" class="accordion-collapse collapse" aria-labelledby="heading{{ Str::slug($module) }}" data-bs-parent="#permissionsAccordion">
                                            <div class="accordion-body">
                                                <div class="row">
                                                    @foreach($permissions->where('module', $module) as $permission)
                                                        <div class="col-md-6">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="permission-{{ $permission->id }}" {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="permission-{{ $permission->id }}">
                                                                    {{ $permission->name }} <small class="text-muted">({{ $permission->slug }})</small>
                                                                    <p class="text-muted small">{{ $permission->description }}</p>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
