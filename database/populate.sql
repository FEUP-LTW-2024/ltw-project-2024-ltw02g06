PRAGMA foreign_keys = ON;

INSERT INTO user (first_name, last_name, email, password, address, city, state, country, zipcode, admin) 
VALUES ('Luís', 'Figo', 'luis@figo.com', '$2y$12$uUhfauycAteP0FIvc5ynRuLVODN3UHb0iKm/T7ABdl7/.Td5o0Hw.', 'Avenida dos Aliados', 'Porto', 'Porto', 'Portugal', '12345', 0),
       ('Luís2', 'Figo2', 'luis2@figo.com', '$2y$12$uUhfauycAteP0FIvc5ynRuLVODN3UHb0iKm/T7ABdl7/.Td5o0Hw.', 'Belém', 'Lisboa', 'Lisboa', 'Portugal', '12345', 0),
       ('LuísAdmin', 'FigoAdmin', 'luisAdmin@figo.com', '$2y$12$uUhfauycAteP0FIvc5ynRuLVODN3UHb0iKm/T7ABdl7/.Td5o0Hw.', 'Praia de Matosinhos', 'Matosinhos', 'Porto', 'Portugal', '12345', 1);

-- password is '1'; '$2y$12$uUhfauycAteP0FIvc5ynRuLVODN3UHb0iKm/T7ABdl7/.Td5o0Hw.' is the hash code of '1'

INSERT INTO category (name)
VALUES ('Outro'),
       ('Category 1'),
       ('Category 2');

INSERT INTO attribute (name, type)
VALUES ('Attribute 1', 'default'),
       ('Estado', 'enum');

INSERT INTO category_attributes (category, attribute)
VALUES (2, 1),
       (3, 2);

INSERT INTO attribute_values (attribute, value)
VALUES (2, 'Novo'),
       (2, 'Usado');

