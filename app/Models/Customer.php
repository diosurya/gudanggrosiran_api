<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['nama', 'foto', 'email', 'no_hp', 'gender', 'tanggal_lahir'];

    public function alamat(): HasMany
    {
        return $this->hasMany(Alamat::class, 'id_customer', 'id');
    }

    public function scopeFiltered(Builder $builder)
    {
        $search = request('search') ?? null;
        $searchColumns = request('searchColumns') ?? null;

        $sort = request('sort') ?? null;
        $sortBy = request('sortBy') ?? null;
        $sortColumns = request('sortColumns') ?? null;

        $customers = $builder->select(
            'customers.id AS id',
            'customers.nama AS nama',
            'customers.email AS email',
            'customers.no_hp AS no_hp',
            'customers.gender AS gender',
            'customers.tanggal_lahir AS tanggal_lahir',
        );

        // if($search && Str::length($search) > 0)
        // {
        //     $listSearch = Str::of(search)->split('/[\s,]+/')->toArray();
        //     $search = count($listSearch) > 0 ? implode("%", $listSearch) : "%{$search}%";

        //     $searchColumns = Str::of($searchColumns)->split('/[\s,]+/')->toArray();

        //     $customers->where(function($query) use ($search, $searchColumns){
        //         foreach($searchColumns as $searchColumn){
        //             $query->orWhereRaw("customers.{$searchColumn} LIKE '{$search}'");

        //         }
        //     });
        // }

        // $sortColumns = Str::of($sortColumns)->split('/[\s,]+/')->toArray();
        // if(collect($sortColumns)->contains($sort) && collect(['ASC', 'DESC']->contains($sort)){
        //     $customers->orderBy("customers.{$sortBy}", $sort);
        // }

        return $customers;
    }
}
