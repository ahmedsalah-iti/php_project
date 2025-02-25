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
