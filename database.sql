CREATE TABLE cart (
  user_id int,
  product_id int,
  color varchar(20) DEFAULT NULL,
  version varchar(20) DEFAULT NULL,
  quantity int DEFAULT NULL,
  cprice int NOT NULL DEFAULT 0
);
drop table admin;
CREATE TABLE admin(
	sold int DEFAULT 0 NOT NULL ,
	total_sold int DEFAULT 0 NOT NULL
)
CREATE TABLE orders (
  order_id int PRIMARY KEY IDENTITY(1,1),
  user_id int DEFAULT NULL,
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
  pdate datetime DEFAULT NULL,
  sold int DEFAULT 0
);



CREATE TABLE category(
  cat_id int IDENTITY(1,1),
  cat_name varchar(30),
  PRIMARY KEY (cat_id)
);



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


CREATE TABLE product_detail(
	product_id int NOT NULL,
	color varchar(20),
	PRIMARY KEY (product_id,color)
)


drop table user_statistic

CREATE TABLE user_statistic(
	user_id int PRIMARY KEY,
	bought int DEFAULT 0 NOT NULL,
	total int DEFAULT 0 NOT NULL,
)
INSERT INTO user_statistic values (2,0,0)



SET IDENTITY_INSERT users ON
INSERT INTO users (user_id, fname, lname, phone, email, avatar, avatar_type, address, type, password)
VALUES (2, 'Admin', 'Admin', '', 'admin', NULL, NULL, NULL, 1, 'password');
SET IDENTITY_INSERT users OFF


-- Create a function to calculate price
DROP FUNCTION CalculatePrice
CREATE FUNCTION CalculatePrice (@pprice DECIMAL, @quantity INT, @version VARCHAR(10),@discount INT)
RETURNS DECIMAL
AS
BEGIN
    DECLARE @cprice DECIMAL;

    SET @cprice = @pprice * @quantity + @quantity*
        CASE 
            WHEN @version = 'fea1' THEN 50
            WHEN @version = 'fea2' THEN 100
            WHEN @version = 'fea3' THEN 200
            ELSE 0
        END;
	SET @cprice = @cprice - (@cprice * @discount / 100);

    RETURN @cprice;
END;


-- Trigger for AFTER INSERT on cart table
	CREATE TRIGGER trg_AfterInsertCart
	ON cart
	AFTER INSERT
	AS
	BEGIN
		UPDATE c
		SET c.cprice = dbo.CalculatePrice
		(p.pprice, i.quantity, i.version,p.discount)
		
		FROM inserted i
		JOIN cart c ON c.user_id = i.user_id
		JOIN products p ON i.product_id = p.product_id
		
		WHERE c.cart_id = i.cart_id;
	END;

-- Trigger for AFTER UPDATE on cart table
CREATE TRIGGER trg_AfterUpdateCart
ON cart
AFTER UPDATE
AS
BEGIN
    UPDATE c
    SET c.cprice = dbo.CalculatePrice(p.pprice, i.quantity, i.version,p.discount)
    FROM inserted i
    INNER JOIN cart c ON c.user_id = i.user_id
    INNER JOIN products p ON i.product_id = p.product_id
    WHERE c.cart_id = i.cart_id;
END;

-- Trigger to ENFORCE QUANTITY on cart table
CREATE TRIGGER trg_EnforceQuantity
ON cart
AFTER INSERT, UPDATE
AS
BEGIN
    IF EXISTS (
        SELECT i.quantity, p.pstock
        FROM inserted i
        INNER JOIN products p ON i.product_id = p.product_id
        WHERE i.quantity > p.pstock
    )
    BEGIN
        ROLLBACK TRANSACTION;
        RETURN;
    END;
END;

-- Trigger to UPDATE THE SOLD on products table
CREATE TRIGGER trg_totalsold
ON orders
AFTER UPDATE
AS
BEGIN
    DECLARE @status varchar(20);
    SELECT @status = order_status FROM inserted;

    IF (@status = 'ARCHIVED')
    BEGIN
        UPDATE p
        SET p.sold = p.sold + op.quantity_sold
        FROM products p
        JOIN inserted i ON p.product_id = i.product_id
        JOIN order_products op ON i.order_id = op.order_id
        WHERE p.product_id = i.product_id;
    END;
END;

DROP TRIGGER trg_user;
CREATE TRIGGER trg_user
ON orders
AFTER UPDATE
AS
BEGIN
    DECLARE @status varchar(20), @add INT, @order_id INT,@user_id int;
	DECLARE @cost int ;
    SELECT @status = order_status FROM inserted;
	SELECT @order_id = order_id FROM inserted;
	SELECT @user_id = user_id FROM inserted;
	SELECT @cost = order_cost FROM inserted;
    SELECT @add = sum(quantity) FROM order_products WHERE order_id = @order_id

    IF (@status = 'ARCHIVED')
    BEGIN
        UPDATE user_statistic
        SET bought = bought + @add
		WHERE user_id = @user_id
		UPDATE user_statistic
		SET total = total + @cost
		WHERE user_id = @user_id
		UPDATE admin 
		SET sold = sold + @cost , total_sold = total_sold + @add
    END;
END;
select * from orders
select * from admin
select * from order_products
SELECT * FROM dbo.users 
SELECT * FROM dbo.user_statistic
INSERT INTO user_statistic VALUES(3,0,0)
INSERT INTO admin VALUES(0,0)
UPDATE user_statistic 
SET bought = 0 , total = 0 
WHERE user_id = 3
UPDATE orders SET order_status = 'ARCHIVED' where ORDER_ID = 11






SELECT * FROM products

SELECT * FROM orders ;
SELECT * FROM order_products
SELECT * FROM cart
SELECT * FROM category


CREATE TABLE che(
num int,
tim datetime
)

UPDATE products 
SET cat_id = 1


INSERT INTO che VALUES (3,GETDATE());

SELECT * from che
DELETE FROM che

INSERT INTO products (pname, pdesc, pprice,pimage,pimagetype, pdate,discount,pstock,cat_id) VALUES 
                ('Product 5','EXPERIMENTAL PRODUCT 5',100,'imageproduct/lamb3.jpg','jpg', GETDATE(),0, 50,0);