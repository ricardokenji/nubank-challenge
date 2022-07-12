
## Technical Decisions
### PHP 8
Foi escolhido o PHP por ter mais familiaridade para desenvolver o teste de maneira rápida.

### DDD
Foi utilizado alguns princípios do domain-drive-design, como separação por camadas e aggregate root (Account), que mantém o estado correto de suas invariáveis.

## Como executar:
### Docker:
- executar na linha de comando: 

```
docker build -t nubank .
docker run -p 8090:80 -d --name nubank nubank
docker exec -it nubank bash
```
  
### Run application
- executar no container: `php authorizer.php < operations`

### Run tests 
- execute `docker exec -it nubank  ./vendor/bin/phpunit tests`
