CREATE TABLE IF NOT EXISTS land_owners (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  full_name VARCHAR(255) NOT NULL,
  email VARCHAR(255),
  phone VARCHAR(30),
  national_id VARCHAR(100),
  address TEXT,
  owner_type VARCHAR(50) DEFAULT 'individual' CHECK (owner_type IN ('individual','corporate','government')),
  created_at TIMESTAMPTZ DEFAULT NOW(),
  updated_at TIMESTAMPTZ DEFAULT NOW()
);
CREATE TABLE IF NOT EXISTS plot_ownership (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  plot_id UUID NOT NULL REFERENCES land_plots(id) ON DELETE CASCADE,
  owner_id UUID NOT NULL REFERENCES land_owners(id) ON DELETE CASCADE,
  ownership_percent NUMERIC(5,2) DEFAULT 100.00,
  start_date DATE NOT NULL,
  end_date DATE,
  is_current BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMPTZ DEFAULT NOW()
);
