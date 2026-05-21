# Color definitions
GREEN=\033[1;32m
BLUE=\033[1;34m
RED=\033[1;31m
YELLOW=\033[1;33m
NC=\033[0m # No Color

start_dev:
	@echo "Starting development environment..."
	@echo " "
	./vendor/bin/sail up -d
	@echo " "
	@echo " "
	@echo "${GREEN}╔════════════════════════════════════════╗"
	@echo "    Project initialized successfully!    "
	@echo "╚════════════════════════════════════════╝${NC}"
	@echo " "
	@echo "${GREEN}Access Project via${NC} ${YELLOW}http://localhost:8007${NC}"
	@echo " "
	@echo "${GREEN}Access Mailpit via${NC} ${YELLOW}http://localhost:8027${NC}"
	@echo " "
	@echo "${GREEN}Access MariaDB at ${NC} ${YELLOW}port 3307${NC}"
	@echo " "
	@echo "${GREEN}Access Redis at ${NC} ${YELLOW}port 6377${NC}"
	@echo " "

stop_dev:           ## Stop the Docker containers
	./vendor/bin/sail stop
	@echo "${YELLOW}╔════════════════════════════════════════╗"
	@echo "   Project uninitialized successfully!    "
	@echo "╚════════════════════════════════════════╝${NC}"
	@echo " "
	@echo "To completely destroy data volume run ${RED}'make kill_dev'${NC}"
	@echo " "
	@echo " "

kill_dev:           ## Clean everything for a fresh start
	./vendor/bin/sail -v stop

watch_dev:
	@echo "Starting development environment (foreground)..."
	@echo " "
	./vendor/bin/sail up & (sleep 8 && tail -f storage/logs/laravel-$$(date +%F).log)
