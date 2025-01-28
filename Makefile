
##------------------------------------------------------##
##                      VARIABLES                       ##
##------------------------------------------------------##
## Variable pour la console Symfony
CONSOLE=php bin/console
## Variable pour docker-compose
FIG=docker compose
# Dans la ligne de commande de notre machine, on vérifie si docker-compose est disponible
HAS_DOCKER:=$(shell command -v $(FIG) 2> /dev/null)
# Si c'est le cas, EXEC et EXEC_DB vont permettre d'exécuter des commandes dans les conteneurs
ifdef HAS_DOCKER
	EXEC=$(FIG) exec app
	EXEC_DB=$(FIG) exec db
# Sinon, on exécute les commandes sur la machine locale
else
	EXEC=
	EXEC_DB=
endif

##------------------------------------------------------##
##                      COMPOSER                        ##
##------------------------------------------------------##
## Installation des dépendances
install:
	$(EXEC) composer install

##------------------------------------------------------##
##                      COMMANDE DOCKER                 ##
##------------------------------------------------------##
docker-start:
	docker-compose up -d

docker-stop:
	docker-compose down

docker-restart: docker-stop docker-start


##------------------------------------------------------##
##                      COMMANDE SYMFONY                ##
##------------------------------------------------------##

init-db:
	$(EXEC) $(CONSOLE) doctrine:database:create --if-not-exists
#	$(EXEC) $(CONSOLE) doctrine:migrations:migrate --no-interaction
#	$(EXEC) $(CONSOLE) doctrine:fixtures:load --no-interaction
## Création d'une migration
create-migration:
	$(EXEC) $(CONSOLE) make:migration
## Migration de la base de données
migrate:
	$(EXEC) $(CONSOLE) doctrine:migrations:migrate --no-interaction
## Percistence des données
fixtures:
	$(EXEC) $(CONSOLE) doctrine:fixtures:load

##------------------------------------------------------##
##                      AUTRES                          ##
##------------------------------------------------------##
## Correction de la qualité du code
csfix:
	$(EXEC) composer fix
## Vérification de la qualité du code avec PHPStan et PHP-CS-Fixer
check:
	$(EXEC) composer check