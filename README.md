# 💊 DRXStore — Portable Pharmacy Management System (ERP & POS)

> **Developed by:** Vineet (`psvineet@zohomail.in`)  
> **Modified & Enhanced by:** Jatin  

![Version](https://img.shields.io/badge/version-2.0.0-blue.svg)
![PHP](https://img.shields.io/badge/PHP-8.1%20%7C%208.2%2B-777bb4.svg)
![Database](https://img.shields.io/badge/database-SQLite%20%7C%20MySQL%20%7C%20JSON-orange.svg)
![Platform](https://img.shields.io/badge/platform-Windows%20%7C%20macOS%20%7C%20Linux-green.svg)
![Portability](https://img.shields.io/badge/deployment-1--Click%20Portable%20(Zero%20XAMPP)-success.svg)

---

## 📌 Overview

**DRXStore** is a comprehensive, production-ready Pharmacy Management System and Point-of-Sale (POS) ERP designed for retail chemists, medical stores, hospital pharmacies, and pharmaceutical distributors.

It features complete offline and standalone capability with zero mandatory setup—no external web server (like Apache, XAMPP, or WAMP) or database server (like MySQL) is required out of the box. Everything can run directly from any folder or USB drive.

---

## ✨ Key Features & Capabilities

- **Point-of-Sale (POS) & GST Billing:**
  - Fast barcode/name-based medicine search.
  - Multi-batch selection with live price, MRP, and stock calculation.
  - Instant GST calculations (CGST/SGST or IGST) with customizable rates.
  - Printable professional thermal & A4 invoices.
- **Batch & Expiry Management:**
  - Automated tracking of batch numbers, manufacturing dates, and expiration dates.
  - Early warning expiry alerts (expiring within 90 days, 60 days, 30 days, or already expired).
  - Segregation and stock adjustment for damaged or expired medicines.
- **Inventory & Purchase Orders (PO):**
  - Low-stock threshold alerts.
  - Supplier purchase orders, stock intake verification, and supplier ledger.
  - CSV bulk medicine import & export.
- **Multi-Role Portals:**
  - **Admin / Manager:** Full store control, financials, inventory, user accounts, and configuration.
  - **Pharmacist / Staff:** Streamlined POS billing, customer records, and order history.
  - **Supplier Portal:** Dedicated view for registered suppliers to manage POs and dispatches.
  - **Customer / Patient Portal:** Prescription requests, order history, and direct messaging.
- **Multi-Engine Database Support:**
  - **SQLite (Default & Recommended):** High-speed WAL mode, single-file database (`data/drxstore.sqlite`), completely standalone.
  - **MySQL / MariaDB:** Enterprise client-server support for multi-terminal local network deployment.
  - **JSON:** Flat-file fallback for minimal environments without database extensions.
- **Portable Cross-Platform Runtime:**
  - Native 1-click Windows executable (`DRXStore.exe`).
  - Interactive & background batch/VBS launchers for Windows.
  - Native bash scripts for macOS and Linux.
  - Automatic port clash detection and resolution.

---

## 🚀 How to Start (Detailed Instructions)

DRXStore runs on default local port **`5051`** at:
```text
http://127.0.0.1:5051
```

Choose your operating system below for detailed startup instructions.

---

### 🪟 Method 1: Windows (1-Click Run — Zero XAMPP Needed)

You do **not** need to install XAMPP, WAMP, or configure Apache. You can launch DRXStore using any of the following options:

#### Option A: Native Launcher (`DRXStore.exe`) — *Recommended*
1. Double-click **`DRXStore.exe`**.
2. The launcher will automatically:
   - Check if port `5051` is free (if occupied by an orphaned process, it terminates it cleanly).
   - Detect the bundled portable PHP runtime inside the `php/` folder (or fallback to system PHP).
   - Launch the server silently in the background with zero terminal popup.
   - Automatically open your default web browser to `http://127.0.0.1:5051`.

#### Option B: Interactive Batch Launcher (`start.bat`)
1. Double-click **`start.bat`**.
2. A command window will show startup verification checks:
   - Freeing port `5051`.
   - Locating PHP executable.
   - Launching server.
   - Opening your default browser.

#### Option C: Silent Launcher (`start_silent.vbs`)
1. Double-click **`start_silent.vbs`**.
2. Runs `start.bat` completely minimized/hidden in the background without any flashing console window.

#### 🛑 How to Stop DRXStore on Windows:
- Double-click **`stop.bat`**.
- It safely locates the running server process on port `5051`, terminates it, and frees the port.

> **💡 Note for Portable Windows Setup:**
> If you cloned the repository without the pre-packaged `php/` binary directory, simply double-click **`setup_windows_php.bat`**. It will automatically download official portable PHP 8.2 and configure `php.ini` with all required extensions in one step!

---

### 🍏 Method 2: macOS & Linux

DRXStore includes optimized shell scripts tailored for Unix environments.

#### 1. Open Terminal in the project root:
```bash
cd /path/to/drxstore
```

#### 2. Make scripts executable (one-time setup):
```bash
chmod +x start.sh stop.sh
```

#### 3. Start the application:
```bash
./start.sh
```
What `./start.sh` does:
- Checks and frees port `5051` using `lsof`.
- Detects your PHP installation (bundled or system-installed via Homebrew/APT).
- Launches the server process as a background daemon.
- Automatically opens the URL (`http://127.0.0.1:5051`) in your default browser using `open` (macOS) or `xdg-open` (Linux).
- Stores the background process ID in `data/server.pid` and logs in `data/server.log`.

#### 🛑 How to Stop the Server:
```bash
./stop.sh
```
This safely shuts down the background process and clears port `5051`.

---

### 🌐 Method 3: Standard PHP CLI or Web Server (Manual Start)

If you prefer using your existing system PHP or a development stack:

#### Using PHP Built-in Server:
```bash
php -S 127.0.0.1:5051 -t .
```
Then navigate to `http://127.0.0.1:5051` in your browser.

#### Using XAMPP / WAMP / Laragon:
1. Place the `drxstore` folder inside your web server document root (e.g., `htdocs/drxstore` or `www/drxstore`).
2. Open your browser and navigate to:
   ```text
   http://localhost/drxstore/
   ```

---

## 🛠️ First-Time Setup Wizard

When you open DRXStore for the first time, it automatically detects that configuration is needed and opens the **Setup Wizard** (`http://127.0.0.1:5051/index.php?p=setup`).

Follow the 3 simple steps:

1. **Choose Database Engine:**
   - **SQLite (Recommended):** Zero-configuration, high-performance embedded database stored in `data/drxstore.sqlite`. Perfect for single-machine stores or portable USB setups.
   - **MySQL / MariaDB:** Enter your Host (`localhost`), Port (`3306`), Database Name, Username, and Password. Ideal for multi-computer setups over a local network (LAN).
   - **JSON:** File-based storage engine for environments where database drivers are unavailable.
2. **Configure Pharmacy / Store Details:**
   - Pharmacy Name, License Number, GSTIN / Tax ID, Phone Number, Email, and Physical Address.
   - Currency symbol and default tax percentage (e.g. `18%`).
3. **Create Super-Admin Account:**
   - Set up your master administrative username, secure password, and recovery email.
4. **Complete Setup:**
   - Click **Finish Setup**. You will be redirected to the Admin Login screen.

---

## 📂 Project Directory Structure

```text
drxstore/
├── DRXStore.exe            # Windows native 1-click launcher executable (Go)
├── start.bat               # Windows interactive batch launcher
├── stop.bat                # Windows server stopper
├── start_silent.vbs        # Windows background silent launcher
├── setup_windows_php.bat   # Windows portable PHP 8.2 downloader & config script
├── start.sh                # macOS & Linux startup script
├── stop.sh                 # macOS & Linux shutdown script
├── index.php               # Front controller & central application router
│
├── launcher/               # Go source code for DRXStore.exe
│   ├── go.mod
│   └── main.go
│
├── config/
│   └── app.php             # Core application bootstrap, constants & DB loader
│
├── helpers/
│   ├── attribution.php     # System attribution & integrity protection
│   ├── functions.php       # Utility functions, sanitization, auth & session helpers
│   ├── Mailer.php          # SMTP email delivery helper
│   ├── SQLiteDB.php        # SQLite database wrapper
│   ├── MySQLDB.php         # MySQL database wrapper
│   └── JsonDB.php          # JSON flat-file storage engine
│
├── views/
│   ├── layout_admin.php    # Admin & staff portal master layout
│   └── layout_portal.php   # Customer & supplier portal master layout
│
├── public/
│   ├── admin/              # POS, Inventory, Batches, Invoices, Reports, Settings
│   ├── customer/           # Customer portal (orders, prescriptions, messages)
│   ├── supplier/           # Supplier portal (purchase orders, dispatches)
│   └── setup/              # First-run setup wizard
│
├── assets/
│   ├── css/                # Application stylesheets
│   └── js/                 # POS calculation, AJAX search & interaction scripts
│
├── data/                   # (Auto-created) Application runtime storage
│   ├── drxstore.sqlite     # SQLite database file
│   ├── db_config.json      # Database connection configuration
│   ├── server.pid          # Active background process ID
│   └── server.log          # Real-time PHP server output & error log
│
└── php/                    # Bundled portable PHP runtime (for Windows standalone mode)
```

---

## 🔧 Configuration & Customization

### Default Credentials & Port:
- **Default Port:** `5051` (Configured in `start.bat`, `start.sh`, and `launcher/main.go`).
- **Data Location:** All dynamic records, database files, and logs reside inside the `data/` folder for instant zero-loss backups.

### How to Change the Port:
If port `5051` is needed for another service:
- In `start.bat` & `stop.bat`: Update `set PORT=5051` to your desired port.
- In `start.sh` & `stop.sh`: Update `PORT=5051`.
- In `launcher/main.go`: Change `port = 5051` and recompile using `go build -o ../DRXStore.exe main.go`.

---

## 🗄️ Backup & Restore

Backing up your entire pharmacy database is as simple as copying a folder:
1. **Full Backup:** Simply copy or zip the `data/` folder. It holds all database tables, invoices, configurations, and user accounts.
2. **In-App Backup:** Navigate to **Admin Dashboard &rarr; Settings &rarr; Backup & Restore** to download automated database dumps anytime.

---

## ❓ Troubleshooting & FAQs

### 1. Port 5051 is already in use?
The launchers (`DRXStore.exe`, `start.bat`, and `start.sh`) have built-in auto-cleanup mechanisms that detect and terminate stale server processes before binding. If you need to stop it manually:
- Windows: Run `stop.bat`
- macOS/Linux: Run `./stop.sh`

### 2. "PHP runtime not found" error on Windows?
Run `setup_windows_php.bat`. It will fetch a standalone, official PHP 8.2 zip package and configure it automatically in the `php/` folder.

### 3. Permission denied on macOS/Linux?
Ensure that script execution and data folder write permissions are granted:
```bash
chmod +x start.sh stop.sh
chmod -R 775 data
```

### 4. How can I inspect server logs?
All runtime errors and server activity are written to:
```text
data/server.log
```

---

## 👨‍💻 Credits & Attribution

- **Original Developer:** **Vineet** (`psvineet@zohomail.in`)
- **Modified & Enhanced by:** **Jatin**
  - Designed the 1-click standalone portable launcher architecture.
  - Developed `DRXStore.exe` (Go-based native background daemon).
  - Built portable cross-platform launch scripts (`start.bat`, `stop.bat`, `start_silent.vbs`, `start.sh`, `stop.sh`).
  - Added automated port-conflict resolution and portable PHP automation (`setup_windows_php.bat`).

---

<p align="center">
  <sub>DRXStore v2.0.0 &bull; Built with dedication for modern pharmacy workflows.</sub>
</p>
