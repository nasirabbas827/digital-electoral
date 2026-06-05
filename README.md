# Digital_electoral_final

A PHP‑based web application for managing electronic elections. It provides separate interfaces for administrators and voters, allowing secure candidate registration, voter enrollment, vote casting, and result viewing.

---

## Overview

The **Digital_electoral_final** project implements a simple digital electoral system:

* **Admin panel** – manage voters, candidates, and view voting statistics.  
* **Voter portal** – register, log in, cast a vote, and view the winning candidate.  

All core functionalities are built with plain PHP, MySQL, and a lightweight CSS stylesheet.

---

## Features

| Admin | Voter |
|-------|-------|
| Secure login (`admin_login.php`) | Secure login (`login.php`) |
| Add / edit / delete voters (`add_voter.php`, `edit_voter.php`) | Register as a voter (`register.php`) |
| Add candidates (`candidate_register.php`) | Cast a vote (`voter/vote.php`) |
| View candidate list (`view_candidates.php`) | View winning candidate (`voter/view_winner.php`) |
| View casted votes (`view_casted_votes.php`) | Logout (`voter/logout.php`) |
| Export data (via MySQL) | Logout (`logout.php`) |
| Responsive navigation bars (`admin_navbar.php`, `navbar.php`) | Responsive navigation (`voter/navbar.php`) |

---

## Tech Stack

| Component | Technology |
|-----------|------------|
| Backend   | PHP 7+ |
| Database  | MySQL (schema in `Database/chairperson_db.sql`) |
| Front‑end | HTML5, CSS3 (`css/style.css`) |
| Server    | Apache / Nginx (any LAMP/LEMP stack) |

---

## Installation

1. **Clone the repository**

   ```bash
   git clone https://github.com/yourusername/Digital_electoral_final.git
   cd Digital_electoral_final
   ```

2. **Create the database**

   ```sql
   -- Using MySQL client or phpMyAdmin
   SOURCE Database/chairperson_db.sql;
   ```

3. **Configure database connection**

   - Edit `config.php` (root) and `admin/config.php` / `voter/config.php` with your DB credentials:

     ```php
     define('DB_HOST', 'localhost');
     define('DB_NAME', 'chairperson_db');
     define('DB_USER', 'YOUR_DB_USER');
     define('DB_PASS', 'YOUR_DB_PASSWORD');
     ```

4. **Set up a web server**

   - Place the project folder in your web root (e.g., `/var/www/html/Digital_electoral_final`).
   - Ensure the server has permission to read/write the files.

5. **Optional: Secure the admin area**

   - Move `admin` folder outside the public web root and adjust `include` paths, or protect it with `.htaccess`.

6. **Start the application**

   - Navigate to `http://your-domain/Digital_electoral_final/index.php`.

---

## Usage

### Administrator

1. Open `admin/admin_login.php` and log in with the admin credentials created during the DB import.
2. Use the navigation bar to:
   * **Add Voters** – `admin/add_voter.php`
   * **Edit Voters** – `admin/edit_voter.php`
   * **Add Winners (candidates)** – `admin/add_winner.php`
   * **View Voters / Candidates / Casted Votes** – respective `view_*.php` pages.
3. Log out via `admin/logout.php`.

### Voter

1. Register a new account via `register.php`.
2. Log in through `login.php`.
3. Cast a vote on `