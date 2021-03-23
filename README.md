# Aplikasi PKPM Polres Batang
## Asset:
- [File Resource]()
- [Mockup]()

## Backup Database
```sh
docker exec mysql.server /usr/bin/mysqldump -u root --password=root dbweb_ppkm > assets/database.sql
```

## Restore Database
```sh
cat assets/database.sql | docker exec -i mysql.server /usr/bin/mysql -u root --password=root dbweb_ppkm
```