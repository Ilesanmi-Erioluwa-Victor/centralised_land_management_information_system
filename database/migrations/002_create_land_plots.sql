CREATE TABLE IF NOT EXISTS land_plots (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  plot_number VARCHAR(100) NOT NULL UNIQUE,
  land_type VARCHAR(50) NOT NULL CHECK (land_type IN ('urban','agricultural','residential','commercial','industrial')),
  location VARCHAR(500) NOT NULL,
  state VARCHAR(100) NOT NULL,
  lga VARCHAR(100),
  area_sqm NUMERIC(12,2),
  coordinates VARCHAR(200),
  description TEXT,
  status VARCHAR(50) DEFAULT 'available' CHECK (status IN ('available','allocated','disputed','revoked','pending')),
  registered_by UUID REFERENCES users(id),
  created_at TIMESTAMPTZ DEFAULT NOW(),
  updated_at TIMESTAMPTZ DEFAULT NOW()
);
