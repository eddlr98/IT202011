CREATE TABLE Ratings(
    id            int auto_increment not null,
    product_id    int,
    user_id       int,
    rating        int,
    comment       varchar(120),
    created       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (product_id) REFERENCES Products(id),
    FOREIGN KEY (user_id)) REFERENCES Users(id);
)