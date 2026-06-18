CREATE OR REPLACE VIEW land_summary_view AS
SELECT
  lp.id, lp.plot_number, lp.land_type, lp.location, lp.state, lp.lga,
  lp.area_sqm, lp.status,
  lo.full_name AS current_owner,
  lo.phone AS owner_phone,
  po.start_date AS ownership_start,
  COUNT(DISTINCT d.id) AS document_count,
  COUNT(DISTINCT t.id) AS transaction_count
FROM land_plots lp
LEFT JOIN plot_ownership po ON po.plot_id = lp.id AND po.is_current = TRUE
LEFT JOIN land_owners lo ON lo.id = po.owner_id
LEFT JOIN documents d ON d.plot_id = lp.id
LEFT JOIN transactions t ON t.plot_id = lp.id
GROUP BY lp.id, lo.full_name, lo.phone, po.start_date;
