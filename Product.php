<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Tên bảng
    protected $table = 'sv23810310277_products';

    // Trường có thể gán hàng loạt
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'stock_quantity',
        'image_path',
        'status',
        'warranty_period',     // trường sáng tạo
        'discount_percent',    // trường sáng tạo
    ];

    // Quan hệ với Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Logic 1: Thông báo bảo hành
    public function getWarrantyMessageAttribute(): string
    {
        if ($this->warranty_period >= 24) {
            return 'Long warranty';
        }
        return 'Standard warranty';
    }

    // Logic 2: Giá sau giảm giá
    public function getPriceAfterDiscountAttribute(): float
    {
        return $this->price * (1 - $this->discount_percent / 100);
    }

    // Logic 3: Trạng thái sản phẩm
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft' => 'Draft',
            'published' => 'Published',
            'out_of_stock' => 'Out of Stock',
            default => 'Unknown',
        };
    }
}