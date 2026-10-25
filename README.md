# DRXStore v2.0.0 — Complete Pharmacy Management System
**Developed by Vineet | psvineet@zohomail.in**

---

## 🚀 1-Click Quick Start (Windows / Mac / Linux — Zero XAMPP Needed)

### On Windows:
1. Double-click **`DRXStore.exe`** (or **`start.bat`** / **`start_silent.vbs`**).
2. The launcher will automatically:
   - Check port `5051` (if busy, it terminates the blocking process automatically).
   - Start the local server using the bundled portable PHP runtime.
   - Automatically open your default web browser to **`http://127.0.0.1:5051`**.
3. To stop the server anytime, double-click **`stop.bat`**.

> **Note for Windows clients**: Zero installation is required! Portable PHP 8.2 is bundled in the `php/` folder. The client does not need to install XAMPP, Apache, MySQL, or PHP.

### On macOS / Linux:
1. Open terminal in the project directory and run:
   ```bash
   ./start.sh
   ```
2. Automatically frees port `5051`, starts the server, and opens your browser.
3. To stop the server:
   ```bash
   ./stop.sh
   ```

---

## 🗄️ Storage Options
- **SQLite (Recommended)** — Embedded database stored in `data/drxstore.sqlite`. High-speed WAL mode, zero configuration, 100% offline and standalone.
- **JSON** — Flat-file storage, ideal for environments without database extensions.
- **MySQL / MariaDB** — For external networked database servers. Select during setup wizard or migrate via Settings.

---

## 📁 Structure
```
drxstore/
├── index.php           ← Router (all pages via ?p=pagename)
├── config/app.php      ← Bootstrap & constants
├── helpers/
│   ├── JsonDB.php      ← JSON storage engine
│   └── functions.php   ← All utility helpers
├── views/
│   ├── layout_admin.php
│   └── layout_portal.php
├── assets/css/app.css
├── assets/js/app.js
├── data/               ← Auto-created, all JSON data stored here
└── public/
    ├── admin/          ← All admin pages
    ├── supplier/       ← Supplier portal pages
    ├── customer/       ← Customer/patient portal pages
    └── setup/          ← First-launch wizard
```

---

## 📧 Contact
**Developer:** Vineet  
**Email:** psvineet@zohomail.in  

---

*DRXStore v2.0.0 — Built with ❤️ by Vineet*
