ArticleHub
===========

Read, post and share articles

###Instructions

- Clone the repository
- Create file `app/config/parameters.yml` with the following content:
```
parameters:
    database_host: 127.0.0.1
    database_port: null
    database_name: <YOUR DATABASE NAME>
    database_user: <YOUR DATABASE USER>
    database_password: <YOUR DATABASE PASSWORD>
    mailer_transport: smtp
    mailer_host: 127.0.0.1
    mailer_user: null
    mailer_password: null
    secret: <YOUR SECRET>
```
- Use doctrine console commands to create your database:
	```
		> php bin/console doctrine:schema:create
	```
- Create a virtual host: `articlehub.com`
- Set up the host to point to your directory path using `Apache | Nginx` server 
- Start the webserver and browse to `http://articlehub.com/app_dev.php`
- Click on registration link and register as a new user with any of these emails (user1@example.com, user2@example.com ... user10@example.com)
- These emails are configured to be self-activated on registration
- Login with the email, password. 
- On the homepage, paste an article link, add tags, choose a category and click + Add Article to create a new article
- These emails accounts: user1@example.com, user2@example.com will be set as admin.
- Admin panel can be reached from the top-right dropdown menu and any member can be made admin from there.
- Admins can manage user access (blocking, activating/deactivating, grant/revoke admin privileges), create/delete category/tags.
- Start with adding a few categories from the admin panel. It is required when creating an article.
