# Pimcore Project Skeleton 

```bash
sed -i "s|#user: '1000:1000'|user: '$(id -u):$(id -g)'|g" docker-compose.yaml
```

```bash
docker compose up -d
```

```bash
docker compose exec php composer install 
```

```bash
docker compose exec php vendor/bin/pimcore-install --mysql-host-socket=db --mysql-username=pimcore --mysql-password=pimcore --mysql-database=pimcore
```
set admin user and password
answer no to installing bundles
type yes to continue

Creare web configs:

Create cartella:

Creare almeno un oggetto
