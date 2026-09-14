# BB Docs — the two commands you need.
#
#   make check     what CI runs: front matter, internal links, image budget
#   make preview   render the pages locally at http://127.0.0.1:8088
#
# Everything here is plain PHP (8.1+). `make check` has no dependencies at all.
# `make preview` wants league/commonmark, the same renderer the website uses;
# `make deps` fetches it with composer if you have one.

PHP  ?= php
PORT ?= 8088
HOST ?= 127.0.0.1

.PHONY: check frontmatter links images preview deps lint help

help:
	@echo "make check     front matter + internal links + image budget (what CI runs)"
	@echo "make preview   http://$(HOST):$(PORT) — the pages, with the site's stylesheet"
	@echo "make deps      install league/commonmark locally, for the preview only"
	@echo "make lint      markdownlint, if you have Node (CI runs it too)"

check: frontmatter links images
	@echo "all checks passed"

frontmatter:
	@$(PHP) tools/check_frontmatter.php .

links:
	@$(PHP) tools/check_links.php .

images:
	@$(PHP) tools/check_images.php .

deps:
	composer install --no-interaction --no-progress

preview:
	@$(PHP) -S $(HOST):$(PORT) -t . tools/preview.php

lint:
	npx --yes markdownlint-cli2
