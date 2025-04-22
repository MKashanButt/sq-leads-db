<?php

namespace App\Models;

use App\Enum\InputStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Lead extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'patient_phone',
        'secondary_phone',
        'first_name',
        'last_name',
        'dob',
        'medicare_id',
        'address',
        'city',
        'state',
        'zip',
        'product_specs',
        'doctor_name',
        'patient_last_visit',
        'doctor_address',
        'doctor_phone',
        'doctor_fax',
        'doctor_npi',
        'recording_link',
        'comments',
        'status_id',
        'insurance_id',
        'product_id',
        'user_id',
        'notes'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'dob' => 'date',
    ];

    public function insurance(): BelongsTo
    {
        return $this->belongsTo(Insurance::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($lead) {
            if (!Auth::user()->hasRole('admin')) {
                abort(403, 'Only admins can delete leads');
            }
        });
    }
}
