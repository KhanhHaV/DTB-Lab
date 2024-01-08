CREATE TABLE cart (
  user_id int,
  product_id int,
  color varchar(20) DEFAULT NULL,
  version varchar(20) DEFAULT NULL,
  quantity int DEFAULT NULL,
  cprice int NOT NULL DEFAULT 0
);


CREATE TABLE orders (
  order_id int PRIMARY KEY IDENTITY(1,1),
  user_id int DEFAULT NULL,
  fname varchar(20) DEFAULT NULL,
  lname varchar(20) DEFAULT NULL,
  phone varchar(15) DEFAULT NULL,
  email varchar(50) DEFAULT NULL,
  street varchar(40) DEFAULT NULL,
  town varchar(40) DEFAULT NULL,
  state varchar(4) DEFAULT NULL,
  post_code varchar(5) DEFAULT NULL,
  pref_contact varchar(5) DEFAULT NULL,
  card_type varchar(20) DEFAULT NULL,
  nameoncard varchar(50) DEFAULT NULL,
  card_number varchar(30) DEFAULT NULL,
  expiry varchar(20) DEFAULT NULL,
  cvv varchar(20) DEFAULT NULL,
  order_cost int DEFAULT NULL,
  order_status VARCHAR(30) NOT NULL DEFAULT 'PENDING' CHECK(order_status IN('PENDING','FULFILLED','PAID','ARCHIVED')) ,
  order_time datetime DEFAULT NULL
);



CREATE TABLE orders (
  order_id int PRIMARY KEY IDENTITY(1,1),
  user_id int DEFAULT NULL,
  pref_contact varchar(5) DEFAULT NULL,
  card_type varchar(20) DEFAULT NULL,
  nameoncard varchar(50) DEFAULT NULL,
  card_number varchar(30) DEFAULT NULL,
  expiry varchar(20) DEFAULT NULL,
  cvv varchar(20) DEFAULT NULL,
  order_cost int DEFAULT 0,
  order_items int DEFAULT 0,
  order_status VARCHAR(30) NOT NULL DEFAULT 'PENDING' CHECK(order_status IN('PENDING','FULFILLED','PAID','ARCHIVED')) ,
  order_time datetime DEFAULT NULL
);


CREATE TABLE order_products (
  order_id int,
  product_id int,
  color varchar(20) DEFAULT NULL,
  version varchar(20) DEFAULT NULL,
  quantity int DEFAULT NULL,
  PRIMARY KEY(order_id,product_id)
);


CREATE TABLE products (
  product_id int PRIMARY KEY IDENTITY(1,1),
  pname varchar(40) DEFAULT NULL,
  pdesc text,
  pprice int DEFAULT NULL,
  pimage nvarchar(max),
  pimagetype varchar(5) DEFAULT NULL,
  discount int DEFAULT 0,
  pstock int NOT NULL,
  cat_id int,
  pdate datetime DEFAULT NULL
);

CREATE TABLE category(
  cat_id int IDENTITY(1,1),
  cat_name varchar(30),
  PRIMARY KEY (cat_id,cat_name)
);

/*CREATE TABLE discount_detail(
	discount_id int PRIMARY KEY,
	product_id,
)

CREATE TABLE discount(
	discount_id int PRIMARY KEY IDENTITY(0,1),
	ddesc text,

	dpercent int UNIQUE
)*/
-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE users (
  user_id int PRIMARY KEY IDENTITY(1,1),
  fname varchar(40) DEFAULT NULL,
  lname varchar(40) DEFAULT NULL,
  phone varchar(15) DEFAULT NULL,
  email varchar(50) UNIQUE DEFAULT NULL ,
  avatar nvarchar(max) DEFAULT NULL ,
  avatar_type varchar(10) DEFAULT NULL,
  address text,
  type int DEFAULT NULL,
  password varchar(40) DEFAULT NULL
);

CREATE TABLE users (
  user_id int PRIMARY KEY IDENTITY(1,1),
  fname varchar(40) DEFAULT NULL,
  lname varchar(40) DEFAULT NULL,
  phone varchar(15) DEFAULT NULL,
  email varchar(50) UNIQUE DEFAULT NULL ,
  avatar nvarchar(max) DEFAULT NULL ,
  avatar_type varchar(10) DEFAULT NULL,
  street varchar(40) DEFAULT NULL,
  town varchar(40) DEFAULT NULL,
  state varchar(4) DEFAULT NULL,
  post_code varchar(5) DEFAULT NULL,
  address text,
  type int DEFAULT NULL,
  password varchar(40) DEFAULT NULL
);



SET IDENTITY_INSERT users ON
INSERT INTO users (user_id, fname, lname, phone, email, avatar, avatar_type, address, type, password)
VALUES (2, 'Admin', 'Admin', '', 'admin', NULL, NULL, NULL, 1, 'password');
SET IDENTITY_INSERT users OFF

CREATE TRIGGER trg_AfterInsertCart
ON cart
AFTER INSERT
AS
BEGIN
    UPDATE cart
    SET cprice = p.pprice * i.quantity +
                 CASE 
                     WHEN i.version = 'fea1' THEN 50
                     WHEN i.version = 'fea2' THEN 100
                     WHEN i.version = 'fea3' THEN 200
                     ELSE 0
                 END
    FROM inserted i
    INNER JOIN products p ON i.product_id = p.product_id
    WHERE cart.user_id = i.user_id;
END;

-- Trigger for AFTER UPDATE on cart table
CREATE TRIGGER trg_AfterUpdateCart
ON cart
AFTER UPDATE
AS
BEGIN
    UPDATE cart
  SET cprice = p.pprice * i.quantity +
                 CASE 
                     WHEN i.version = 'fea1' THEN 50
                     WHEN i.version = 'fea2' THEN 100
                     WHEN i.version = 'fea3' THEN 200
                     ELSE 0
                 END
    FROM inserted i
    INNER JOIN products p ON i.product_id = p.product_id
    WHERE cart.user_id = i.user_id;
END;
/*SET IDENTITY_INSERT dbo.discount ON;
INSERT INTO dbo.discount (discount_id,dpercent)
VALUES (0,0)
SET IDENTITY_INSERT dbo.discount OFF;*/



SELECT * FROM dbo.users

SELECT * FROM products
SELECT * FROM orders
SELECT * FROM order_products
SELECT * FROM cart
SELECT * FROM category

CREATE TABLE che(
num int,
tim datetime
)

INSERT INTO che (num) VALUES (3);
SELECT * FROM che

