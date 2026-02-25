# VincenThinks

VincenThinks is a collaborative, academic Q&A platform built with Laravel. Designed for students and educators, it provides a safe, interactive environment to ask questions, share knowledge, and find solutions. 

A standout feature of VincenThinks is its **Automated AI Content Moderation**, which utilizes Google's Gemini AI to scan both text and images for unsafe content, ensuring the community remains focused and respectful.

## 🚀 Key Features

* **Interactive Q&A Forum:** Ask questions, post answers, and engage in threaded replies.
* **Reputation & Gamification:** Upvote/rate helpful answers and allow authors to mark the "Best Answer."
* **AI-Powered Safety Moderation:** Integrates with the Gemini API to automatically queue and scan new questions, answers, and images for inappropriate content before they are published.
* **Role-Based Access Control:** Distinct roles for Students, Teachers, and Administrators.
* **Department & Course Filtering:** Categorize and filter questions by academic departments and specific courses.
* **Admin Dashboard:** Comprehensive tools for managing users, tracking analytics, reviewing reported content, and managing banned words.

## 🛠️ Tech Stack

* **Backend:** PHP 8.2+, Laravel 12.x
* **Frontend:** Blade Templates, Tailwind CSS, Vite
* **Database:** MySQL / SQLite
* **AI Integration:** Google Gemini API (gemini-2.5-flash)

## 📋 Prerequisites

Before you begin, ensure you have the following installed on your local machine:
* PHP >= 8.2
* Composer
* Node.js & npm
* A supported database (MySQL, PostgreSQL, or SQLite)

## ⚙️ Installation & Setup

**1. Clone the repository**
```bash
git clone [https://github.com/your-username/vincenthinks.git](https://github.com/your-username/vincenthinks.git)
cd vincenthinks
```

**2. Install dependencies**
```bash
composer install
npm install
```

**3. Environment Configuration**
Copy the example environment file and generate an application key:
```bash
cp .env.example .env
php artisan key:generate
```

**4. Configure your `.env` file**
Open the `.env` file and set up your database connection. **Crucially, you must add your Gemini API key for the moderation queue to function:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=vincenthinks
DB_USERNAME=root
DB_PASSWORD=

# AI Moderation Configuration
GEMINI_API_KEY=your_gemini_api_key_here
```

**5. Run Migrations and Seeders**
Set up your database tables and populate them with initial data (Categories, Courses, etc.):
```bash
php artisan migrate --seed
```

**6. Link Storage**
Link the storage directory to allow local image uploads for questions:
```bash
php artisan storage:link
```

## 🏃‍♂️ Running the Application

To run the application locally, you will need to start three separate processes:

**1. Start the local PHP server:**
```bash
php artisan serve
```

**2. Start the Vite development server (for Tailwind CSS/JS compilation):**
```bash
npm run dev
```

**3. Start the Queue Worker:**
VincenThinks relies on background jobs to process the AI content moderation without slowing down the user experience. You must run the queue worker:
```bash
php artisan queue:listen
```
*(Alternatively, you can use the built-in `composer run dev` script if configured to run concurrently).*

## 🛡️ Content Moderation Workflow

1. A user posts a Question, Answer, or Reply.
2. The content is saved with a `pending_review` status.
3. A background job (`CheckContentSafety`) is dispatched.
4. The system first checks against a local database of banned words.
5. If it passes, the text and attached images are sent to the **Gemini AI API**.
6. If flagged as unsafe, the content remains hidden, and admins are notified. If safe, the status updates to `published` and notifications are sent to relevant users.

## 📄 License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).