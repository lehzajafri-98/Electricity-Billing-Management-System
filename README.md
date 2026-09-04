# Electricity Bill Management System (EBMS)

![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Microsoft SQL Server](https://img.shields.io/badge/MS%20SQL%20Server-2019%2B-CC292B?style=for-the-badge&logo=microsoftsqlserver&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.x-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)
![Status](https://img.shields.io/badge/Status-Production%20Ready-success?style=for-the-badge)

**Electricity Bill Management System (EBMS)** is a full-stack web application designed for power distribution companies and administrators to manage customer billing, tariff plans, region-wise analytics, and complaints. Built using a **PHP** front-end and a normalized **Microsoft SQL Server (MS SQL)** back-end utilizing triggers, views, and stored procedures.

---

## ⚡ Key Features

* **Admin Authentication:** Secure session-based authentication guarding protected management routes.
* **Automated Bill Calculation:** Uses a SQL database trigger (`trg_AfterInsert_Bill`) to automatically calculate total bill amounts (`UnitsConsumed * RatePerUnit`).
* **Real-time Input Calculation:** JavaScript integration on bill creation and edit forms to dynamically compute standard costs before saving.
* **Tariff Plan Management:** Capability to set and update dynamic per-unit rates linked to electricity distribution boards (e.g., K-Electric, LESCO).
* **Complaint Tracking:** Enables administration to capture customer grievances with automated status assignments.
* **Unified Reporting & Analytics:**
  * Displays high-bill consumers (Bills > Rs. 2000).
  * Aggregates total paid revenue collected.
  * Measures average electricity consumption grouped by geographic region via custom SQL views and join operations.

---

## 🛠️ Technology Stack

| Layer | Technology Used |
| :--- | :--- |
| **Front-End** | HTML5, CSS3, JavaScript (ES6), Bootstrap 5, Font Awesome |
| **Back-End** | PHP (with `sqlsrv` driver extensions) |
| **Database** | Microsoft SQL Server (MS SQL / SQL Express) |

---

## 🗄️ Database Schema Overview

The back-end consists of a normalized database structure:

* **Customer:** Stores personal information for energy subscribers.
* **Elec_Board:** Stores board names and respective regional locations.
* **TariffPlan:** Stores per-unit rates tied to specific electricity boards via foreign keys.
* **Customer_ElecBoard:** Mapping table resolving the many-to-many relationship between customers and electric boards.
* **Bill:** Contains consumption records, calculation details, and payment tracking dates.
* **Complaint:** Tracks support issues logged against specific customers.
* **Admin:** Stores administrator authentication credentials.

---

## ⚙️ SQL Advanced Features Implemented

* **Triggers:**
  * `trg_AfterInsert_Bill`: Executes post `INSERT`/`UPDATE` on the `Bill` table to calculate total bill amount automatically (`UnitsConsumed * RatePerUnit`).
* **Views:**
  * `vw_BillingDetails`: Combines customer information, linked electric boards, and bill amounts for fast dashboard querying without writing repetitive joins.
* **Stored Procedures:**
  * `sp_GetCustomerBills`: Returns all bill records filtering by customer name.
  * `sp_UpdateBillPaymentDate`: Updates payment status and dates for specified bills.

---

## 📁 Project Directory Structure

```text
ebms/
├── auth.php              # Access control & session validation
├── db.php                # MS SQL connection handler using sqlsrv
├── login.php             # Admin login interface
├── admin.php             # Database-driven authentication
├── logout.php            # Session termination script
├── index.php             # Main dashboard showing analytics & summary stats
├── add_bill.php          # Form to create new electricity bill records
├── edit_bill.php         # Interface to modify existing bill details
├── delete_bill.php       # Script to safely remove bills
├── tariff_plans.php      # Dynamic inline editor for unit rates
├── complaints.php        # Support ticket logging and viewing table
└── README.md             # Project documentation
```

---

## 🚀 Setup & Installation

### 1. Prerequisites
* **XAMPP / WAMP** (with PHP 7.4+ enabled)
* **Microsoft SQL Server Management Studio (SSMS)**
* **Microsoft Drivers for PHP for SQL Server** (`php_sqlsrv` extension enabled in `php.ini`)

### 2. Database Setup
1. Open SSMS and create a new database named `EBMS`.
2. Execute the database script (schema creation, triggers, views, and stored procedures) provided in your project files.

### 3. Application Setup
1. Clone or copy the project files to your web server root directory (e.g., `C:/xampp/htdocs/ebms`).
2. Open `db.php` and update your SQL server instance name:
   ```php
   $serverName = "YOUR_SERVER_NAME\SQLEXPRESS"; 
   $connectionInfo = array("Database" => "EBMS", "TrustServerCertificate" => true);
   ```
3. Run Apache via XAMPP and open `http://localhost/ebms/login.php` in your browser.
