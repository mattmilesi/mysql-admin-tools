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
- Add MFA
- Have a local running MVP
- Figure out how to secure the tool in production
- Clean code moving schema change logic to a service
- Rename Percona references to generic SchemaChange
- Seed the target DB with cli params
- Enable `npm run build` in watch mode

These are the capabilities that this tool aims to cover, plus some nice-to-have ideas:
- Schema change
  - Implement alerts with e-mails or other providers, like Telegram
  - Handle credentials
  - Support for multiple databases
  - Better frontend
  - Implement audit logs
  - Implement history option for resuming execution
  - Implement pause file option for pausing execution
  - Implement customization of progress
  - Support for multiple tools (e.g. gh-ost)
- Add pt-visual-explain
- Add "kill processes as root" feature
- Add pt-show-grants
- Support for users creation and grants management (?)
- Add pt-deadlock-logger (?)
- Add pt-table-usage (?)
