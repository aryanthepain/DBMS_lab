-- admin tables
CREATE TABLE
    department (
        DID INT PRIMARY key auto_increment NOT NULL,
        name VARCHAR(25) NOT NULL
    );

CREATE TABLE
    admins (
        AID INT PRIMARY key auto_increment NOT NULL,
        DID INT,
        name VARCHAR(25) NOT NULL,
        password VARCHAR(20) NOT NULL,
        FOREIGN key (DID) REFERENCES department (DID) ON DELETE SET NULL
    );

-- rooms
CREATE TABLE
    category (
        CID INT PRIMARY key auto_increment NOT NULL,
        name VARCHAR(25) NOT NULL,
        tariff INT NOT NULL,
        points INT NOT NULL
    );

CREATE TABLE
    rooms (
        room_no INT PRIMARY key auto_increment NOT NULL,
        CID INT NOT NULL,
        FOREIGN key (CID) REFERENCES category (CID)
    );

-- customers
CREATE TABLE
    customers (
        phone_no BIGINT PRIMARY key auto_increment NOT NULL,
        name VARCHAR(25) NOT NULL
    );

-- reservation
CREATE TABLE
    reservations (
        phone_no BIGINT NOT NULL,
        room_no INT,
        checkin DATE NOT NULL,
        checkout DATE NOT NULL,
        isStaying BOOLEAN DEFAULT FALSE,
        points INT NOT NULL,
        FOREIGN key (phone_no) REFERENCES customers (phone_no) ON DELETE CASCADE,
        FOREIGN key (room_no) REFERENCES rooms (room_no) ON DELETE SET NULL
    );

-- amenities
CREATE TABLE
    amenities (
        AID INT PRIMARY key auto_increment NOT NULL,
        name VARCHAR(25) NOT NULL,
        points_req INT NOT NULL
    );

-- food
CREATE TABLE
    foods (
        FID INT PRIMARY key auto_increment NOT NULL,
        name VARCHAR(25) NOT NULL,
        price INT NOT NULL
    );

-- orders
CREATE TABLE
    orders (
        phone_no BIGINT NOT NULL,
        room_no INT,
        FID INT NOT NULL,
        order_date DATE NOT NULL,
        FOREIGN key (phone_no) REFERENCES customers (phone_no) ON DELETE CASCADE,
        FOREIGN key (room_no) REFERENCES rooms (room_no) ON DELETE SET NULL,
        FOREIGN key (FID) REFERENCES foods (FID) ON DELETE CASCADE
    );

-- procedures
DELIMITER /
-- Procedure to get dept
CREATE PROCEDURE getDepartment (IN inputID INT) BEGIN
-- returning DID
SELECT
    DID
FROM
    admins
WHERE
    AID = inputID;

END / DELIMITER;