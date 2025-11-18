# Hayden Tomas – Custom Sage Starter Theme

This is my custom WordPress theme built using **Sage (Roots)**, **Laravel Blade**, **Vite**, and **Tailwind CSS**.  
It’s designed to be a clean starting point for professional WordPress development with modern tooling, hot reload, Blade templating, and a fully extendable structure.

This project serves as my personal Sage starter that I can clone on any machine — including my main PC and home setup — and rebuild with Composer and NPM.

---

## 🚀 Features

- **Sage (Roots) dev-main**
- **Laravel Blade templating**
- **Vite 7 with hot reload**
- **Tailwind CSS pre-configured**
- **Blade components, layouts, controllers**
- Clean folder structure for scalable WordPress development
- Custom `.gitignore` to keep repo clean  
  (`vendor/`, `node_modules/`, `.env`, `.vite`, public build)

---

## 🖥️ Requirements

Ensure the following are installed:

- **PHP 8.2+ (Non-thread-safe NTS build)**  
  ✔️ *I use:* `php-8.2.29-nts-Win32-vs16-x64.zip`  
  > I keep a copy of this ZIP so I can re-create the PHP installation at home.  
  Extract it to `C:\php\`, add to PATH, and copy in your `php.ini`.

- **Composer 2**  
  Composer 1 is deprecated and will throw warnings.

- **Node.js 18+ / NPM 9+**

- **Working local environment**  
  e.g. Laragon, LocalWP, DDEV, XAMPP, or custom Bedrock-style setup.

---

## 📦 Installation (New Machine Setup)

Clone the theme into your WordPress installation:

```bash
cd wp-content/themes
git clone https://github.com/YOUR_USERNAME/hayden-sage-starter.git haydentomas
cd haydentomas
```

### 1. Install PHP dependencies:

```bash
composer install
```

### 2. Install Node dependencies:

```bash
npm install
```

### 3. Create your `.env` file:

```bash
APP_URL=http://your-local-domain.test
```

Example:  
`APP_URL=http://sage.local`

### 4. Start Vite (development mode):

```bash
npm run dev
```

---

## 🛠️ Build for Production

```bash
npm run build
```

This outputs files into:

```
public/build/
```

---

## 🔄 Typical Workflow

1. Clone theme or pull updates  
2. Run `composer install`  
3. Run `npm install`  
4. Run `npm run dev`  
5. Develop normally  
6. Push changes  
7. Deploy using production build instructions below

---

## 🚀 Deployment Guide

This is the **exact deployment process** for taking this Sage theme from local → live server.

### 1. Build production assets

```bash
npm run build
```

### 2. Install production PHP dependencies

```bash
composer install --no-dev --optimize-autoloader
```

### 3. (Optional) Remove node_modules

```powershell
Remove-Item -Recurse -Force node_modules
```

### 4. Zip the theme folder

Include:

- `app/`  
- `resources/`  
- `public/`  
- `vendor/`  
- PHP files  
- composer.json / composer.lock  

Exclude:

- node_modules  
- .git  
- .env  
- .vite  

### 5. Upload to server

Upload the ZIP to:

```
wp-content/themes/
```

Extract it, then activate the theme in WordPress admin.

### 6. Finalise

Visit:

**Settings → Permalinks → Save**

---

## 📦 Optional: Full Site Migration (All-in-One WP Migration)

Use AIO Migration only for:

- Database  
- Uploads  
- ACF data  

Upload the theme separately to avoid overwriting it.

---

## 🧹 Git Ignore Rules

```
vendor/
node_modules/
public/build/
.vite/
.env
```

---

## 📬 Author

**Hayden Tomas**

---

## 📝 License

MIT
