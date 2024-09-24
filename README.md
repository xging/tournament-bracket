# How to run application

Symfony PHP + MySQL Tournament Bracket

## Getting started

## MySQL
### 1. Create new database:
  ### `CREATE DATABASE your_db_name;`
### 2. Grant privileges to your db user:
  ### `GRANT ALL PRIVILEGES ON your_db_name.* TO 'your_mysql_user'@'localhost';`

## Symfony
### Dependecies
### 1. Install Dependencies:
 - Navigate to your Symfony project directory and run the command to install all necessary dependencies:
### `composer install`
### 1.1 Update DB credentials:
- Navigate to .env file in your Symfony project folder, and update DATABASE_URL:
  ### `DATABASE_URL="mysql://your_db_user:your_db_pass@127.0.0.1:3306/your_db_name"`
### 2. Start the Symfony Server:
  - Start the Symfony built-in server to ensure everything is working correctly:
### `symfony server:start`
### 3. Start the MySQL Server:
### `mysqld --console`
### 4. Check MySQL Connection:
### `mysql -u root -p`
### 5. Run Migrations and Doctrine Setup:
### `php bin/console make:migration`
### `php bin/console doctrine:migrations:migrate`


## How to test:
### Run APIs from Symfony:
## Division Matches
### API Parameters:

api/generate-match-data/numberOfDivisions/teamType/matchType/divisionId

### - numberOfDivisions: 1-8
-- (How many divisions are going to participate)

### - teamType: 'random' or 'list'
-- (Random generated teams or predifined teams in json format file)

### - matchType: 'single' or 'all'
-- (Generate one match at a time in selected division or generate all matches in all divisions)

### - divisionId: 1-8
-- (Select the division ID for single matches in which the match will be generated.) 
['numberOfDivisions' => 2, 'teamType' => 'random', 'matchType' => 'all', 'divisionId' => 'all']

### Run API:
### Default: localhost:8000/api/generate-match-data
-- (By default will be used 'numberOfDivisions' => 2, 'teamType' => 'random', 'matchType' => 'all', 'divisionId' => 'all' parameters.)

### Generate four divisions, random generated teams, all matches generated:
localhost:8000/api/generate-match-data/4/random/all

### Generate one single division, listed teams, single match, all divisions.
localhost:8000/api/generate-match-data/1/list/single/all








