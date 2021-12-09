# LDL ENV file builder

Combines:

- [Env util](https://github.com/ldl-php/ldl-env-util)
- [File finder local adapter](https://github.com/ldl-php/ldl-file-finder-adapter-local)

To create a powerful finder and env compiler which will find all .env files inside a directory recursively
and compile them into one single env output file which will contain all the lines of found .env files

#### Example usage

```
php bin/ldl-env-build env:build -d /path/to/project/with/env/files ./out.env
```

### TODO 

- Add more documentation
- Add more options to CLI ldl-env-build command