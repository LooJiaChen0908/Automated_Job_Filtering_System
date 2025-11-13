<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'contact_email',
        'address',
        'city',
        'state',
        'industry',
        'country',
        'image_paths',
    ];

    protected $casts = [
        'image_paths' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function job()
    {
        return $this->hasMany(JobPosting::class);
    }

    public function getCountryNameAttribute()
    {
        $countries = [
            'MY' => 'Malaysia',
            'SG' => 'Singapore',
            'TH' => 'Thailand',
            'ID' => 'Indonesia',
            'PH' => 'Philippines',
            'VN' => 'Vietnam',
            'CN' => 'China',
        ];

        return $countries[$this->country] ?? '-';
    }

    public function getIndustryNameAttribute()
    {
        $industries = [
            'it' => 'Information Technology (IT)',
            'finance' => 'Finance & Banking',
            'education' => 'Education & Training',
            'healthcare' => 'Healthcare & Medical',
            'manufacturing' => 'Manufacturing',
            'retail' => 'Retail & Consumer Goods',
            'food_beverage' => 'Food & Beverage',
            'tourism' => 'Tourism & Hospitality',
            'construction' => 'Construction & Real Estate',
            'logistics' => 'Transportation & Logistics',
        ];

        return $industries[$this->industry] ?? '-';
    }

    // one admin for one company
    // or one for many company
}

