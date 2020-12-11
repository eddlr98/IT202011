CREATE TABLE IF NOT EXISTS OrderItems
(
    id            int auto_increment not null,
    order_id      int,
    product_id    int,
    quantity int,   
    unit_price decimal(12, 2),
    PRIMARY KEY (id),
    FOREIGN KEY (product_id) REFERENCES Products(id),
    FOREIGN KEY (order_id) REFERENCES Orders(id)
)