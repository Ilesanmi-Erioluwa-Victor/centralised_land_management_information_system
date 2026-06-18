INSERT INTO users (full_name, email, password_hash, role, is_active)
VALUES ('System Superadmin', 'admin@clmis.gov', '$2y$12$Qo6B4Q26Qqq2FRfcdTTTFugAlxMuKDUUFkMd7TjoNlVrbnDU3sNZ2', 'superadmin', TRUE)
ON CONFLICT (email) DO NOTHING;
