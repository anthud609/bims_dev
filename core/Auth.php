<?php
namespace Core;

use App\Modules\Auth\Models\Session as UserSession;
use Carbon\Carbon;

class Auth
{
    public static function check(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $token = $_SESSION['session_token'] ?? null;
        if (! $token) {
            return false;
        }

        // Find the session record
        $sess = UserSession::where('token', $token)
               ->where('is_revoked', false)
               ->where('expires_at',   '>', Carbon::now())
               ->first();

        if (! $sess) {
            return false;
        }

        // Optionally update last_activity and extend sliding expiration:
        $sess->last_activity = Carbon::now();
        // e.g. extend expires_at by another 14 days if nearing expiry
        // $sess->expires_at = Carbon::now()->addDays(14);
        $sess->save();

        return true;
    }

    public static function userId(): ?string
    {
        return self::check()
            ? UserSession::where('token', $_SESSION['session_token'])->first()->user_id
            : null;
    }

    public static function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $token = $_SESSION['session_token'] ?? null;
        if ($token) {
            UserSession::where('token', $token)
                ->update(['is_revoked' => true]);
        }
        session_destroy();
    }
}
