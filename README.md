# 🔌 Laravel Plugin Installer

A modular plugin management system for Laravel that allows you to upload, install, and manage ZIP-based plugins with auto migration, composer dependency resolution, and service provider registration — all from a beautiful UI.

---

## 📦 Package Info

- **Package Name:** `sumandey8976/plugin-installer`
- **Framework:** Laravel 10+
- **PHP:** ^8.1
- **License:** MIT

---

## 🚀 Features

- Upload plugin ZIP files via UI
- Automatically extract and register plugins
- Auto-run plugin-specific migrations
- Auto-install composer dependencies declared by the plugin
- Auto-register plugin service providers
- Plugin Store UI with category badges and author info

---

## 🛠 Installation

### Step 1: Install via Composer

```bash
composer require sumandey8976/plugin-installer


📂 Access the Plugin UI

Once installed, visit the following route in your browser:

http://your-site.test/plugins
Replace your-site.test with your actual domain.

This will open the Plugin Manager UI, where you can upload and manage plugins easily.
