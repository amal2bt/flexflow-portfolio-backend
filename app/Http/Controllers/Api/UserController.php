<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * User management is an administrative function. Employees must never be
     * able to list, create, modify or delete accounts (BOLA / privilege
     * escalation).
     */
    public function index(Request $request): JsonResponse
    {
        $this->requireAdmin($request);

        return response()->json(User::orderBy('id')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $this->requireAdmin($request);

        $data = $this->validated($request, null, true);
        $data['role'] ??= 'employee'; // safe default
        $data['status'] ??= 'active';

        $user = User::create($data);

        return response()->json($user, 201);
    }

    public function show(Request $request, User $user): JsonResponse
    {
        // Object-level authorisation: an employee may read their own profile,
        // admins may read anyone. Everyone else gets 403 (not 404) because the
        // route already exists and the user is authenticated.
        $this->requireSelfOrAdmin($request, $user);

        return response()->json($user);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $actor = $request->user();
        $isAdmin = $actor?->role === 'admin';
        $isSelf = $actor?->is($user) ?? false;

        abort_unless($isAdmin || $isSelf, 403, 'This action is unauthorized.');

        $data = $this->validated($request, $user, $isAdmin);

        if (array_key_exists('password', $data)) {
            // A user changing their own password must prove they know the
            // current one. Admins resetting *another* account do not need it.
            if ($isSelf) {
                $request->validate(['old_password' => 'required|string']);

                if (! Hash::check((string) $request->input('old_password'), (string) $user->password)) {
                    return response()->json([
                        'message' => 'The current password is incorrect.',
                        'errors' => ['old_password' => ['The current password is incorrect.']],
                    ], 422);
                }
            } else {
                // Revoke any token belonging to the account whose password was
                // reset, so a stolen session cannot survive the reset.
                $this->revokeToken($user);
            }
        }

        // Never allow the last administrator to be demoted (this would lock
        // everyone out of user management).
        if ($isAdmin && $user->role === 'admin'
            && array_key_exists('role', $data) && $data['role'] !== 'admin'
            && User::where('role', 'admin')->count() <= 1) {
            return response()->json(['message' => 'Cannot demote the last administrator.'], 422);
        }

        $user->update($data);

        return response()->json($user->fresh());
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        $this->requireAdmin($request);

        if ($request->user()->is($user)) {
            return response()->json(['message' => 'You cannot delete your own account.'], 422);
        }

        if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
            return response()->json(['message' => 'Cannot delete the last administrator.'], 422);
        }

        $this->revokeToken($user);
        $user->delete();

        return response()->json(['deleted' => true]);
    }

    private function validated(Request $request, ?User $user = null, bool $isAdmin = false): array
    {
        // Normalise the email before the unique rule runs, otherwise
        // "Admin@Example.com" and "admin@example.com" could both be stored on
        // a case-sensitive database (SQLite) and then collide at login.
        $rawEmail = $request->input('email');
        if (is_string($rawEmail)) {
            $request->merge(['email' => Str::lower(trim($rawEmail))]);
        }

        $rules = [
            'name' => [$user ? 'sometimes' : 'required', 'string', 'max:255'],
            'email' => [
                $user ? 'sometimes' : 'required',
                'email:rfc',
                'max:255',
                Rule::unique('users', 'email')->ignore($user?->id),
            ],
            // 8 chars minimum. The password is hashed by the model cast.
            'password' => [$user ? 'nullable' : 'required', 'string', Password::min(8)],
            // Accepted only for self-service password changes; never persisted.
            'old_password' => ['nullable', 'string'],
        ];

        // Role and status are privileged fields. They are only accepted from
        // an authenticated admin, and are dropped for everyone else even if
        // the client sends them.
        if ($isAdmin) {
            $rules['role'] = ['nullable', Rule::in(['admin', 'employee'])];
            $rules['status'] = ['nullable', Rule::in(['active', 'pending'])];
        }

        $data = $request->validate($rules);
        unset($data['old_password']);

        if (array_key_exists('password', $data) && $data['password'] === null) {
            unset($data['password']);
        }

        if (! $isAdmin) {
            unset($data['role'], $data['status']);
        }

        return $data;
    }

    private function requireAdmin(Request $request): void
    {
        abort_unless($request->user()?->role === 'admin', 403, 'This action is unauthorized.');
    }

    private function requireSelfOrAdmin(Request $request, User $user): void
    {
        $actor = $request->user();

        abort_unless($actor && ($actor->role === 'admin' || $actor->is($user)), 403, 'This action is unauthorized.');
    }

    private function revokeToken(User $user): void
    {
        $user->forceFill([
            'api_token' => null,
            'last_activity_at' => null,
            'token_expires_at' => null,
        ])->save();
    }
}
