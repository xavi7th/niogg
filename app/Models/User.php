<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Modules\UserAuth\Notifications\VerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Modules\UserAuth\Notifications\SendPasswordResetNotification;

class User extends Authenticatable implements MustVerifyEmail
{
  use HasApiTokens, HasFactory, Notifiable;

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

  public function getFirstNameAttribute(): string
  {
    return explode(' ', $this->full_name)[0];
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
