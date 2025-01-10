# DBMS lab-1

1. Initialise the table in database named `lab1_student` using `initialiseDB.sql` in root folder

```sql
CREATE TABLE
    students (
        Roll_number BIGINT PRIMARY key auto_increment NOT NULL,
        First_name VARCHAR(25) NOT NULL,
        Last_name VARCHAR(25) NOT NULL,
        DOB DATE NOT NULL,
        branch VARCHAR(10) NOT NULL,
        Phone_no INT NOT NULL,
        Hostel VARCHAR(50) NOT NULL,
        GPA FLOAT NOT NULL
    );
```

2. The program will run freely
3. In case of connection error, make changes in file: `include/dbh.inc.php`

```php
$dsn = "mysql:host=localhost;dbname=lab1_student";
```
