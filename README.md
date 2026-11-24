# Examen AWS: EC2 + Docker + RDS MySQL

App PHP mínima que se conecta a RDS con variables de entorno.

## Despliegue en EC2 (Amazon Linux 2023)
```bash
git clone https://github.com/TU_USUARIO/examen-aws-app.git
cd examen-aws-app
cp .env.example .env && nano .env      # poner endpoint, usuario y clave de RDS
docker build -t examen-app .
docker run -d --name web -p 80:80 --env-file .env --restart always examen-app
```

## Crear la tabla en RDS
```bash
mysql -h ENDPOINT_RDS -u admin -p < init.sql
```
