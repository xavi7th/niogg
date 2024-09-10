<?php

namespace Modules\Conference\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Conference\Database\Factories\ConferenceRegistrantFactory;

class ConferenceRegistrant extends User
{
  use HasFactory;

  protected $fillable = ['full_name', 'email', 'phone', 'conference_tag', 'password', 'registration_id'];

  protected $casts = [
    'id' => 'int',
    'registration_processed_at' => 'datetime',
  ];

  public function payment_transactions()
  {
    return $this->morphMany(PaymentTransaction::class, 'purchased_item');
  }

  protected static function newFactory(): ConferenceRegistrantFactory
  {
    return ConferenceRegistrantFactory::new();
  }
}
