docker exec -it mautic php "app/console" doctrine:schema:drop --force
docker exec -it mautic php "app/console" doctrine:schema:create
docker exec -it mautic php "app/console" doctrine:fixtures:load -n