<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubDepartment extends Model
{
    use HasFactory;

    protected $fillable = [
        'subdepartment_code',
        'name',
        'description'
    ];

    // Relationships
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'sub_department_id');
    }

    public function parentDepartment()
    {
        // This would need to be implemented based on your specific requirements
        // For now, returning null as the migration doesn't show a parent relationship
        return null;
    }
}

