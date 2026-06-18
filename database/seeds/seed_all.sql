-- =============================================================
-- CLMIS – Full Seed Data
-- Run after all 007 migrations have been applied.
-- Usage:  psql "$DATABASE_URL" -f database/seeds/seed_all.sql
-- =============================================================

BEGIN;

DO $$
DECLARE
  -- user ids
  uid_superadmin UUID;
  uid_admin      UUID;
  uid_officer    UUID;
  uid_viewer     UUID;
  -- land owner ids
  own_ade       UUID;
  own_grace     UUID;
  own_zenith    UUID;
  own_greenfield UUID;
  own_fct       UUID;
  own_lasg      UUID;
  -- land plot ids
  plt_lagos     UUID;
  plt_abuja1    UUID;
  plt_abuja2    UUID;
  plt_rivers    UUID;
  plt_kaduna    UUID;
  plt_enugu     UUID;
  plt_kano      UUID;
  plt_ibadan    UUID;
BEGIN

  -- =============================================================
  -- 1. USERS
  -- =============================================================
  -- Password for all: Admin@1234!
  INSERT INTO users (full_name, email, password_hash, role, is_active)
    VALUES ('System Superadmin', 'admin@clmis.gov',
            '$2y$12$Qo6B4Q26Qqq2FRfcdTTTFugAlxMuKDUUFkMd7TjoNlVrbnDU3sNZ2',
            'superadmin', TRUE)
    ON CONFLICT (email) DO UPDATE SET is_active = TRUE
    RETURNING id INTO uid_superadmin;

  INSERT INTO users (full_name, email, password_hash, role, is_active)
    VALUES ('Ibrahim Danjuma', 'ibrahim@clmis.gov',
            '$2y$12$Qo6B4Q26Qqq2FRfcdTTTFugAlxMuKDUUFkMd7TjoNlVrbnDU3sNZ2',
            'admin', TRUE)
    ON CONFLICT (email) DO NOTHING
    RETURNING id INTO uid_admin;

  INSERT INTO users (full_name, email, password_hash, role, is_active)
    VALUES ('Chioma Okafor', 'chioma@clmis.gov',
            '$2y$12$Qo6B4Q26Qqq2FRfcdTTTFugAlxMuKDUUFkMd7TjoNlVrbnDU3sNZ2',
            'officer', TRUE)
    ON CONFLICT (email) DO NOTHING
    RETURNING id INTO uid_officer;

  INSERT INTO users (full_name, email, password_hash, role, is_active)
    VALUES ('Femi Adeyemi', 'femi@clmis.gov',
            '$2y$12$Qo6B4Q26Qqq2FRfcdTTTFugAlxMuKDUUFkMd7TjoNlVrbnDU3sNZ2',
            'viewer', TRUE)
    ON CONFLICT (email) DO NOTHING
    RETURNING id INTO uid_viewer;

  -- fallback if rows already existed (RETURNING yields NULL on conflict)
  IF uid_admin IS NULL THEN
    SELECT id INTO uid_admin FROM users WHERE email = 'ibrahim@clmis.gov';
  END IF;
  IF uid_officer IS NULL THEN
    SELECT id INTO uid_officer FROM users WHERE email = 'chioma@clmis.gov';
  END IF;
  IF uid_viewer IS NULL THEN
    SELECT id INTO uid_viewer FROM users WHERE email = 'femi@clmis.gov';
  END IF;

  -- =============================================================
  -- 2. LAND OWNERS
  -- =============================================================
  INSERT INTO land_owners (full_name, email, phone, national_id, address, owner_type)
    VALUES ('Adebayo Ogunlesi', 'ade.ogunlesi@email.com', '08031234567',
            'NG-1982-123456', '12 Bourdillon Road, Ikoyi, Lagos', 'individual')
    RETURNING id INTO own_ade;

  INSERT INTO land_owners (full_name, email, phone, national_id, address, owner_type)
    VALUES ('Grace Okonkwo', 'grace.okonkwo@email.com', '08099887766',
            'NG-1990-654321', '45 Ziks Avenue, Enugu', 'individual')
    RETURNING id INTO own_grace;

  INSERT INTO land_owners (full_name, email, phone, national_id, address, owner_type)
    VALUES ('Zenith Properties Ltd', 'info@zenithproperties.ng', '07011223344',
            'RC-2015-78901', 'Plot 234, Ahmadu Bello Way, Victoria Island, Lagos', 'corporate')
    RETURNING id INTO own_zenith;

  INSERT INTO land_owners (full_name, email, phone, national_id, address, owner_type)
    VALUES ('Greenfield Agro-Allied Ltd', 'contact@greenfieldagro.ng', '08055667788',
            'RC-2010-45678', 'Km 15, Zaria Road, Kano', 'corporate')
    RETURNING id INTO own_greenfield;

  INSERT INTO land_owners (full_name, email, phone, national_id, address, owner_type)
    VALUES ('Federal Capital Territory Administration', 'land@fcta.gov.ng', '08090000111',
            'GOV-FCT-001', 'Area 11, Garki, Abuja', 'government')
    RETURNING id INTO own_fct;

  INSERT INTO land_owners (full_name, email, phone, national_id, address, owner_type)
    VALUES ('Lagos State Government', 'lands@lagosstate.gov.ng', '08090000222',
            'GOV-LAS-001', 'The Secretariat, Alausa, Ikeja, Lagos', 'government')
    RETURNING id INTO own_lasg;

  -- =============================================================
  -- 3. LAND PLOTS
  -- =============================================================
  INSERT INTO land_plots (plot_number, land_type, location, state, lga, area_sqm, coordinates, description, status, registered_by)
    VALUES ('LAS/IK/001', 'residential', '12 Bourdillon Road, Ikoyi', 'Lagos', 'Eti-Osa', 850.50,
            '6.4478° N, 3.4350° E',
            'Prime residential plot in high-end Ikoyi neighbourhood', 'allocated', uid_superadmin)
    RETURNING id INTO plt_lagos;

  INSERT INTO land_plots (plot_number, land_type, location, state, lga, area_sqm, coordinates, description, status, registered_by)
    VALUES ('FCT/MT/002', 'commercial', 'Plot 88, Maitama District', 'Abuja', 'Municipal', 1200.00,
            '9.0833° N, 7.4833° E',
            'Commercial plot opposite Maitama shopping complex', 'allocated', uid_admin)
    RETURNING id INTO plt_abuja1;

  INSERT INTO land_plots (plot_number, land_type, location, state, lga, area_sqm, coordinates, description, status, registered_by)
    VALUES ('FCT/WU/003', 'residential', 'Wuse Zone 4, Abuja', 'Abuja', 'Municipal', 650.00,
            '9.0750° N, 7.4800° E',
            'Medium-size residential plot in Wuse district', 'available', uid_officer)
    RETURNING id INTO plt_abuja2;

  INSERT INTO land_plots (plot_number, land_type, location, state, lga, area_sqm, coordinates, description, status, registered_by)
    VALUES ('RIV/PH/004', 'industrial', 'Trans-Amadi Industrial Layout, Port Harcourt', 'Rivers', 'Obio-Akpor', 5000.00,
            '4.8167° N, 7.0333° E',
            'Large industrial plot in Port Harcourt hub', 'pending', uid_admin)
    RETURNING id INTO plt_rivers;

  INSERT INTO land_plots (plot_number, land_type, location, state, lga, area_sqm, coordinates, description, status, registered_by)
    VALUES ('KAD/KM/005', 'agricultural', 'Kakuri, Kaduna South', 'Kaduna', 'Kaduna South', 15000.00,
            '10.5167° N, 7.4333° E',
            'Expansive farmland along Kaduna river bank', 'available', uid_officer)
    RETURNING id INTO plt_kaduna;

  INSERT INTO land_plots (plot_number, land_type, location, state, lga, area_sqm, coordinates, description, status, registered_by)
    VALUES ('ENU/EN/006', 'residential', 'Independence Layout, Enugu', 'Enugu', 'Enugu North', 500.00,
            '6.4333° N, 7.4833° E',
            'Corner plot in quiet residential area', 'disputed', uid_superadmin)
    RETURNING id INTO plt_enugu;

  INSERT INTO land_plots (plot_number, land_type, location, state, lga, area_sqm, coordinates, description, status, registered_by)
    VALUES ('KAN/MM/007', 'commercial', 'Murtala Mohammed Way, Kano', 'Kano', 'Kano Municipal', 2000.00,
            '12.0000° N, 8.5167° E',
            'High-traffic commercial plot in Kano metropolis', 'available', uid_admin)
    RETURNING id INTO plt_kano;

  INSERT INTO land_plots (plot_number, land_type, location, state, lga, area_sqm, coordinates, description, status, registered_by)
    VALUES ('OYO/IB/008', 'residential', 'Bodija Estate, Ibadan', 'Oyo', 'Ibadan North', 720.00,
            '7.3833° N, 3.9500° E',
            'Well-served residential plot in Bodija', 'revoked', uid_officer)
    RETURNING id INTO plt_ibadan;

  -- =============================================================
  -- 4. PLOT OWNERSHIP
  -- =============================================================
  -- Ikoyi plot  -> Adebayo Ogunlesi (individual)
  INSERT INTO plot_ownership (plot_id, owner_id, ownership_percent, start_date, is_current)
    VALUES (plt_lagos, own_ade, 100.00, '2018-03-15', TRUE);

  -- Maitama plot -> Zenith Properties Ltd (corporate)
  INSERT INTO plot_ownership (plot_id, owner_id, ownership_percent, start_date, is_current)
    VALUES (plt_abuja1, own_zenith, 100.00, '2020-07-01', TRUE);

  -- Enugu plot -> Grace Okonkwo (individual), but disputed
  INSERT INTO plot_ownership (plot_id, owner_id, ownership_percent, start_date, end_date, is_current)
    VALUES (plt_enugu, own_grace, 50.00, '2019-01-10', '2023-06-15', FALSE);

  -- Also claimed by Zenith on the same Enugu plot (dispute)
  INSERT INTO plot_ownership (plot_id, owner_id, ownership_percent, start_date, is_current)
    VALUES (plt_enugu, own_zenith, 50.00, '2023-06-16', TRUE);

  -- Ibadan plot -> originally Greenfield (revoked)
  INSERT INTO plot_ownership (plot_id, owner_id, ownership_percent, start_date, end_date, is_current)
    VALUES (plt_ibadan, own_greenfield, 100.00, '2017-11-20', '2024-01-30', FALSE);

  -- Ibadan plot -> now back to LASG (revoked)
  INSERT INTO plot_ownership (plot_id, owner_id, ownership_percent, start_date, is_current)
    VALUES (plt_ibadan, own_lasg, 100.00, '2024-02-01', TRUE);

  -- =============================================================
  -- 5. TRANSACTIONS
  -- =============================================================
  -- Sale: Bourdillon plot allocated to Adebayo
  INSERT INTO transactions (plot_id, from_owner_id, to_owner_id, transaction_type, amount, currency, transaction_date, notes, status, approved_by, approved_at, created_by)
    VALUES (plt_lagos, own_lasg, own_ade, 'government_allocation', 45000000.00, 'NGN',
            '2018-03-15',
            'State allocation of Ikoyi plot to individual under land reform scheme',
            'approved', uid_superadmin, NOW(), uid_superadmin);

  -- Lease: Maitama plot leased to Zenith
  INSERT INTO transactions (plot_id, from_owner_id, to_owner_id, transaction_type, amount, currency, transaction_date, notes, status, approved_by, approved_at, created_by)
    VALUES (plt_abuja1, own_fct, own_zenith, 'lease', 12000000.00, 'NGN',
            '2020-07-01',
            '99-year lease of commercial plot in Maitama',
            'approved', uid_superadmin, NOW(), uid_admin);

  -- Revocation: Ibadan plot revoked from Greenfield
  INSERT INTO transactions (plot_id, from_owner_id, to_owner_id, transaction_type, amount, currency, transaction_date, notes, status, approved_by, approved_at, created_by)
    VALUES (plt_ibadan, own_greenfield, own_lasg, 'revocation', NULL, 'NGN',
            '2024-02-01',
            'Revoked due to non-development over 7 years',
            'approved', uid_superadmin, NOW(), uid_admin);

  -- Inheritance: Enugu plot transfer dispute
  INSERT INTO transactions (plot_id, from_owner_id, to_owner_id, transaction_type, amount, currency, transaction_date, notes, status, created_by)
    VALUES (plt_enugu, own_grace, own_zenith, 'inheritance', NULL, 'NGN',
            '2023-06-16',
            'Disputed inheritance claim – contested by Grace Okonkwo',
            'pending', uid_officer);

  -- Sale: pending transaction for Kano commercial plot
  INSERT INTO transactions (plot_id, from_owner_id, to_owner_id, transaction_type, amount, currency, transaction_date, notes, status, created_by)
    VALUES (plt_kano, own_fct, own_greenfield, 'sale', 75000000.00, 'NGN',
            '2025-11-01',
            'Proposed sale of Kano commercial plot to Greenfield Agro-Allied',
            'pending', uid_officer);

  -- =============================================================
  -- 6. DOCUMENTS
  -- =============================================================
  INSERT INTO documents (plot_id, owner_id, transaction_id, doc_type, file_name, file_path, file_size, mime_type, uploaded_by)
    VALUES (plt_lagos, own_ade, NULL, 'Title Deed',
            'LAS_IK_001_title_deed.pdf', '/uploads/documents/LAS_IK_001_title_deed.pdf',
            204800, 'application/pdf', uid_admin);

  INSERT INTO documents (plot_id, owner_id, transaction_id, doc_type, file_name, file_path, file_size, mime_type, uploaded_by)
    VALUES (plt_abuja1, own_zenith, NULL, 'Lease Agreement',
            'FCT_MT_002_lease_agreement.pdf', '/uploads/documents/FCT_MT_002_lease_agreement.pdf',
            153600, 'application/pdf', uid_admin);

  INSERT INTO documents (plot_id, owner_id, transaction_id, doc_type, file_name, file_path, file_size, mime_type, uploaded_by)
    VALUES (plt_enugu, own_grace, NULL, 'Certificate of Occupancy',
            'ENU_EN_006_c_of_o.pdf', '/uploads/documents/ENU_EN_006_c_of_o.pdf',
            102400, 'application/pdf', uid_officer);

  INSERT INTO documents (plot_id, owner_id, transaction_id, doc_type, file_name, file_path, file_size, mime_type, uploaded_by)
    VALUES (plt_ibadan, NULL, NULL, 'Survey Plan',
            'OYO_IB_008_survey_plan.pdf', '/uploads/documents/OYO_IB_008_survey_plan.pdf',
            51200, 'application/pdf', uid_officer);

  -- =============================================================
  -- 7. AUDIT LOGS
  -- =============================================================
  INSERT INTO audit_logs (user_id, action, entity_type, entity_id, old_data, new_data, ip_address)
    VALUES (uid_superadmin, 'SEED', 'system', gen_random_uuid(),
            NULL,
            '{"event": "full_database_seed_executed"}',
            '127.0.0.1');

  RAISE NOTICE 'Seeding complete: 4 users, 6 land owners, 8 plots, 7 ownership records, 5 transactions, 4 documents.';

END $$;

COMMIT;
