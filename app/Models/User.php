<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Modules\UserAuth\Notifications\VerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Modules\UserAuth\Notifications\SendPasswordResetNotification;

class User extends Authenticatable // implements MustVerifyEmail
{
  use HasFactory, Notifiable;

  protected $fillable = [
    'name',
    'email',
    'password',
  ];

  protected $hidden = [
    'password',
    'remember_token',
  ];

  protected $casts = [
    'email_verified_at' => 'datetime',
    'password' => 'hashed',
  ];

  public function firstName(): Attribute
  {
    return Attribute::make(
        get: fn () => explode(' ', $this->full_name)[0],
    );
  }

  /**
   * Send the password reset notification.
   *
   * @param  string  $token
   */
  public function sendPasswordResetNotification($token): void
  {
    $this->notify(new SendPasswordResetNotification($token));
  }

  /**
   * Send the email verification notification.
   */
  public function sendEmailVerificationNotification(): void
  {
    $this->notify(new VerifyEmail());
  }
}
