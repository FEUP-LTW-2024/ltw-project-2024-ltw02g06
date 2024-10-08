PRAGMA foreign_keys = ON;

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
VALUES ('Outro');

CREATE TABLE attribute (
  id INTEGER PRIMARY KEY,
  name TEXT,
  type TEXT DEFAULT 'default' CHECK (type IN ('default', 'enum', 'real', 'int')) NOT NULL
);

CREATE TABLE attribute_values (
  id INTEGER PRIMARY KEY,
  attribute INTEGER,
  value TEXT,
  FOREIGN KEY (attribute) REFERENCES attribute(id) ON DELETE CASCADE
);

CREATE TABLE category_attributes (
  category INTEGER,
  attribute INTEGER,
  PRIMARY KEY (category, attribute),
  FOREIGN KEY (category) REFERENCES category(id) ON DELETE CASCADE,
  FOREIGN KEY (attribute) REFERENCES attribute(id) ON DELETE CASCADE
);

CREATE TABLE item (
  id INTEGER PRIMARY KEY,
  name TEXT,
  description TEXT,
  price REAL DEFAULT 0,
  seller INTEGER,
  buyer INTEGER,
  category INTEGER,
  status TEXT DEFAULT 'active' CHECK (status IN ('active', 'to send', 'sold')),
  sold_price REAL,
  creation_date DATE DEFAULT CURRENT_DATE,
  clicks INTEGER DEFAULT 0,
  FOREIGN KEY (seller) REFERENCES user(id) ON DELETE CASCADE,
  FOREIGN KEY (buyer) REFERENCES user(id) ON DELETE CASCADE,
  FOREIGN KEY (category) REFERENCES category(id)
);

CREATE TABLE image (
  id INTEGER PRIMARY KEY,
  path TEXT
);

INSERT INTO image (path)
VALUES ('database/files/default_profile_picture.svg'); 

CREATE TABLE item_image (
  item INTEGER,
  image INTEGER,
  PRIMARY KEY (item, image),
  FOREIGN KEY (item) REFERENCES item(id) ON DELETE CASCADE,
  FOREIGN KEY (image) REFERENCES image(id) ON DELETE CASCADE
);

CREATE TABLE item_attributes (
  item INTEGER,
  attribute INTEGER,
  value TEXT,
  PRIMARY KEY (item, attribute),
  FOREIGN KEY (item) REFERENCES item(id) ON DELETE CASCADE,
  FOREIGN KEY (attribute) REFERENCES attribute(id) ON DELETE CASCADE
);

CREATE TABLE user_wishlist (
  item INTEGER,
  user INTEGER,
  PRIMARY KEY (item, user),
  FOREIGN KEY (item) REFERENCES item(id) ON DELETE CASCADE,
  FOREIGN KEY (user) REFERENCES user(id) ON DELETE CASCADE
);

CREATE TABLE user_cart (
  item INTEGER,
  user INTEGER,
  price REAL,
  shipping REAL,
  PRIMARY KEY (item, user),
  FOREIGN KEY (item) REFERENCES item(id) ON DELETE CASCADE,
  FOREIGN KEY (user) REFERENCES user(id) ON DELETE CASCADE
);

CREATE TABLE message (
  id INTEGER PRIMARY KEY,
  item INTEGER NOT NULL,
  sender INTEGER NOT NULL,
  receiver INTEGER NOT NULL,
  message TEXT,
  timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
  type TEXT DEFAULT 'default' CHECK (type IN ('default', 'negotiation')),
  value REAL,
  accepted INTEGER DEFAULT 0,
  FOREIGN KEY (item) REFERENCES item(id) ON DELETE CASCADE,
  FOREIGN KEY (sender) REFERENCES user(id) ON DELETE CASCADE,
  FOREIGN KEY (receiver) REFERENCES user(id) ON DELETE CASCADE
)