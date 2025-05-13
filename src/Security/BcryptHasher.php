<?php
// src/Security/BcryptHasher.php
namespace App\Security;

use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class BcryptHasher implements PasswordHasherInterface
{
    public function hash(string $plainPassword, ?string $salt = null): string
    {
        // Use bcrypt with cost 10 to match jBCrypt's default
        return password_hash($plainPassword, PASSWORD_BCRYPT, ['cost' => 10]);
    }

    public function verify(string $hashedPassword, string $plainPassword, ?string $salt = null): bool
    {
        return password_verify($plainPassword, $hashedPassword);
    }

    public function needsRehash(string $hashedPassword): bool
    {
        // Rehash if not bcrypt or cost differs
        return !preg_match('/^\$2a\$10\$/', $hashedPassword);
    }
}