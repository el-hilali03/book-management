# 📚 Book Management System

A web-based Book Management System developed using PHP, MySQL, HTML, CSS and JavaScript.

## 🚀 Features

### 👤 Authentication
- User registration
- User login
- User logout
- Password hashing
- Role-based access

### 📚 Books
- Display all books
- Search books
- View book details
- Add books
- Edit books
- Delete books
- Upload book covers

### ❤️ Favorites
- Add books to favorites
- Remove books from favorites
- View favorite books

### 👨‍💼 Admin Dashboard
- View statistics
- Manage books
- Manage users
- Change user roles

### 👤 User Dashboard
- View account information
- View favorite books
- Edit profile

## 🛠️ Technologies

- PHP
- MySQL
- HTML5
- CSS3
- JavaScript
- XAMPP
- Git
- GitHub

## 📁 Project Structure

```text
book-management/
│
├── config/
│   └── database.php
│
├── assets/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── script.js
│   └── images/
│
├── auth/
│   ├── login.php
│   ├── register.php
│   └── logout.php
│
├── user/
│   ├── dashboard.php
│   ├── favorites.php
│   └── profile.php
│
├── admin/
│   ├── dashboard.php
│   ├── add-book.php
│   ├── edit-book.php
│   ├── delete-book.php
│   └── users.php
│
├── books/
│   ├── index.php
│   └── details.php
│
├── index.php
└── README.md
```

## 🗄️ Database

The project uses MySQL with three main tables:

- `users`
- `books`
- `favorites`

Database name:

```text
book_management
```

## ⚙️ Installation

### 1. Install XAMPP

Install XAMPP and start:

- Apache
- MySQL

### 2. Copy the project

Place the project inside:

```text
C:\xampp\htdocs\
```

Example:

```text
C:\xampp\htdocs\book-management\
```

### 3. Create the database

Open:

```text
http://localhost/phpmyadmin
```

Create a database called:

```text
book_management
```

Then create the required tables.

### 4. Configure the database

Open:

```text
config/database.php
```

Make sure the database configuration matches your MySQL setup.

### 5. Run the project

Open:

```text
http://localhost/book-management/
```

## 👨‍💻 Author

Ismail El hilali

## 📌 Project Purpose

This project was created as a portfolio project to demonstrate practical skills in:

- PHP development
- MySQL databases
- CRUD operations
- Authentication
- Session management
- File uploads
- JavaScript
- Responsive web design
