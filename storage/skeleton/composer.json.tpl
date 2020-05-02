{
  "name": "<?php echo $composerName; ?>",
  "description": "<?php echo $composerDesc; ?>",
  "license": "Apache-2.0",
  "keywords": [
    <?php echo "$composerKeywords\n"; ?>
  ],
  "type": "library",
  "authors": [
    {
      "name": "Martin Juul",
      "email": "code@juul.xyz"
    }
  ],
  "require": {
    "php": "^7.4.5",
    "illuminate/support": "^7.0"
  },
  "require-dev": {
    "orchestra/testbench": "^4.0|^5.0",
    "phpunit/phpunit": "^8.4|^9.0"
  },
  "autoload": {
    "psr-4": {
      "<?php echo $vendor; ?>\\<?php echo $package; ?>\\": "src"
    }
  },
  "autoload-dev": {
    "psr-4": {
      "<?php echo $vendor; ?>\\<?php echo $package; ?>\\Tests\\": "tests"
    }
  },
  "scripts": {
    "phpunit": "phpunit"
  },
  "extra": {
    "laravel": {
      "providers": [
        "<?php echo $vendor; ?>\\<?php echo $package; ?>\\ServiceProvider"
      ]
    }
  },
  "config": {
    "optimize-autoloader": true,
    "preferred-install": "dist",
    "sort-packages": true
  },
  "minimum-stability": "dev",
  "prefer-stable": true
}
