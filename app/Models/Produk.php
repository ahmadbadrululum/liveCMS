<?php

namespace App\Models;

use App\liveCommerce\Models\LiveCommerceBaseModel as BaseModel;

class Produk extends BaseModel
{
    protected $fillable = ['produk', 'slug', 'produk_kategori_id', 'harga', 'harga_diskon', 'deskripsi', 'netto', 'foto', 'produk_brand_id', 'stock'];

    protected $dependencies = ['produkKategori', 'produkBrand'];

    public function rules()
    {
        $slug = str_slug(request()->has('slug') ? request()->get('slug') : request()->get('produk'));

        request()->merge(compact('slug'));

        return [
            'produk' => 'required|unique:'.$this->getTable().',produk'.(($this->id != null) ? ','.$this->id : ''),
            'slug' => 'required|unique:'.$this->getTable().',slug'.(($this->id != null) ? ','.$this->id : ''),
            'harga' => 'required|numeric',
            'netto' => 'required|numeric',
        ];
    }

    public function produkFotos()
    {
        return $this->hasMany(ProdukFoto::class);
    }

    public function produkKategori()
    {
        return $this->belongsTo(ProdukKategori::class);
    }

    public function produkBrand()
    {
        return $this->belongsTo(ProdukBrand::class);
    }
}
