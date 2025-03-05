# MySQL Admin Tools

## Usage
This project is intended to run in production on a remote machine.

**DISCLAIMER: DO NOT USE THIS SOFTWARE LOCALLY WITH PRODUCTION DATA!**

### Users
In order to use it, you need to have a user registered on the DB.

## Development
Use docker-compose to run the project. \
The seeds provide a Test User: _test@example.com_ - _password_.

### Run for development
Run
```
composer start
```
or
```
composer rebuild
```
to run the software locally.
`composer rebuild` also rebuilds the image according the Dockerfile.

### Stop everything
```
composer stop
```

### Run the migrations
```
composer migrate
```

### Seed the database
```
composer seed
```

### Build the frontend
```
npm run build
```

## Roadmap and ideas
Short term todos:
- Fix session duration
- Seed the target DB
- Have a local running MVP
- Enable `npm run build` in watch mode
- Figure out how to secure the tool in production

These are the capabilities that this tool aims to cover, plus some nice-to-have ideas:
- Implement alerts with e-mails or other providers, like Telegram
- Handle credentials
- Support for multiple databases
- Implement audit logs
- Implement history option for resuming execution
- Implement pause file option for pausing execution
- Implement customization of progress
- Add pt-visual-explain
- Add "kill processes as root" feature
- Add pt-show-grants
- Support for users creation and grants management (?)
- Add pt-deadlock-logger (?)
- Add pt-table-usage (?)
- Move execution to ECS (?)
