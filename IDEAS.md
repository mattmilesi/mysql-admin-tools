# MySQL Admin Tools

## Securing the Percona tool

### Idea #1
- Connections are stored in the DB, excepts passwords.
- On the DB there is a key (for each connection) to decrypt passwords sent from the client.
- Passwords are encrypted locally on the client. The encrypted password is saved in the localStorage, while the key is sent to the server.

### Idea #2
- Connections are centralized in the DB, but credentials are taken from AWS Secrets Manager.
- AWS 
