ALTER TABLE Cart
    ALTER COLUMN quantity int default 1,
    ALTER COLUMN price      decimal(12, 2) default 999999.99,
    ALTER COLUMN modified   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP on update current_timestamp,
    ALTER COLUMN created    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ALTER COLUMN foreign key (product_id) references Products (id) on delete cascade,
    ALTER COLUMN foreign key (user_id) references Users (id) on delete cascade;