PRAGMA foreign_keys = ON;

INSERT INTO user (first_name, last_name, email, password, address, city, state, country, zipcode, admin) 
VALUES ('Luís', 'Figo', 'luis@gmail.com', '$2y$12$uUhfauycAteP0FIvc5ynRuLVODN3UHb0iKm/T7ABdl7/.Td5o0Hw.', 'R. Dr. Roberto Frias', 'Porto', 'Porto', 'Portugal', '4200-465', 1),
       ('Fábio', 'Coentrão', 'fabio@gmail.com', '$2y$12$uUhfauycAteP0FIvc5ynRuLVODN3UHb0iKm/T7ABdl7/.Td5o0Hw.', 'R. Dom Sancho I', 'Vila do Conde', 'Porto', 'Portugal', '4480-876', 0),
       ('Cristiano', 'Aveiro', 'cr7@gmail.com', '$2y$12$uUhfauycAteP0FIvc5ynRuLVODN3UHb0iKm/T7ABdl7/.Td5o0Hw.', 'Al Halilah', 'Tuwaiq', 'Riyadh', 'Arábia Saudita', '14922', 1);

-- password is '1'; '$2y$12$uUhfauycAteP0FIvc5ynRuLVODN3UHb0iKm/T7ABdl7/.Td5o0Hw.' is the hash code of '1'

INSERT INTO category (name)
VALUES ('Eletrônicos'),
       ('Móveis'),
       ('Roupas');

INSERT INTO attribute (name, type)
VALUES ('Cor', 'default'),
       ('Estado', 'enum'),
       ('Cor', 'default'),
       ('Estado', 'enum');

INSERT INTO category_attributes (category, attribute)
VALUES (2, 1),
       (3, 2),
       (4, 3),
       (4, 4);

INSERT INTO attribute_values (attribute, value)
VALUES (2, 'Novo'),
       (2, 'Usado'),
       (4, 'Novo'),
       (4, 'Usado');

INSERT INTO item (name, description, price, seller, category, creation_date)
VALUES ('iPhone 13', 'iPhone 13, 128GB, cor preta, novo na caixa.', 799.99, 1, 2, DATE('now')),
       ('Sofá de Couro', 'Sofá de couro marrom, usado mas em excelente estado.', 399.99, 2, 3, DATE('now')),
       ('Camiseta Polo', 'Camiseta polo branca, tamanho M, nova com etiqueta.', 29.99, 1, 4, DATE('now')),
       ('Smart TV 55"', 'Smart TV 55 polegadas, 4K, nova.', 999.99, 2, 2, DATE('now')),
       ('Mesa de Jantar', 'Mesa de jantar de madeira, acomoda 6 pessoas, usada.', 199.99, 1, 3, DATE('now')),
       ('Jaqueta de Couro', 'Jaqueta de couro preta, tamanho L, usada.', 149.99, 2, 4, DATE('now')),
       ('Laptop Dell XPS 13', 'Laptop Dell XPS 13, 256GB SSD, 8GB RAM, novo.', 1199.99, 1, 2, DATE('now')),
       ('Cadeira de Escritório', 'Cadeira de escritório ergonômica, preta, nova.', 249.99, 2, 3, DATE('now')),
       ('Vestido Floral', 'Vestido floral, tamanho S, novo com etiqueta.', 49.99, 1, 4, DATE('now')),
       ('Console PlayStation 5', 'Console PlayStation 5, novo na caixa.', 499.99, 2, 2, DATE('now')),
       ('PlayStation 5', 'Console PlayStation 5, novo na caixa.', 489.99, 3, 2, DATE('now'));;

INSERT INTO image (path)
VALUES ('database/files/1-1.png'),
       ('database/files/1-2.png'), 
       ('database/files/1-3.png'), 
       ('database/files/2-1.png'),
       ('database/files/2-2.png'), 
       ('database/files/2-3.png'),
       ('database/files/3-1.jpg'), 
       ('database/files/4-1.jpg'), 
       ('database/files/5-1.jpeg'), 
       ('database/files/6-1.jpeg'), 
       ('database/files/6-2.jpeg'), 
       ('database/files/7-1.jpg'), 
       ('database/files/8-1.jpg'), 
       ('database/files/9-1.jpg'), 
       ('database/files/10-1.jpg'); 

INSERT INTO item_image (item, image)
VALUES (1, 2),
       (1, 3), 
       (1, 4),
       (2, 5),
       (2, 6), 
       (2, 7),
       (3, 8),
       (4, 9),
       (5, 10),
       (6, 11),
       (6, 12),
       (7, 13),
       (8, 14),
       (9, 15),
       (10, 16),
       (11, 16); 

INSERT INTO item_attributes (item, attribute, value)
VALUES (1, 1, 'Preta'),
       (2, 2, 'Usado'),
       (3, 3, 'Preto'),
       (3, 4, 'Novo'),
       (4, 1, 'Preta'),
       (5, 2, 'Usado'),
       (6, 3, 'Preto'),
       (6, 4, 'Usado'),
       (7, 1, 'Cinzento'),
       (8, 2, 'Novo'),
       (9, 3, 'Laranja'),
       (9, 4, 'Novo'),
       (10, 1, 'Branco'),
       (11, 1, 'Branco');

INSERT INTO user_wishlist (item, user)
VALUES (1, 2);

INSERT INTO user_cart (item, user, price, shipping)
VALUES (1, 2, 799.99, 19.99);

INSERT INTO message (item, sender, receiver, message, type, value, accepted)
VALUES 
  (1, 1, 2, "Olá, estou interessado no seu iPhone.", 'default', NULL, 0),
  (2, 2, 1, "Claro, qual seria a sua oferta?", 'default', NULL, 0),
  (1, 1, 2, "Você aceitaria $750?", 'negotiation', 750, 0),
  (3, 1, 2, "Oi, vi sua camiseta polo e estou interessado.", 'default', NULL, 0),
  (2, 2, 1, "Poderia fornecer mais detalhes sobre o sofá?", 'default', NULL, 0),
  (3, 1, 2, "Claro, está em ótimo estado sem rasgos ou manchas.", 'default', NULL, 0),
  (3, 2, 1, "Ótimo, eu vou querer!", 'default', NULL, 0),
  (2, 1, 2, "Desculpe, mas decidi não vender o sofá.", 'default', NULL, 0);
