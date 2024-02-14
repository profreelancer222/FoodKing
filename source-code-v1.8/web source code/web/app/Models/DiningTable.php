<?php

namespace App\Models;

use App\Models\Scopes\BranchScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DiningTable extends Model
{
    use HasFactory;
    protected $table = "dining_tables";
    protected $fillable = ['name', 'slug', 'size', 'status', 'branch_id', 'qr_code'];
    protected $casts = [
        'id'        => 'integer',
        'name'      => 'string',
        'slug'      => 'string',
        'qr_code'   => 'string',
        'size'      => 'integer',
        'branch_id' => 'integer',
        'status'    => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new BranchScope());
    }


    public function branch(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function getQrAttribute(): ?string
    {
        if (!empty($this->qr_code)) {
            return asset($this->qr_code);
        }
        return null;
    }
}