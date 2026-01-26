<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'provider',
        'provider_id',
        'avatar',
        'email_verified_at',
        'is_admin',
        'is_active',
        'suspended_at',
        'suspended_reason',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_active' => 'boolean',
            'suspended_at' => 'datetime',
        ];
    }

    /**
     * ユーザーの診断履歴
     */
    public function diagnoseResults(): BelongsToMany
    {
        return $this->belongsToMany(DiagnoseResult::class, 'user_diagnose_results')
            ->withTimestamps();
    }

    /**
     * ユーザーの訪問済み店舗
     */
    public function visitedStores(): BelongsToMany
    {
        return $this->belongsToMany(Store::class, 'user_visited_stores')
            ->withPivot(['memo', 'visited_at'])
            ->withTimestamps();
    }

    /**
     * SNSログインかどうか
     */
    public function isSocialLogin(): bool
    {
        return !is_null($this->provider);
    }

    /**
     * プロバイダーの表示名
     */
    public function getProviderLabelAttribute(): string
    {
        return match($this->provider) {
            'google' => 'Google',
            'line' => 'LINE',
            'twitter' => 'X (Twitter)',
            default => 'メール',
        };
    }

    /**
     * 管理者かどうか
     */
    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    /**
     * アカウントが有効かどうか
     */
    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    /**
     * アカウントを停止
     */
    public function suspend(string $reason = null): void
    {
        $this->update([
            'is_active' => false,
            'suspended_at' => now(),
            'suspended_reason' => $reason,
        ]);
    }

    /**
     * アカウント停止を解除
     */
    public function unsuspend(): void
    {
        $this->update([
            'is_active' => true,
            'suspended_at' => null,
            'suspended_reason' => null,
        ]);
    }

    /**
     * Filament管理画面へのアクセス権限
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // adminパネルは管理者のみアクセス可能
        if ($panel->getId() === 'admin') {
            return $this->isAdmin();
        }

        return true;
    }
}
