OS := $(shell uname)

start_dev:
ifeq ($(OS),Darwin)
	docker volume create --name=l10-app-sync
	docker-sync start
	./vendor/bin/sail up -d
else
	./vendor/bin/sail up -d
endif

stop_dev:           ## Stop the Docker containers
ifeq ($(OS),Darwin)
	./vendor/bin/sail stop
	docker-sync stop
else
	./vendor/bin/sail stop
endif

kill_dev:           ## Clean everything for a fresh start
ifeq ($(OS),Darwin)
	./vendor/bin/sail -v stop
	docker-sync clean
else
	./vendor/bin/sail -v stop
endif
