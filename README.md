# Project Overview

This project is a web application developed using **PHP**, with **MySQL** as the database and **Bootstrap** for styling. It allows users to make reservations and manage their profiles, while administrators can manage users and access all functionalities.

## Features

- **User Authentication**: Two types of user credentials:
  - **Admin**: Full access to manage users and reservations.
  - **User**: Ability to make reservations and manage personal profiles.

- **Responsive Design**: Built with Bootstrap to ensure a modern and responsive user interface.

- **Current Status**: This project is not 100% completed yet, but it is functional and showcases core features.

## Prerequisites

To run this project locally, ensure you have the following installed on your machine:

- **PHP** (version 7.0 or higher)
- **MySQL** (version 5.7 or higher)
- **Apache Server** (or any server that supports PHP)
- **Composer** (optional, if you're using any PHP dependencies)

## Installation

Follow these steps to replicate the project locally:

1. **Clone the repository**:
2. **Set up the database**:

    Create a new MySQL database named yourdbname.
    Import the SQL script provided in the project (if applicable) to set up the necessary tables.

3. **Configure Database Connection**:

    Open connection.php and update the database credentials to match your local setup:
        $servername = "localhost";
        $username = "your_db_username";
        $password = "your_db_password";
        $dbname = "yourdbname";

4. **Start the server**:

    If you're using Apache, ensure the server is running.
    Place the project folder in the htdocs directory (for XAMPP, WAMP, or similar).
    Access the project through your web browser at http://localhost/yourproject.

5. **Login Credentials**:

    Use the following credentials to log in:
        Admin:
            Email: admin@admin.com
            Password: 123123
        User:
            Email: user@user.com
            Password: 123123
