
# EzTickEntry

Welcome to EzTickEntry, a dynamic web application designed for event management, specifically focusing on ticket sales and attendee management for live performances and festivals and cinema events.

## Features

- **User Authentication:** Secure login and registration system.
- **Event Management:** Create, update, and cancel events.
- **Ticket Sales:** Manage ticket sales with real-time updates on availability.
- **Venue Management:** Add and manage event venues, including capacity and location.
- **Real-Time Data:** Dashboard for real-time monitoring of ticket sales and event status.
- **Reporting:** Generate comprehensive reports on sales, attendance, and customer demographics.

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes.

### Prerequisites

What things you need to install the software and how to install them:

```bash
- PHP 7.4 or newer
- MySQL 5.7 or newer
- Apache or Nginx server
```

### Installing

A step-by-step series of examples that tell you how to get a development environment running:

1. **Clone the repository:**

```bash
git clone https://github.com/yourusername/EzTickEntry.git
```

2. **Navigate to the project directory:**

```bash
cd EzTickEntry
```

4. **Configure your environment:**

Copy the `settings/connection.example.php` to `settings/connection.php` and adjust the database settings.

```php
// settings/connection.php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'your_username');
define('DB_PASSWORD', 'your_password');
define('DB_DATABASE', 'eztickentry');
```

5. **Initialize the database:**

Import the `eztickentry.sql` file into your MySQL database. This file contains the necessary database schema and initial data.

## Author

- **Leslie Konlack** -


