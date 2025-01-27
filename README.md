# Project Setup Guide

## Prerequisites
Ensure you have the following installed on your system:
- **Git**
- **Node.js** and **npm** (Node Package Manager) or **Yarn**
- **PHP** >= 7.4
- **Node.js** >= 12.x

## Backend Setup

### Clone the Repository
```sh
git clone https://github.com/chanakanaveen/Freelance-Technicians-Marketplace
#Open the Project in VS Code.

#Run XAMPP Apache and MySQL Server
#Install PHP Dependencies using Composer

composer install

# Copy the .env.example file to .env and configure your environment variables:
 cp .env.example .env

# Generate the Application Key:
 php artisan key:generate

# Create a Database:
Create a database named techconnect.
# Update your .env file to use the new database:
DB_DATABASE='techconnect'

# Import the Database:
# In the project directory, locate the sql folder.
# Use phpMyAdmin to import the SQL file into the techconnect database.

# Start the Development Server:
 php artisan serve

# Open your Browser and Navigate to:
 http://localhost:8000

Admin username : admin@mail.com
Admin password : 123123

