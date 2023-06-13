# DeskPlan
DeskPlan is a web application that allows users to book a desk in a shared office space. It is built using the Laravel framework and uses a MySQL database. The application can be accessed at https://deskplan.com/.

## Contributing

Thank you for considering contributing. Please follow the steps below to contribute to the project.
First clone the repository to your local machine.
```bash
git clone git@github.com:hyperlinkgroup/DeskPlan.git
```
Then create a new branch for your contribution.
```bash
git checkout -b <branch-name>
```
Make your changes and commit them.
```bash
git commit -m "commit message"
```
Push your changes to the remote repository.
```bash
git push origin <branch-name>
```
Finally, open a pull request on GitHub.

## Installation
1. Clone the repository to your local machine.
```bash
git clone git@github.com:hyperlinkgroup/DeskPlan.git
```
2. Install the dependencies.
```bash
composer install
```
3. Create a new database.
4. Copy the .env.example file and rename it to .env.
5. Update the .env file with your database credentials.
6. Generate a new application key.
```bash
php artisan key:generate
```
7. Run the database migrations.
```bash
php artisan migrate
```
8. Run the database seeders.
```bash
php artisan db:seed
```
9. Start the development server.
```bash
php artisan serve
```
10. Visit http://localhost:8000/ in your browser.
11. Login with the following credentials.
```bash
Email: test@test.com
Password: password
```
12. You can now use the application.


## Roadmap
1. Add a feature to allow users to book a desk for multiple days.
2. Convert the JavaScript code to TypeScript.
3. Activate the email verification feature.

## License

The project is open-sourced software licensed under the [GNU General Public License v3.0](https://choosealicense.com/licenses/gpl-3.0/).
