<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Localizacao;
use App\Models\Group;
use App\Models\Permission;
use App\Models\UserPermission;
use App\Models\UserFilter;
use App\Models\UserAccessSchedule;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'localizacao_id',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the localizacao that the user belongs to.
     */
    public function localizacao()
    {
        return $this->belongsTo(Localizacao::class);
    }
    
    /**
     * Verifica se o usuário é um administrador.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return (bool) $this->is_admin;
    }
    
    /**
     * Get the groups that the user belongs to.
     */
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'user_group');
    }
    
    /**
     * Relação com permissões específicas do usuário.
     */
    public function userPermissions()
    {
        return $this->hasMany(UserPermission::class);
    }
    
    /**
     * Verifica se o usuário tem uma permissão específica.
     *
     * @param string $permissionSlug
     * @return bool
     */
    public function hasPermission($permissionSlug)
    {
        // Administradores têm todas as permissões
        if ($this->isAdmin()) {
            return true;
        }
        
        // Se o usuário possui uma permissão específica cadastrada com qualquer ação permitida, considera que ele possui a permissão
        $permission = Permission::where('name', $permissionSlug)->first();
        if ($permission) {
            $up = $this->userPermissions()->where('permission_id', $permission->id)->first();
            if ($up && ($up->can_create || $up->can_read || $up->can_update || $up->can_delete)) {
                return true;
            }
        }
        
        // Verifica se o usuário tem a permissão através de seus grupos
        foreach ($this->groups as $group) {
            foreach ($group->permissions as $permission) {
                if ($permission->name === $permissionSlug) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * Verifica se o usuário tem qualquer uma das permissões especificadas.
     *
     * @param array $permissionSlugs
     * @return bool
     */
    public function hasAnyPermission(array $permissionSlugs)
    {
        foreach ($permissionSlugs as $slug) {
            if ($this->hasPermission($slug)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Verifica se o usuário tem todas as permissões especificadas.
     *
     * @param array $permissionSlugs
     * @return bool
     */
    public function hasAllPermissions(array $permissionSlugs)
    {
        foreach ($permissionSlugs as $slug) {
            if (!$this->hasPermission($slug)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Verifica se o usuário pode executar uma ação específica (create, read, update, delete)
     * para uma permissão (identificada pelo slug).
     */
    public function canAction(string $action, string $permissionSlug): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        $action = strtolower($action);
        if (!in_array($action, ['create', 'read', 'update', 'delete'], true)) {
            return false;
        }

        $permission = Permission::where('name', $permissionSlug)->first();
        if (!$permission) {
            return false;
        }

        // Se houver configuração específica do usuário, ela prevalece
        $up = $this->userPermissions()->where('permission_id', $permission->id)->first();
        if ($up) {
            $col = 'can_' . $action;
            return (bool) data_get($up, $col);
        }

        // Fallback: usa a permissão por grupo
        return $this->hasPermission($permissionSlug);
    }

    public function canCreate(string $permissionSlug): bool
    {
        return $this->canAction('create', $permissionSlug);
    }

    public function canRead(string $permissionSlug): bool
    {
        return $this->canAction('read', $permissionSlug);
    }

    public function canUpdate(string $permissionSlug): bool
    {
        return $this->canAction('update', $permissionSlug);
    }

    public function canDelete(string $permissionSlug): bool
    {
        return $this->canAction('delete', $permissionSlug);
    }
    
    /**
     * Relacionamento com os filtros do usuário
     */
    public function filters()
    {
        return $this->hasMany(UserFilter::class);
    }
    
    /**
     * Salvar filtros para um tipo de página
     *
     * @param string $pageType
     * @param array $filters
     * @return UserFilter
     */
    public function saveFilters(string $pageType, array $filters)
    {
        return UserFilter::saveFilters($this->id, $pageType, $filters);
    }
    
    /**
     * Obter filtros para um tipo de página
     *
     * @param string $pageType
     * @return array
     */
    public function getFilters(string $pageType)
    {
        return UserFilter::getFilters($this->id, $pageType);
    }
    
    /**
     * Limpar filtros para um tipo de página
     *
     * @param string $pageType
     * @return bool
     */
    public function clearFilters(string $pageType)
    {
        return UserFilter::clearFilters($this->id, $pageType);
    }
    
    /**
     * Get the access schedule associated with the user.
     */
    public function accessSchedule()
    {
        return $this->hasOne(UserAccessSchedule::class, 'user_id');
    }
    
    /**
     * Obter contagem de movimentações pendentes (não concluídas) para a localização do usuário
     *
     * @return int
     */
    public function getMovimentacoesPendentesCount()
    {
        if (!$this->localizacao_id) {
            return 0;
        }
        
        return \App\Models\Movimentacao::where('localizacao_id', $this->localizacao_id)
            ->where('concluido', false)
            ->whereNull('data_saida')
            ->count();
    }
    
    /**
     * Obter contagem de movimentações atrasadas para a localização do usuário
     *
     * @return int
     */
    public function getMovimentacoesAtrasadasCount()
    {
        if (!$this->localizacao_id || !$this->localizacao) {
            return 0;
        }
        
        // Se a localização não tem prazo definido, não há movimentações atrasadas
        if (!$this->localizacao->prazo) {
            return 0;
        }
        
        // Buscar movimentações pendentes e calcular dias úteis
        $movimentacoes = \App\Models\Movimentacao::where('localizacao_id', $this->localizacao_id)
            ->where('concluido', false)
            ->whereNull('data_saida')
            ->get();
        
        // Contar apenas as que estão atrasadas considerando dias úteis
        return $movimentacoes->filter(function ($movimentacao) {
            $diasUteis = \App\Helpers\MovimentacaoHelper::calcularDiasUteis($movimentacao->data_entrada);
            return $diasUteis > $this->localizacao->prazo;
        })->count();
    }
    
    /**
     * Obter movimentações pendentes completas para a localização do usuário
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getMovimentacoesPendentes()
    {
        if (!$this->localizacao_id) {
            return collect();
        }
        
        return \App\Models\Movimentacao::with(['produto', 'tipo', 'situacao', 'localizacao'])
            ->where('localizacao_id', $this->localizacao_id)
            ->where('concluido', false)
            ->whereNull('data_saida')
            ->orderByRaw('DATEDIFF(NOW(), data_entrada) DESC')
            ->get();
    }
}
