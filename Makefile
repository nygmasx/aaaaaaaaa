.PHONY: help install setup dev build clean fresh test

# Variables
PHP = php
COMPOSER = composer
NPM = npm
ARTISAN = $(PHP) artisan

# Couleurs pour l'affichage
GREEN = \033[0;32m
YELLOW = \033[0;33m
RED = \033[0;31m
NC = \033[0m # No Color

help: ## Affiche cette aide
	@echo "$(GREEN)Application de Transfert d'Argent - Commandes disponibles :$(NC)"
	@echo ""
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  $(YELLOW)%-15s$(NC) %s\n", $$1, $$2}'

install: ## Installe toutes les dépendances
	@echo "$(GREEN)Installation des dépendances...$(NC)"
	$(COMPOSER) install
	$(NPM) install

setup: install ## Configuration initiale complète du projet
	@echo "$(GREEN)Configuration du projet...$(NC)"
	@if [ ! -f .env ]; then \
		cp .env.example .env; \
		echo "$(YELLOW)Fichier .env créé depuis .env.example$(NC)"; \
	fi
	$(ARTISAN) key:generate
	@if [ ! -f database/database.sqlite ]; then \
		touch database/database.sqlite; \
		echo "$(YELLOW)Base de données SQLite créée$(NC)"; \
	fi
	$(ARTISAN) migrate:fresh --seed
	$(NPM) run build
	@echo "$(GREEN)✅ Projet configuré avec succès !$(NC)"
	@echo "$(YELLOW)⚠️  N'oubliez pas d'ajouter votre clé API Fixer.io dans .env$(NC)"
	@echo "$(YELLOW)   FIXER_API_KEY=votre_cle_api_ici$(NC)"

dev: ## Lance l'environnement de développement
	@echo "$(GREEN)Démarrage en mode développement...$(NC)"
	@echo "$(YELLOW)Serveur Laravel : http://localhost:8000$(NC)"
	$(COMPOSER) run dev

build: ## Compile les assets pour la production
	@echo "$(GREEN)Compilation des assets...$(NC)"
	$(NPM) run build

fresh: ## Réinitialise la base de données avec les données de test
	@echo "$(GREEN)Réinitialisation de la base de données...$(NC)"
	$(ARTISAN) migrate:fresh --seed
	@echo "$(GREEN)✅ Base de données réinitialisée !$(NC)"

test: ## Lance les tests
	@echo "$(GREEN)Exécution des tests...$(NC)"
	$(ARTISAN) test

clean: ## Nettoie les caches
	@echo "$(GREEN)Nettoyage des caches...$(NC)"
	$(ARTISAN) cache:clear
	$(ARTISAN) config:clear
	$(ARTISAN) view:clear
	$(ARTISAN) route:clear
	$(NPM) run build
	@echo "$(GREEN)✅ Caches nettoyés !$(NC)"

production: clean ## Optimise pour la production
	@echo "$(GREEN)Optimisation pour la production...$(NC)"
	$(COMPOSER) install --no-dev --optimize-autoloader
	$(NPM) run build
	$(ARTISAN) config:cache
	$(ARTISAN) route:cache
	$(ARTISAN) view:cache
	@echo "$(GREEN)✅ Application optimisée pour la production !$(NC)"

reset: ## Remet le projet à zéro (supprime vendor, node_modules, etc.)
	@echo "$(RED)Remise à zéro du projet...$(NC)"
	@read -p "Êtes-vous sûr ? (y/N): " confirm && [ "$$confirm" = "y" ]
	rm -rf vendor/
	rm -rf node_modules/
	rm -f .env
	rm -f database/database.sqlite
	@echo "$(GREEN)✅ Projet remis à zéro !$(NC)"

logs: ## Affiche les logs Laravel en temps réel
	@echo "$(GREEN)Logs Laravel (Ctrl+C pour arrêter) :$(NC)"
	tail -f storage/logs/laravel.log

db-console: ## Ouvre la console de base de données
	$(ARTISAN) tinker

serve: ## Lance uniquement le serveur Laravel
	@echo "$(GREEN)Serveur Laravel : http://localhost:8000$(NC)"
	$(ARTISAN) serve

watch: ## Lance la compilation des assets en mode watch
	@echo "$(GREEN)Compilation des assets en mode watch...$(NC)"
	$(NPM) run dev

# Raccourcis
up: dev ## Alias pour 'make dev'
start: dev ## Alias pour 'make dev'