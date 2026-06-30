USE gestion_abarrotes_test;

INSERT INTO usuarios (usuario, clave, nombre_completo, email, rol, estado) VALUES
  ('admin', '$2y$12$gNA0mZtoL122oAQcw8UxXebgT1AV5EReNYH5HFNQxGFazLD2bXmju', 'Administrador Pruebas', 'admin@tienda.test', 'admin', 1),
  ('admin@tienda.com', '$2y$12$8hyZrBC5l3.Wz0IOdt3oWeBgbS3zRe5C6I.7hR1bZ0caznnnoVY56', 'Administrador Tienda', 'admin@tienda.com', 'admin', 1),
  ('inactivo@tienda.com', '$2y$12$8hyZrBC5l3.Wz0IOdt3oWeBgbS3zRe5C6I.7hR1bZ0caznnnoVY56', 'Usuario Inactivo', 'inactivo@tienda.com', 'admin', 0);

INSERT INTO proveedores (ruc, razon_social, contacto, telefono, email, direccion, estado) VALUES
  ('20999888777', 'Proveedor Sin Productos SAC', 'Maria Lopez', '999888777', 'sinproductos@test.com', 'Calle Pruebas 456', 1),
  ('20111111111', 'Proveedor Inactivo SAC', 'Luis Rojas', '911111111', 'inactivo@test.com', 'Av. Dormida 100', 0);

INSERT INTO productos (
  codigo,
  nombre,
  categoria,
  precio_compra,
  precio_venta,
  stock,
  stock_minimo,
  proveedor_id,
  fecha_vencimiento,
  estado
) VALUES
  ('PROD-LOW', 'Aceite Bajo Stock', 'Aceites', 6.20, 8.50, 3, 10, NULL, '2026-12-31', 1),
  ('PROD-INACTIVO', 'Producto Inactivo', 'Granos', 1.00, 2.00, 10, 5, NULL, '2026-12-31', 0);
