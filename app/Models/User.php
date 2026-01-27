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
    'is_admin',
    'is_super_admin',
  ];

  protected $hidden = [
    'password',
    'remember_token',
  ];

  protected $casts = [
    'email_verified_at' => 'datetime',
    'password' => 'hashed',
    'is_admin' => 'boolean',
    'is_super_admin' => 'boolean',
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

  public function isAdmin(): bool
  {
    return $this->is_admin || $this->is_super_admin;
  }

  public function isSuperAdmin(): bool
  {
    return $this->is_super_admin;
  }

  public function getType(): string
  {
    if ($this->is_super_admin) {
      return 'SuperAdmin';
    }

    if ($this->is_admin) {
      return 'Admin';
    }

    return 'AppUser';
  }
}
