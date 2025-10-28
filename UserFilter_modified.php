<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFilter extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'page_type',
        'filters'
    ];

    // Forçar a serialização explícita
    public function setFiltersAttribute($value)
    {
        $this->attributes['filters'] = is_array($value) ? json_encode($value) : $value;
    }

    // Forçar a deserialização explícita
    public function getFiltersAttribute($value)
    {
        if (is_string($value)) {
            return json_decode($value, true) ?? [];
        }
        return $value ?? [];
    }

    /**
     * Relacionamento com o usuário
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Salvar filtros para um usuário e tipo de página
     *
     * @param int $userId
     * @param string $pageType
     * @param array $filters
     * @return UserFilter
     */
    public static function saveFilters($userId, $pageType, $filters)
    {
        // Garantir que os filtros sejam um array
        $filters = is_array($filters) ? $filters : [];
        
        // Log para debug
        \Log::info("Salvando filtros para usuário {$userId}, página {$pageType}: " . json_encode($filters));
        
        return self::updateOrCreate(
            ['user_id' => $userId, 'page_type' => $pageType],
            ['filters' => $filters]
        );
    }

    /**
     * Obter filtros para um usuário e tipo de página
     *
     * @param int $userId
     * @param string $pageType
     * @return array
     */
    public static function getFilters($userId, $pageType)
    {
        $userFilter = self::where('user_id', $userId)
            ->where('page_type', $pageType)
            ->first();

        $filters = $userFilter ? $userFilter->filters : [];
        
        // Log para debug
        \Log::info("Obtendo filtros para usuário {$userId}, página {$pageType}: " . json_encode($filters));
        
        return $filters;
    }

    /**
     * Limpar filtros para um usuário e tipo de página
     *
     * @param int $userId
     * @param string $pageType
     * @return bool
     */
    public static function clearFilters($userId, $pageType)
    {
        // Log para debug
        \Log::info("Limpando filtros para usuário {$userId}, página {$pageType}");
        
        return self::saveFilters($userId, $pageType, []);
    }
}
