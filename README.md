📌 Laravel File Upload & Automatic Deletion System
This project is a Laravel-based file upload and management system with the following features:
✅ Asynchronous file upload (PDF, DOCX)
✅ CRUD operations for managing files
✅ Automatic file deletion after 5 minutes
✅ RabbitMQ notifications upon file deletion

🚀 Features
File Upload: Supports PDF & DOCX files with a 10MB size limit.
Manage Uploaded Files: View and delete files manually.
Auto-Delete After 5 Minutes: Files are automatically deleted and a notification is sent via RabbitMQ.
Queue System: Uses Laravel's queue to handle notifications asynchronously.
🛠️ Installation & Setup
Follow these steps to set up and run the project on your local machine.

1️⃣ Clone the Repository

git clone https://github.com/iamkamranhashmi/laraveltestask.git
cd laravel-file-upload
2️⃣ Install Dependencies

composer install
npm install
3️⃣ Configure Environment
Copy the .env.example file and update database and RabbitMQ settings.

cp .env.example .env
Update the following variables in .env:

env
Copy
Edit
APP_NAME=LaravelFileManager
APP_ENV=local
APP_KEY=base64:generated_key
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=file_upload_db
DB_USERNAME=root
DB_PASSWORD=

# RabbitMQ Configuration
RABBITMQ_HOST=127.0.0.1
RABBITMQ_PORT=5672
RABBITMQ_USER=guest
RABBITMQ_PASSWORD=guest
RABBITMQ_QUEUE=file_deletion_queue
4️⃣ Run Database Migrations

php artisan migrate
5️⃣ Run Queue Worker

php artisan queue:work
6️⃣ Start Laravel Server

php artisan serve
Laravel server will run at: http://127.0.0.1:8000

📂 API Endpoints
1️⃣ Upload File

POST /upload
Request:
json
Copy
Edit
{
  "file": "PDF/DOCX file"
}
Response:

{
  "message": "File uploaded successfully",
  "file": {
    "id": 1,
    "filename": "document.pdf",
    "path": "storage/uploads/document.pdf"
  }
}
2️⃣ List All Files

GET /files
Response:
json
Copy
Edit
[
  {
    "id": 1,
    "filename": "document.pdf",
    "created_at": "2025-03-08 14:30:00"
  }
]
3️⃣ Delete a File Manually


DELETE /files/{id}
Response:
json

{
  "message": "File deleted successfully"
}
4️⃣ Manually Delete Expired Files


GET /delete-expired-files
Response (if files exist):

{
  "message": "Expired files deleted successfully"
}
Response (if no expired files):
json

{
  "message": "No expired files found"
}
🛠️ Project Structure


📂 laravel-file-upload
│── 📂 app
│   ├── 📂 Console
│   │   └── Kernel.php
│   ├── 📂 Http
│   │   ├── Controllers
│   │   │   ├── FileController.php
│   │   │   ├── ExpiredFileController.php
│   ├── 📂 Jobs
│   │   ├── SendDeletionNotification.php
│   ├── 📂 Models
│   │   ├── FileUpload.php
│── 📂 database
│   ├── 📂 migrations
│   │   ├── 2025_03_08_create_file_uploads_table.php
│── 📂 routes
│   ├── web.php
│── 📂 storage
│   ├── 📂 uploads
│── .env
│── README.md
│── composer.json
│── package.json
│── artisan
📝 How Automatic Deletion Works?
When a file is uploaded, it is stored in storage/uploads/.
Laravel's scheduler checks files every 5 minutes (deleteExpiredFiles() method).
If a file is older than 5 minutes, it is deleted from storage and database.
A RabbitMQ notification is sent with the filename.
📌 RabbitMQ Integration
When a file is deleted, the SendDeletionNotification job runs.
This job publishes a message to RabbitMQ.
You can consume the message using a RabbitMQ listener.
🛠️ Laravel Commands
Run Migrations

php artisan migrate
Clear Cache

php artisan cache:clear
Run Queue Worker

php artisan queue:work
Delete Expired Files Manually

php artisan schedule:run
🛠️ Troubleshooting
1️⃣ RabbitMQ is not sending messages
Run RabbitMQ manually:


sudo systemctl start rabbitmq-server
rabbitmqctl status
2️⃣ Files are not being deleted automatically
Check Laravel scheduler is running:


php artisan schedule:run
3️⃣ Queue Worker Not Processing Jobs

php artisan queue:restart
php artisan queue:work --tries=3
👨‍💻 Contributors
Your Name – Developer
Your Company/Team – Organization
📜 License
This project is open-source under the MIT License.

