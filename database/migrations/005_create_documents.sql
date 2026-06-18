CREATE TABLE IF NOT EXISTS documents (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  plot_id UUID REFERENCES land_plots(id) ON DELETE SET NULL,
  owner_id UUID REFERENCES land_owners(id) ON DELETE SET NULL,
  transaction_id UUID REFERENCES transactions(id) ON DELETE SET NULL,
  doc_type VARCHAR(100) NOT NULL,
  file_name VARCHAR(255) NOT NULL,
  file_path TEXT NOT NULL,
  file_size INTEGER,
  mime_type VARCHAR(100),
  uploaded_by UUID REFERENCES users(id),
  created_at TIMESTAMPTZ DEFAULT NOW()
);
