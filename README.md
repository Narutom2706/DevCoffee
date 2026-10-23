# Projet Café

## Équipe

| Membre               | Rôle           |
| -------------------- | -------------- |
| Tom Cizeau           | Frontend       |
| Tom Audry            | Backend — Lead |
| Ivan Lhomme Ageorges | Backend        |

## Structure du projet

```
projet_cafe/
├── backend/
│   ├── Core/
│   │   └── Database.php
│   ├── public/
│   │   └── index.php
│   ├── src/
│   │   ├── Controllers/
│   │   │   ├── ConsumptionController.php
│   │   │   └── DrinkController.php
│   │   ├── Middlewares/
│   │   │   ├── CorsMiddleware.php
│   │   │   └── EcoMetricsMiddleware.php
│   │   ├── Models/
│   │   │   ├── Consumption.php
│   │   │   └── Drink.php
│   │   ├── Responses/
│   │   │   └── ProblemDetailsResponse.php
│   │   └── Services/
│   │       └── CoffeeService.php
│   ├── .env.exemple
│   ├── .gitignore
│   ├── composer.json
│   ├── composer.lock
│   └── schema.sql
│
├── frontend/
│   ├── .vscode/
│   │   └── extensions.json
│   ├── public/
│   │   ├── favicon.svg
│   │   └── icons.svg
│   ├── src/
│   │   ├── assets/
│   │   │   ├── hero.png
│   │   │   ├── vite.svg
│   │   │   └── vue.svg
│   │   ├── components/
│   │   │   ├── ConsumptionForm.vue
│   │   │   ├── ConsumptionList.vue
│   │   │   ├── DrinkModal.vue
│   │   │   └── EcoBadge.vue
│   │   ├── composables/
│   │   │   └── useCoffee.ts
│   │   ├── types/
│   │   │   └── coffee.ts
│   │   ├── App.vue
│   │   ├── main.ts
│   │   └── style.css
│   ├── .gitignore
│   ├── bun.lock
│   ├── index.html
│   ├── package.json
│   ├── README.md
│   ├── tsconfig.app.json
│   ├── tsconfig.json
│   ├── tsconfig.node.json
│   └── vite.config.ts
│
├── README.md
└── startServeur.bat
```