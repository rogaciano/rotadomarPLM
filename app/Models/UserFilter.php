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

    protected $casts = [
        'filters' => 'array'
    ];

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

        return $userFilter ? $userFilter->filters : [];
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
        return self::saveFilters($userId, $pageType, []);
    }
}
