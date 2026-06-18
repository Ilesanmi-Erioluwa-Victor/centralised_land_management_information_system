CREATE TABLE IF NOT EXISTS transactions (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  plot_id UUID NOT NULL REFERENCES land_plots(id),
  from_owner_id UUID REFERENCES land_owners(id),
  to_owner_id UUID NOT NULL REFERENCES land_owners(id),
  transaction_type VARCHAR(50) NOT NULL CHECK (transaction_type IN ('sale','lease','inheritance','gift','government_allocation','revocation')),
  amount NUMERIC(15,2),
  currency VARCHAR(10) DEFAULT 'NGN',
  transaction_date DATE NOT NULL,
  notes TEXT,
  status VARCHAR(50) DEFAULT 'pending' CHECK (status IN ('pending','approved','rejected','reversed')),
  approved_by UUID REFERENCES users(id),
  approved_at TIMESTAMPTZ,
  created_by UUID REFERENCES users(id),
  created_at TIMESTAMPTZ DEFAULT NOW()
);
