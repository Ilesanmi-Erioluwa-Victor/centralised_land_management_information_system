<?php

namespace App\Models;

/**
 * User account persistence and administration queries.
 */
class User extends BaseModel
{
    /**
     * Find a user by UUID.
     *
     * @param string $id
     * @return array<string,mixed>|null
     */
    public function findById(string $id): ?array
    {
        // Retrieve one active or inactive user for session/profile use.
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Find a user by email.
     *
     * @param string $email
     * @return array<string,mixed>|null
     */
    public function findByEmail(string $email): ?array
    {
        // Retrieve login credentials by unique email address.
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute(['email' => strtolower($email)]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Create a user account.
     *
     * @param array<string,mixed> $data
     * @return string
     */
    public function create(array $data): string
    {
        // Insert a new user and return its generated UUID.
        $stmt = $this->db->prepare('INSERT INTO users (full_name, email, password_hash, role) VALUES (:full_name, :email, :password_hash, :role) RETURNING id');
        $stmt->execute([
            'full_name' => $data['full_name'],
            'email' => strtolower($data['email']),
            'password_hash' => password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]),
            'role' => $data['role'] ?? 'officer',
        ]);
        return (string) $stmt->fetchColumn();
    }

    /**
     * Update a user profile or role.
     *
     * @param string $id
     * @param array<string,mixed> $data
     * @return bool
     */
    public function update(string $id, array $data): bool
    {
        // Update administrative user fields.
        $stmt = $this->db->prepare('UPDATE users SET full_name = :full_name, role = :role, updated_at = NOW() WHERE id = :id');
        return $stmt->execute(['id' => $id, 'full_name' => $data['full_name'], 'role' => $data['role']]);
    }

    /**
     * Update a user's password.
     *
     * @param string $id
     * @param string $password
     * @return bool
     */
    public function updatePassword(string $id, string $password): bool
    {
        // Store a fresh bcrypt password hash and clear reset fields.
        $stmt = $this->db->prepare('UPDATE users SET password_hash = :hash, reset_token = NULL, reset_expires = NULL, updated_at = NOW() WHERE id = :id');
        return $stmt->execute(['id' => $id, 'hash' => password_hash($password, PASSWORD_BCRYPT, ['cost' => 12])]);
    }

    /**
     * Store a hashed password reset token.
     *
     * @param string $id
     * @param string $tokenHash
     * @param string $expires
     * @return bool
     */
    public function updateResetToken(string $id, string $tokenHash, string $expires): bool
    {
        // Save the hashed OTP and expiry timestamp.
        $stmt = $this->db->prepare('UPDATE users SET reset_token = :token, reset_expires = :expires, updated_at = NOW() WHERE id = :id');
        return $stmt->execute(['id' => $id, 'token' => $tokenHash, 'expires' => $expires]);
    }

    /**
     * Toggle active account status.
     *
     * @param string $id
     * @return bool
     */
    public function toggleActive(string $id): bool
    {
        // Flip active status for account access control.
        $stmt = $this->db->prepare('UPDATE users SET is_active = NOT is_active, updated_at = NOW() WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    /**
     * List users.
     *
     * @param array<string,mixed> $filters
     * @return array<int,array<string,mixed>>
     */
    public function list(array $filters = []): array
    {
        // Retrieve users for administration.
        $stmt = $this->db->prepare('SELECT * FROM users ORDER BY created_at DESC');
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
