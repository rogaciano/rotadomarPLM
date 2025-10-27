@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Editar Grupos do Usuário: {{ $user->name }}</span>
                        <a href="{{ route('user-groups.index') }}" class="btn btn-sm btn-secondary">Voltar</a>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('user-groups.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-4">
                            <label for="groups">Grupos</label>
                            <select class="form-control groups-select" name="groups[]" id="groups" multiple>
                                @foreach ($groups as $group)
                                    <option value="{{ $group->id }}" {{ in_array($group->id, $userGroups) ? 'selected' : '' }}>
                                        {{ $group->display_name }} - {{ $group->description }}
                                    </option>
                                @endforeach
                            </select>
                            @error('groups')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
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

@section('scripts')
<script>
    $(document).ready(function() {
        $('.groups-select').select2({
            placeholder: "Selecione os grupos",
            allowClear: true,
            width: '100%'
        });
    });
</script>
@endsection
