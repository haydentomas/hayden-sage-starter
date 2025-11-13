# Hayden Tomas – Custom Sage Starter Theme

This is my custom WordPress theme built using **Sage (Roots)**, **Laravel Blade**, **Vite**, and **Tailwind CSS**.  
It’s designed to be a clean starting point for professional WordPress development with modern tooling, hot-reload, Blade templating, and a fully extendable structure.

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

If Vite connects, you’ll see:

```
✓ Vite connected
✓ Hot reload active
```

If not, reload the browser — HMR still works even without the console message.

---

## 🛠️ Build for Production

```bash
npm run build
```

This outputs files into:

```
public/build/
```

*(This folder is ignored by Git.)*

---

## 🔄 Typical Workflow

1. Clone theme or pull updates  
2. Run `composer install`  
3. Run `npm install`  
4. Run `npm run dev` for hot reload  
5. Develop normally (Blade, Tailwind, PHP templates)  
6. Push changes to GitHub  
7. Deploy using `npm run build` + normal WordPress deployment

---

## 📁 File Structure Overview

```
haydentomas/
│
├── app/                 # Sage controllers, config, setup
├── resources/
│   ├── css/             # Tailwind, app.css, editor.css
│   ├── js/              # JavaScript
│   ├── views/           # Blade templates (layouts, partials, single, etc.)
│   └── images/
│
├── public/build/        # Vite compiled assets (ignored in Git)
├── .env                 # Local dev URL (ignored in Git)
├── composer.json
├── package.json
├── vite.config.js
└── functions.php
```

---

## 🧹 Git Ignore Rules

The repo excludes:

```
vendor/
node_modules/
public/build/
.vite/
.env
```

This ensures the repo stays clean and installable.

---

## 📦 PHP Installation Note (Important)

On any new machine, install the exact PHP build I use:

**php-8.2.29-nts-Win32-vs16-x64.zip**

1. Extract to: `C:\php\`
2. Copy over your existing `php.ini` (or configure a new one)
3. Add `C:\php\` to PATH
4. Ensure these extensions are enabled:
   - `extension=fileinfo`
   - `extension=openssl`
   - `extension=mbstring`
   - `extension=exif`

This ensures Sage, Composer, and Acorn run perfectly.

---

## 📬 Author

**Hayden Tomas**  
Modern WordPress & Laravel-based theme development with Tailwind, Blade, and AI-assisted tooling.

---

## 📝 License

MIT — feel free to extend or fork this starter theme for your own projects.
