$pass = 'password';
$h1 = Hash::make($pass);
$h2 = Hash::make($h1);

echo "H1: $h1\n";
echo "H2: $h2\n";
echo "Check pass vs H1: " . (Hash::check($pass, $h1) ? 'PASS' : 'FAIL') . "\n";
echo "Check pass vs H2: " . (Hash::check($pass, $h2) ? 'PASS' : 'FAIL') . "\n";

echo "JWT Secret: " . (config('jwt.secret') ? 'SET' : 'NOT SET') . "\n";
