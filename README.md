# players.json  

Web hosts don't like rogue PHP scripts running for too long, so I'm moving this to a github actions cron.  

Used to 1) correct names within various DB tables and 2) generate json lists (used for player drop down menus)  

`./main.php <host> <db in use> <user name> <DB password> <steam web API>`  

### Admins  

Before editing player_aliases.php:  
1) Check that the cron job isn't about to execute (executes every 8 hours, so only edit if last commit is 6 hours or less ago)  
2) update your local folder (`git pull`)  
3) run `php -l` on the player_aliases.php file, or paste the contents into an online PHP editor to make sure the [format is correct](https://i.imgur.com/jnfHoug.jpg)  

[Missing aliases](https://github.com/dustinandband/players.json/blob/main/logs/MissingPlayers.md) (Organized by most rounds logged)  