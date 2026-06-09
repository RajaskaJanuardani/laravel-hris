<?php

namespace App\Models\Concerns;

trait UsesIndonesianUserColumns
{
    public function getNameAttribute(): ?string
    {
        return $this->attributes['nama'] ?? null;
    }

    public function setNameAttribute(?string $value): void
    {
        $this->attributes['nama'] = $value;
    }

    public function getRoleAttribute(): ?string
    {
        return $this->attributes['peran'] ?? null;
    }

    public function setRoleAttribute(?string $value): void
    {
        $this->attributes['peran'] = $value;
    }

    public function getPasswordAttribute(): ?string
    {
        return $this->attributes['kata_sandi'] ?? null;
    }

    public function setPasswordAttribute(?string $value): void
    {
        $this->attributes['kata_sandi'] = $value;
    }

    public function getEmailVerifiedAtAttribute()
    {
        return $this->attributes['email_diverifikasi_pada'] ?? null;
    }

    public function setEmailVerifiedAtAttribute($value): void
    {
        $this->attributes['email_diverifikasi_pada'] = $value;
    }

    public function getIsActiveAttribute(): bool
    {
        return (bool) ($this->attributes['aktif'] ?? false);
    }

    public function setIsActiveAttribute(bool $value): void
    {
        $this->attributes['aktif'] = $value;
    }
}
