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
  postal_code TEXT,
  registration_date DATE
);

INSERT INTO user (first_name, last_name, email, password, address, city, state, country, postal_code, registration_date) 
VALUES ('Luís', 'Figo', 'luis@figo.com', '356a192b7913b04c54574d18c28d46e6395428ab', 'Avenida dos Aliados', 'Porto', 'Porto', 'Portugal', '12345', '2024-04-20');

CREATE TABLE category (
  id INTEGER PRIMARY KEY,
  name TEXT
);

CREATE TABLE attribute (
  id INTEGER PRIMARY KEY,
  name TEXT,
  type TEXT,
  parent INTEGER,
  FOREIGN KEY (parent) REFERENCES attribute(id)
);

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
  FOREIGN KEY (seller) REFERENCES user(id)
);

CREATE TABLE item_attributes (
  item INTEGER,
  attribute INTEGER,
  value TEXT,
  PRIMARY KEY (item, attribute),
  FOREIGN KEY (item) REFERENCES item(id),
  FOREIGN KEY (attribute) REFERENCES attribute(id)
);