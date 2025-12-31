
$user = App\Models\User::where('email', 'admin@ishyar.local')->first();
if ($user) {
    echo "USER: " . $user->email . "\n";
    echo "HASH: " . $user->password . "\n";
    echo "CHECK: " . (Hash::check('password', $user->password) ? 'PASS' : 'FAIL') . "\n";

    // Check Guard
    echo "GUARD (api): " . config('auth.guards.api.driver') . "\n";
    echo "PROVIDER (api): " . config('auth.guards.api.provider') . "\n";

    // Test Auth Attempt
    try {
        if (Auth::attempt(['email' => 'admin@ishyar.local', 'password' => 'password'])) {
             echo "AUTH::ATTEMPT: SUCCESS\n";
        } else {
             echo "AUTH::ATTEMPT: FAIL\n";
        }
    } catch (\Exception $e) {
        echo "AUTH::ATTEMPT ERROR: " . $e->getMessage() . "\n";
    }

    // Test JWT Attempt
    try {
        if ($token = Tymon\JWTAuth\Facades\JWTAuth::attempt(['email' => 'admin@ishyar.local', 'password' => 'password'])) {
            echo "JWT::ATTEMPT: SUCCESS (Token generated)\n";
        } else {
            echo "JWT::ATTEMPT: FAIL (Invalid credentials or other error)\n";
        }
    } catch (\Exception $e) {
        echo "JWT::ATTEMPT ERROR: " . $e->getMessage() . "\n";
    }
} else {
    echo "User not found.\n";
}
