# http-service
Repository for storing ofcourseme test

## Installation

### Prerequisites

- yarn installed
- npm installed
- docker/docker-compose installed

### Steps

1. Clone the repository (don't specify an existing folder)
```shell
git clone <repo_url>
```

2. Navigate to the created root directory

3. Build the docker containers by running 

```shell
docker-compose up -d --build
```

4. Initiate a terminal in the php container by typing
```shell
docker-compose exec php /bin/bash
```

5. Install composer dependencies by typing the following in the newly created terminal
```shell
composer update
```

6. Exit from the created terminal and navigate to `http-service/app/assets`

7. Install node dependencies by typing:
```shell
npm install
```

8. Install yarn dependencies by typing:
```shell
yarn install
```

9. Make a copy of `http-service/app/.env` in the same directory and name it `.env.local`

10. Open the created `http-service/app/.env.local` file and change the following

    - MAILER_DSN: [How to set up Amazon SES](https://docs.aws.amazon.com/ses/latest/DeveloperGuide/send-email-smtp.html)
    - ERROR_STATUS_EMAIL=A verified email address to where you want to receive your error status emails

11. Enjoy


