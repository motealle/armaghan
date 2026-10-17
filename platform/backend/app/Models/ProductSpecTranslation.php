<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
#[Fillable(['product_spec_value_id', 'locale', 'value_text'])]
class ProductSpecTranslation extends Model {}
