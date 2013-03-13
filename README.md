# Sweepo

## Installation

- Clone the project :

```bash
    git clone git@github.com:rgazelot/Sweepo.git
```

- Duplicate parameters.yml.dist and make your own settings (db_name, twitter_callback, ...).

- Install components via Bower :

```bash
    ./bin/bower_init.sh
```

- Composer :

```bash
    composer update
```

- Assetic :

```bash
    ./app/console assetic:dump --force
```

- Images variable : Make the correct path in /web/front/less/variable.less => @pathImages.

- Create your doctrine schema :

```bash
    ./app/console doctrine:schema:update --force
```

