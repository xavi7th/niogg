<?php

namespace Modules\Conference\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Casts\AsCollection;

class PaymentTransaction extends BaseModel
{
  protected $fillable = ['amount', 'description', 'transaction_reference', 'payment_provider', 'api_response', 'purchased_item_id', 'purchased_item_type'];

  protected $casts = [
    'id' => 'int',
    'purchased_item_id' => 'int',
    'amount' => 'double',
    'processed_at' => 'datetime',
    'api_response' => AsCollection::class,
    'is_processed' => 'bool',
  ];

  protected $appends = ['is_processed'];

  public function purchased_item()
  {
    return $this->morphTo();
  }

  public static function getByRef(string $trxrf)
  {
    return self::where('transaction_reference', $trxrf)->sole();
  }

  public function firstName(): Attribute
  {
    return Attribute::make(
        get: fn () => ! is_null($this->processed_at),
    );
  }
}
