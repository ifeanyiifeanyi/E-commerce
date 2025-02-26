<?php

namespace App\Services;

use Illuminate\Support\Str;

class TwoFactorAuthService
{
    public function enable(string $secretKey): void
    {
        $user = request()->user();

        // Update user record
        $user->update([
            'two_factor_secret' => $secretKey,
            'two_factor_enabled' => true,
            'two_factor_recovery_codes' => $this->generateBackupCodes(true),
        ]);

        // Log this significant security event
        activity()
            ->causedBy($user)
            ->log('Enabled two-factor authentication');
    }

    public function disable(): void
    {
        $user = request()->user();

        // Update user record
        $user->update([
            'two_factor_secret' => null,
            'two_factor_enabled' => false,
            'two_factor_recovery_codes' => null,
        ]);

        // Log this significant security event
        activity()
            ->causedBy($user)
            ->log('Disabled two-factor authentication');
    }

    public function generateBackupCodes(bool $returnArray = false)
    {
        $user = request()->user();

        // Generate 8 random recovery codes
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = Str::random(10);
        }

        // If not generating for first time, update user record
        if (!$returnArray && $user->two_factor_enabled) {
            $user->update([
                'two_factor_recovery_codes' => json_encode($codes),
            ]);

            // Log this security event
            activity()
                ->causedBy($user)
                ->log('Generated new two-factor recovery codes');
        }

        return $returnArray ? json_encode($codes) : $codes;
    }

    public function useRecoveryCode(string $code): bool
    {
        $user = request()->user();

        if (!$user->two_factor_recovery_codes) {
            return false;
        }

        $recoveryCodes = json_decode($user->two_factor_recovery_codes, true);

        // Check if the provided code exists
        $index = array_search($code, $recoveryCodes);

        if ($index !== false) {
            // Remove the used code
            unset($recoveryCodes[$index]);

            // Save the remaining codes
            $user->update([
                'two_factor_recovery_codes' => json_encode(array_values($recoveryCodes)),
            ]);

            // Log this security event
            activity()
                ->causedBy($user)
                ->log('Used a two-factor recovery code');

            return true;
        }

        return false;
    }
}
