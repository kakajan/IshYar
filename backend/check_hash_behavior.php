// Test Double Hashing Behavior
$password = 'password';
$singleHash = Hash::make($password);
$doubleHash = Hash::make($singleHash);

echo "Single Hash: $singleHash\n";
echo "Double Hash: $doubleHash\n";
echo "Check 'password' vs Single: " . (Hash::check($password, $singleHash) ? 'PASS' : 'FAIL') . "\n";
echo "Check 'password' vs Double: " . (Hash::check($password, $doubleHash) ? 'PASS' : 'FAIL') . "\n";

// Check User again
$user = App\Models\User::where('email', 'admin@ishyar.local')->first();
if ($user) {
echo "USER Hash: " . $user->password . "\n";
echo "Check 'password' vs USER: " . (Hash::check('password', $user->password) ? 'PASS' : 'FAIL') . "\n";
}
