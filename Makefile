.PHONY: fix
fix:
	php vendor/bin/php-cs-fixer fix src tests \
		--show-progress=none \
		--verbose \
		--config php-cs-fixer.php

.PHONY: fix-github
fix-github:
	php vendor/bin/php-cs-fixer fix src tests \
		--dry-run \
		--show-progress=none \
		--verbose \
		--config php-cs-fixer.php \
		--format=checkstyle | php vendor/bin/cs2pr

.PHONY: psalm
psalm:
	php vendor/bin/psalm --config=psalm.xml --find-dead-code --threads=1 --output-format=console

.PHONY: psalm-github
psalm-github:
	php vendor/bin/psalm --config=psalm.xml --find-dead-code --threads=1 --long-progress --output-format=github

.PHONY: phpstan
phpstan:
	php vendor/bin/phpstan analyze --error-format table --level max --configuration phpstan.neon src tests

.PHONY: phpstan-github
phpstan-github:
	php vendor/bin/phpstan analyze --ansi --error-format github --level max --configuration phpstan.neon src tests

.PHONY: phpunit
phpunit:
	php vendor/bin/phpunit

.PHONY: phpunit-github
phpunit-github:
	php vendor/bin/phpunit --printer mheap\\GithubActionsReporter\\Printer

.PHONY: all
all:
	make fix
	make psalm
	make phpstan
	make phpunit
