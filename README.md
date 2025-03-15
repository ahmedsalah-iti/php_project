# PHP Cafeteria Project  

**Note:** The frontend template is not made by me; I only developed the PHP backend.  

## Setting Up the Database  

### 1. Download the Database Files  
Download all `.sql` files from:  
[GitHub Repository - db_project_improved](https://github.com/ahmedsalah-iti/db_project_improved)  

### 2. Import the Database  
1. Open your MySQL Server.  
2. Run the following command (make sure to update the file path `/path/to/`):  

   ```sql
   SOURCE /path/to/db_project_improved/create_tables.sql;
   SOURCE /path/to/db_project_improved/create_functions.sql;
   SOURCE /path/to/db_project_improved/create_triggers_views.sql;

   CREATE TABLE access_tokens (
       id BIGINT AUTO_INCREMENT PRIMARY KEY,
       user_id INT NOT NULL,
       token VARCHAR(255) UNIQUE NOT NULL,
       expiry DATETIME NOT NULL,
       FOREIGN KEY (user_id) REFERENCES User(id)
   );
   DROP FUNCTION IF EXISTS addUserBalance;
   DELIMITER //
   create function addUserBalance(userID int, addAmount decimal(10,2)) RETURNS INT
   DETERMINISTIC
   BEGIN
    DECLARE tid INT;
    DECLARE currentUserBalance decimal(10,2);
    DECLARE newUserBalance decimal(10,2);
    DECLARE trStatus VARCHAR(10);
    -- SET tid = -1;
    SET trStatus = 'completed';
    if NOT isUserIdExists(userID) THEN
        SET trStatus = 'failed';
        SET tid = 0;
        RETURN tid;
    END if;
    if addAmount <= 0 THEN
        SET trStatus = 'failed';
        SET tid = 0;
    end if;

    SET currentUserBalance = getUserBalance(userID);
    SET newUserBalance = currentUserBalance + addAmount;

    INSERT INTO Wallet_Transaction ( 
        type,
        amount,
        balance_before,
        balance_after,
        user_id,
        status
     ) VALUES(
         'add',
         addAmount,
         currentUserBalance,
         newUserBalance,
         userID,
         trStatus
    );

    SET tid = LAST_INSERT_ID();

    IF getUserBalance2(userID) = newUserBalance AND trStatus = 'completed' THEN 
        RETURN tid;
    else
        RETURN 0;
    end if;
    END //
    DELIMITER ;


    DROP FUNCTION IF EXISTS subUserBalance;


    DELIMITER //
    create function subUserBalance(userID int , subAmount decimal(10,2)) RETURNS INT
    DETERMINISTIC
    BEGIN
        DECLARE tid INT;
        DECLARE currentUserBalance decimal(10,2);
        DECLARE newUserBalance decimal(10,2);
        DECLARE trStatus VARCHAR(10);
        -- SET tid = -1;
        SET trStatus = 'completed';
        if NOT isUserIdExists(userID) THEN
            SET trStatus = 'failed';
            SET tid = 0;
            RETURN tid;
        END if;
        SET currentUserBalance = getUserBalance(userID);
        SET newUserBalance = currentUserBalance - subAmount;
        if subAmount <= 0.00 OR newUserBalance < 0.00 THEN
            SET trStatus = 'failed';
            SET tid = 0;
        end if;

        INSERT INTO Wallet_Transaction ( 
        type,
        amount,
        balance_before,
        balance_after,
        user_id,
        status
     ) VALUES(
         'sub',
         subAmount,
         currentUserBalance,
         newUserBalance,
         userID,
         trStatus
    );

    SET tid = LAST_INSERT_ID();

    IF getUserBalance(userID) = newUserBalance AND trStatus = 'completed' THEN
        RETURN tid;
    else
        RETURN 0;
    end if;
    END //
    DELIMITER ;
