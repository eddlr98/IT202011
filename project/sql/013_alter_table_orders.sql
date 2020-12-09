ALTER TABLE Orders
    ADD COLUMN address varchar(80) NOT NULL,
    ADD COLUMN payment_method varchar(20) NOT NULL,
    DROP COLUMN price,
    ADD COLUMN total_price decimal(10,2),
    DROP COLUMN product_id,
    DROP COLUMN quantity;
    

