@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Permissões do Usuário: {{ $user->name }}</span>
                        <a href="{{ route('user-groups.index') }}" class="btn btn-sm btn-secondary">Voltar</a>
                    </div>
                </div>

                <div class="card-body">
                    <h5>Grupos do Usuário:</h5>
                    <div class="mb-4">
                        @forelse ($user->groups as $group)
                            <span class="badge bg-primary">{{ $group->display_name }}</span>
                        @empty
                            <span class="badge bg-secondary">Nenhum grupo</span>
                        @endforelse
                    </div>

                    <h5>Permissões:</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Slug</th>
                                    <th>Módulo</th>
                                    <th>Descrição</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($userPermissions as $permission)
                                    <tr>
                                        <td>{{ $permission->name }}</td>
                                        <td><code>{{ $permission->slug }}</code></td>
                                        <td>{{ $permission->module }}</td>
                                        <td>{{ $permission->description }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Nenhuma permissão encontrada.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        <div class="alert alert-info">
                            <p><strong>Nota:</strong> As permissões são herdadas dos grupos aos quais o usuário pertence.</p>
                            <p>Para modificar as permissões, edite os grupos do usuário ou as permissões dos grupos.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
