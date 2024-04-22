CREATE TABLE user (
  id INTEGER PRIMARY KEY,
  first_name TEXT,
  last_name TEXT,
  email TEXT UNIQUE,
  password TEXT,
  address TEXT,
  city TEXT,
  state TEXT,
  country TEXT,
  zipcode TEXT,
  image INTEGER DEFAULT 1,
  admin INTEGER DEFAULT 0,
  registration_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (image) REFERENCES image(id)
);

INSERT INTO user (first_name, last_name, email, password, address, city, state, country, zipcode) 
VALUES ('Luís', 'Figo', 'luis@figo.com', '356a192b7913b04c54574d18c28d46e6395428ab', 'Avenida dos Aliados', 'Porto', 'Porto', 'Portugal', '12345');
-- password is '1'; '356a192b7913b04c54574d18c28d46e6395428ab' is the hash code of '1'

CREATE TABLE review (
  id INTEGER PRIMARY KEY,
  reviewed_user INTEGER,
  reviewer_user INTEGER,
  rating INTEGER,
  comment TEXT,
  timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (reviewed_user) REFERENCES user(id),
  FOREIGN KEY (reviewer_user) REFERENCES user(id)
);

CREATE TABLE category (
  id INTEGER PRIMARY KEY,
  name TEXT
);

INSERT INTO category (name)
VALUES ('Category Name');

CREATE TABLE attribute (
  id INTEGER PRIMARY KEY,
  name TEXT,
  type TEXT DEFAULT 'default' CHECK (type IN ('default', 'enum')),
  parent INTEGER,
  FOREIGN KEY (parent) REFERENCES attribute(id)
);

INSERT INTO attribute (name)
VALUES ('Attribute Name');

CREATE TABLE attribute_values (
  id INTEGER PRIMARY KEY,
  value TEXT,
  attribute INTEGER,
  parent INTEGER,
  FOREIGN KEY (attribute) REFERENCES attribute(id),
  FOREIGN KEY (parent) REFERENCES attribute_values(id)
);

CREATE TABLE category_attributes (
  category INTEGER,
  attribute INTEGER,
  PRIMARY KEY (category, attribute),
  FOREIGN KEY (category) REFERENCES category(id),
  FOREIGN KEY (attribute) REFERENCES attribute(id)
);

INSERT INTO category_attributes (category, attribute)
VALUES (1, 1);

CREATE TABLE item (
  id INTEGER PRIMARY KEY,
  name TEXT,
  description TEXT,
  price REAL,
  seller INTEGER,
  category INTEGER,
  status TEXT DEFAULT 'active' CHECK (status IN ('active', 'to send', 'sold')),
  sold_price REAL,
  creation_date DATE,
  clicks INTEGER DEFAULT 0,
  FOREIGN KEY (seller) REFERENCES user(id),
  FOREIGN KEY (category) REFERENCES category(id)
);

INSERT INTO item (name, description, price, seller, category, creation_date)
VALUES ('Example Item', 'This is a sample item description.', 99.99, 1, 1, DATE('now'));

CREATE TABLE image (
  id INTEGER PRIMARY KEY,
  path TEXT
);

-- Change this later:
INSERT INTO image (path)
VALUES ('database/files/default_profile_picture.svg'); 

CREATE TABLE item_image (
  item INTEGER,
  image INTEGER,
  PRIMARY KEY (item, image)
  FOREIGN KEY (item) REFERENCES item(id),
  FOREIGN KEY (image) REFERENCES image(id)
);

CREATE TABLE item_attributes (
  item INTEGER,
  attribute INTEGER,
  value TEXT,
  PRIMARY KEY (item, attribute),
  FOREIGN KEY (item) REFERENCES item(id),
  FOREIGN KEY (attribute) REFERENCES attribute(id)
);

INSERT INTO item_attributes (item, attribute, value)
VALUES (1, 1, "Attribute Value");

CREATE TABLE user_wishlist (
  item INTEGER,
  user INTEGER,
  PRIMARY KEY (item, user),
  FOREIGN KEY (item) REFERENCES item(id),
  FOREIGN KEY (user) REFERENCES user(id)
);

CREATE TABLE user_cart (
  item INTEGER,
  user INTEGER,
  price INTEGER,
  shipping INTEGER,
  PRIMARY KEY (item, user),
  FOREIGN KEY (item) REFERENCES item(id),
  FOREIGN KEY (user) REFERENCES user(id)
);