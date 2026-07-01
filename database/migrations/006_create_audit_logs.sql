CREATE TABLE IF NOT EXISTS audit_logs (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  user_id UUID REFERENCES users(id) ON DELETE SET NULL,
  action VARCHAR(200) NOT NULL,
  entity_type VARCHAR(100),
  entity_id UUID,
  old_data JSONB,
  new_data JSONB,
  ip_address VARCHAR(255),
  created_at TIMESTAMPTZ DEFAULT NOW()
);
