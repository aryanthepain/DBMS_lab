-- Insert dummy data into department
INSERT INTO
    department (name)
VALUES
    ('HR'),
    ('Finance'),
    ('IT'),
    ('Maintenance');

-- Insert dummy data into admins
INSERT INTO
    admins (DID, name, password)
VALUES
    (1, 'Alice', 'secret1'),
    (2, 'Bob', 'secret2'),
    (3, 'Charlie', 'secret3');

-- Insert dummy data into category (room categories)
INSERT INTO
    category (name, tariff, points)
VALUES
    ('Deluxe', 200, 100),
    ('Standard', 100, 50),
    ('Suite', 300, 150);

-- Insert dummy data into rooms
-- (Assuming room_no is auto-incremented)
INSERT INTO
    rooms (CID)
VALUES
    (1), -- Room of type Deluxe
    (1), -- Another Deluxe room
    (2), -- Standard room
    (3);

-- Suite
-- Insert dummy data into customers
-- (phone_no is auto-incremented in this design)
INSERT INTO
    customers (name)
VALUES
    ('John Doe'),
    ('Jane Smith'),
    ('Bob Johnson');

-- Insert dummy data into reservations
-- Note: Adjust the dates and points as needed.
INSERT INTO
    reservations (
        phone_no,
        room_no,
        checkin,
        checkout,
        isStaying,
        points
    )
VALUES
    (1, 1, '2024-05-01', '2024-05-05', TRUE, 50),
    (2, 2, '2024-06-10', '2024-06-15', FALSE, 30);

-- Insert dummy data into amenities
INSERT INTO
    amenities (name, points_req)
VALUES
    ('WiFi', 10),
    ('Breakfast', 20),
    ('Gym', 15);

-- Insert dummy data into foods
INSERT INTO
    foods (name, price)
VALUES
    ('Burger', 5),
    ('Pizza', 8),
    ('Salad', 4);

-- Insert dummy data into orders
INSERT INTO
    orders (phone_no, room_no, FID, order_date)
VALUES
    (1, 1, 1, '2024-05-02'),
    (2, 2, 2, '2024-06-11');