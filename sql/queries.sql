-- create reservation
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
    (3, 3, '2025-03-01', '2025-03-05', TRUE, 70);

-- register a customer
INSERT INTO
    customers (phone_no, name)
VALUES
    (phone_no, 'New Customer');

-- delete old reservations
DELETE FROM reservations
WHERE
    checkin < DATE_SUB (CURDATE (), INTERVAL 1 YEAR);

-- automatic
SET
    GLOBAL event_scheduler = ON;

CREATE EVENT delete_old_reservations ON SCHEDULE EVERY 1 DAY STARTS CURRENT_TIMESTAMP DO
DELETE FROM reservations
WHERE
    checkin < DATE_SUB (CURDATE (), INTERVAL 1 YEAR);