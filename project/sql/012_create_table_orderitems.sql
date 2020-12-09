CREATE TABLE IF NOT EXISTS OrderItems
(
    id            int auto_increment not null,
    order_id      int,
    product_id    int,
    quantity int,   
    unit_price decimal(12, 2),
    created    timestamp default current_timestamp,
    user_id int,
    PRIMARY KEY (id),
    FOREIGN KEY (product_id) REFERENCES Products(id),
    FOREIGN KEY (user_id) REFERENCES Users(id),
    FOREIGN KEY (order_id) REFERENCES Orders(id)
)