<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryAlert extends Model
{
    protected $fillable = [
        'product_id',
        'alert_type',
        'is_resolved',
        'resolved_at',
        'resolved_by',
        'notes'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
    public function scopeUnresolved($query)
    {
        return $query->where('is_resolved', false);
    }
    public function scopeResolved($query)
    {
        return $query->where('is_resolved', true);
    }

    public function resolvedByUser()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function resolve($userId, $notes = null)
    {
        $this->is_resolved = true;
        $this->resolved_at = now();
        $this->resolved_by = $userId;

        if ($notes) {
            $this->notes = $this->notes ? $this->notes . "\n\nResolution: " . $notes : "Resolution: " . $notes;
        }

        $this->save();

        return $this;
    }

    public function markAsUnresolved()
    {
        $this->is_resolved = false;
        $this->resolved_at = null;
        $this->resolved_by = null;
        $this->save();

        return $this;
    }
    


}
