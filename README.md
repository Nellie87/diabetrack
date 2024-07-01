# DiabeTrack:A health app that keeps track of your diabetic measurements

## Description
DiabeTrack is a health app designed to keep track of diabetic measurements. This platform allows diabetic patients to enter their readings, enabling doctors to access their data and communicate in real-time.

## Built with
* [Laravel](https://laravel.com/docs/11.x)
* [Laravel Jetsream](https://jetstream.laravel.com/introduction.html)

## Set up and deploy
### Prerequisites
1. PHP installed on your system (version 7.4 or higher recommended)
2. Composer globally installed
3. Node.js and npm installed
4. MySQL or another compatible database installed
5. Git installed

#### 1. Clone the repository
    git clone https://github.com/Nellie87/diabetrack.git
    cd Diabetrack

#### 2. Install PHP dependecies
    composer install
    
#### 3. Install Node.js Dependencies
    npm install
    
#### 4. Set Environment Variables
Create a copy of the .env.example file and rename it to .env. Update the database connection settings and other necessary variables.

#### 5. Generate Application Key
    php artisan key:generate
    
#### 6. Database Migration
    php artisan migrate
    
#### 7. Compile Assets
    npm run dev
    
#### 8. Run the Application
    php artisan serve

## Project Structure

  The project structure is documented in the [project_structure.txt](docs/project_structure.txt) file.

                    
## Contibutors
* [Nelius Ndung'u ](https://github.com/Nellie87)
* [Keith Karathi](https://github.com/Keithkkkkk)

## Licenses
* MIT License
* [BootstrapeMade](https://bootstrapmade.com/license/)
