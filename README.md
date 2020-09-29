# Streaming App

This project is audio/video file upload, managament and process.

## Requirement

- Node >= 8.10
- npm >= 5.6
- mysql

## Clone

Clone this repo to your local machine using

```bash
git clone https://github.com/vyphan009/streaming.git
```

##Installation

- In the project stream-react directory, you can run:
```bash
npm install
```
```bash 
npm start
```

Open [http://localhost:3000](http://localhost:3000) on Safari to view it in the browser.


- In the project stream-php directory:
    - Run 
        ```bash
            source stream-php/sql/files.sql
        ```
    - Go to "stream-php/stream-php/lib/Database.php" to set your username, password and database for mysql
    - Follow this instruction to install PHP and Nginx [https://flaviocopes.com/php-installation-osx-nginx/](https://flaviocopes.com/php-installation-osx-nginx/).
    - Run
        ```bash
            mysql.server start
        ```

## Database Structure
```bash
+-----------+--------------+------+-----+---------+----------------+
| Field     | Type         | Null | Key | Default | Extra          |
+-----------+--------------+------+-----+---------+----------------+
| id        | int(11)      | NO   | PRI | NULL    | auto_increment |
| file_name | varchar(150) | NO   |     | NULL    |                |
| file_type | varchar(50)  | YES  |     | NULL    |                |
| file_size | int(11)      | YES  |     | NULL    |                |
| link      | varchar(255) | YES  |     | NULL    |                |
| timestamp | datetime     | YES  |     | NULL    |                |
+-----------+--------------+------+-----+---------+----------------+

```

