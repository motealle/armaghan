<?php

namespace App\Filament\Pages\Auth;

use App\Models\ActivityLog;
use App\Services\TemporaryAdminAccessService;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;

class Login extends BaseLogin
{
    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('Email or username')
            ->required()
            ->autocomplete('username')
            ->autofocus();
    }

    public function authenticate(): ?LoginResponse
    {
        $data = $this->form->getState();
        $identifier = strtolower(trim((string) ($data['email'] ?? '')));

        if ($identifier === '' || str_contains($identifier, '@')) {
            return parent::authenticate();
        }

        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();
            return null;
        }

        $user = app(TemporaryAdminAccessService::class)->authenticate($identifier, (string) ($data['password'] ?? ''));
        if (! $user || ! $user->isPrimaryOwner() || ! $this->isUserAllowedToAccessPanel($user)) {
            $this->throwFailureValidationException();
        }

        Filament::auth()->login($user, (bool) ($data['remember'] ?? false));
        session()->regenerate();
        session()->put('armaghan.advanced_admin.user_id', $user->id);
        session()->put('armaghan.advanced_admin.until', now()->addMinutes(15)->timestamp);

        ActivityLog::create([
            'actor_user_id' => $user->id,
            'action' => 'admin.advanced.opened',
            'subject_type' => $user::class,
            'subject_id' => $user->id,
            'metadata' => ['reason' => 'recovery', 'source' => 'filament-owner-alias'],
        ]);

        return app(LoginResponse::class);
    }
}