INSERT INTO item (name, description, price, seller, category, creation_date)
VALUES ('Example Item', 'This is a sample item description.', 99.99, 1, 2, DATE('now')),
       ('Example Item 2', 'This is a sample item description 2.', 399.99, 2, 3, DATE('now')),
       ('Example Item', 'This is a sample item description.', 99.99, 1, 2, DATE('now')),
       ('Example Item 2', 'This is a sample item description 2.', 399.99, 2, 3, DATE('now')),
       ('Example Item', 'This is a sample item description.', 99.99, 1, 2, DATE('now')),
       ('Example Item 2', 'This is a sample item description 2.', 399.99, 2, 3, DATE('now')),
       ('Example Item', 'This is a sample item description.', 99.99, 1, 2, DATE('now')),
       ('Example Item 2', 'This is a sample item description 2.', 399.99, 2, 3, DATE('now')),
       ('Example Item', 'This is a sample item description.', 99.99, 1, 2, DATE('now')),
       ('Example Item 2', 'This is a sample item description 2.', 399.99, 2, 3, DATE('now')),
       ('Example Item', 'This is a sample item description.', 99.99, 1, 2, DATE('now')),
       ('Example Item 2', 'This is a sample item description 2.', 399.99, 2, 3, DATE('now')),
       ('Example Item', 'This is a sample item description.', 99.99, 1, 2, DATE('now')),
       ('Example Item 2', 'This is a sample item description 2.', 399.99, 2, 3, DATE('now')),
       ('Example Item', 'This is a sample item description.', 99.99, 1, 2, DATE('now')),
       ('Example Item 2', 'This is a sample item description 2.', 399.99, 2, 3, DATE('now')),
       ('Example Item', 'This is a sample item description.', 99.99, 1, 2, DATE('now')),
       ('Example Item 2', 'This is a sample item description 2.', 399.99, 2, 3, DATE('now')),
       ('Example Item', 'This is a sample item description.', 99.99, 1, 2, DATE('now')),
       ('Example Item 2', 'This is a sample item description 2.', 399.99, 2, 3, DATE('now')),
       ('Example Item', 'This is a sample item description.', 99.99, 1, 2, DATE('now')),
       ('Example Item 2', 'This is a sample item description 2.', 399.99, 2, 3, DATE('now')),
       ('Example Item', 'This is a sample item description.', 99.99, 1, 2, DATE('now')),
       ('Example Item 2', 'This is a sample item description 2.', 399.99, 2, 3, DATE('now')),
       ('Example Item', 'This is a sample item description.', 99.99, 1, 2, DATE('now')),
       ('Example Item 2', 'This is a sample item description 2.', 399.99, 2, 3, DATE('now')),
       ('Example Item', 'This is a sample item description.', 99.99, 1, 2, DATE('now')),
       ('Example Item 2', 'This is a sample item description 2.', 399.99, 2, 3, DATE('now')),
       ('Example Item', 'This is a sample item description.', 99.99, 1, 2, DATE('now')),
       ('Example Item 2', 'This is a sample item description 2.', 399.99, 2, 3, DATE('now')),
       ('Example Item', 'This is a sample item description.', 99.99, 1, 2, DATE('now')),
       ('Example Item 2', 'This is a sample item description 2.', 399.99, 2, 3, DATE('now')),
       ('Example Item', 'This is a sample item description.', 99.99, 1, 2, DATE('now')),
       ('Example Item 2', 'This is a sample item description 2.', 399.99, 2, 3, DATE('now')),
       ('Example Item', 'This is a sample item description.', 99.99, 1, 2, DATE('now')),
       ('Example Item 2', 'This is a sample item description 2.', 399.99, 2, 3, DATE('now')),
       ('Example Item', 'This is a sample item description.', 99.99, 1, 2, DATE('now')),
       ('Example Item 2', 'This is a sample item description 2.', 399.99, 2, 3, DATE('now')),
       ('Example Item', 'This is a sample item description.', 99.99, 1, 2, DATE('now')),
       ('Example Item 2', 'This is a sample item description 2.', 399.99, 2, 3, DATE('now')),
       ('Example Item', 'This is a sample item description.', 99.99, 1, 2, DATE('now')),
       ('Example Item 2', 'This is a sample item description 2.', 399.99, 2, 3, DATE('now')),
       ('Example Item', 'This is a sample item description.', 99.99, 1, 2, DATE('now')),
       ('Example Item 2', 'This is a sample item description 2.', 399.99, 2, 3, DATE('now')),
       ('Example Item', 'This is a sample item description.', 99.99, 1, 2, DATE('now')),
       ('Example Item 2', 'This is a sample item description 2.', 399.99, 2, 3, DATE('now')),
       ('Example Item', 'This is a sample item description.', 99.99, 1, 2, DATE('now')),
       ('Example Item 2', 'This is a sample item description 2.', 399.99, 2, 3, DATE('now')),
       ('Example Item', 'This is a sample item description.', 99.99, 1, 2, DATE('now')),
       ('Example Item 2', 'This is a sample item description 2.', 399.99, 2, 3, DATE('now')),
       ('Example Item', 'This is a sample item description.', 99.99, 1, 2, DATE('now')),
       ('Example Item 2', 'This is a sample item description 2.', 399.99, 2, 3, DATE('now')),
       ('Example Item', 'This is a sample item description.', 99.99, 1, 2, DATE('now')),
       ('Example Item 2', 'This is a sample item description 2.', 399.99, 2, 3, DATE('now')),
       ('Example Item', 'This is a sample item description.', 99.99, 1, 2, DATE('now')),
       ('Example Item 2', 'This is a sample item description 2.', 399.99, 2, 3, DATE('now')),
       ('Example Item', 'This is a sample item description.', 99.99, 1, 2, DATE('now')),
       ('Example Item 2', 'This is a sample item description 2.', 399.99, 2, 3, DATE('now')),
       ('Example Item', 'This is a sample item description.', 99.99, 1, 2, DATE('now')),
       ('Example Item 2', 'This is a sample item description 2.', 399.99, 2, 3, DATE('now')),
       ('Example Item', 'This is a sample item description.', 99.99, 1, 2, DATE('now')),
       ('Example Item 2', 'This is a sample item description 2.', 399.99, 2, 3, DATE('now')),
       ('Example Item', 'This is a sample item description.', 99.99, 1, 2, DATE('now')),
       ('Example Item 2', 'This is a sample item description 2.', 399.99, 2, 3, DATE('now')),
       ('Example Item', 'This is a sample item description.', 99.99, 1, 2, DATE('now')),
       ('Example Item 2', 'This is a sample item description 2.', 399.99, 2, 3, DATE('now')),
       ('Example Item', 'This is a sample item description.', 99.99, 1, 2, DATE('now')),
       ('Example Item 2', 'This is a sample item description 2.', 399.99, 2, 3, DATE('now')),
       ('Example Item', 'This is a sample item description.', 99.99, 1, 2, DATE('now')),
       ('Example Item 2', 'This is a sample item description 2.', 399.99, 2, 3, DATE('now')),
       ('Example Item', 'This is a sample item description.', 99.99, 1, 2, DATE('now')),
       ('Example Item 2', 'This is a sample item description 2.', 399.99, 2, 3, DATE('now'));

INSERT INTO image (path)
VALUES ('database/files/2-1.png'),
       ('database/files/2-2.png'), 
       ('database/files/2-3.png'); 

INSERT INTO item_image (item, image)
VALUES (2, 2),
       (2, 3), 
       (2, 4); 

INSERT INTO item_attributes (item, attribute, value)
VALUES (1, 1, "Attribute Value"),
       (2, 2, "Usado");
       
INSERT INTO user_wishlist (item, user)
VALUES (1, 2);

INSERT INTO user_cart (item, user, price, shipping)
VALUES (1, 2, 99.99, 99.99);

INSERT INTO message (item, sender, receiver, message, type, value, accepted)
VALUES 
  (1, 1, 2, "Hey, I'm interested in your item.", 'default', NULL, 0),
  (2, 2, 1, "Sure, what are you willing to pay?", 'default', NULL, 0),
  (1, 1, 2, "Would you accept $45?", 'negotiation', 45, 0),
  (3, 1, 2, "Hi, I saw your item and I'm interested.", 'default', NULL, 0),
  (2, 2, 1, "Could you provide more details about the condition?", 'default', NULL, 0),
  (3, 1, 2, "Sure, it's in excellent condition.", 'default', NULL, 0),
  (3, 2, 1, "Great, I'll take it!", 'default', NULL, 0),
  (2, 1, 2, "I'm sorry, but I've decided not to sell it anymore.", 'default', NULL, 0);
