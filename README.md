# 📊 Análisis y Visualización de Datos del Gobierno / Government Data Analysis & Visualization

🚀 **Un proyecto full-stack para analizar y visualizar datos del gobierno de manera accesible. / A full-stack project for analyzing and visualizing government data in an accessible way.**

## 📌 Características / Features
✔️ **Visualización de Datos / Data Visualization:**  
Mapas de calor, diagramas de dispersión, clustering y más. / Heatmaps, scatter plots, clustering, and more.  
  
✔️ **Correlación de Variables / Variable Correlation:**  
Comparar indicadores económicos (por ejemplo, inflación vs. desempleo). / Compare economic indicators (e.g., inflation vs. unemployment).  
  
✔️ **Calculadora de Impuestos y Gasto Público / Tax & Public Spending Calculator:**  
Ver cómo los salarios contribuyen a los presupuestos públicos. / See how salaries contribute to public budgets.  
  
✔️ **Análisis Estadístico / Statistical Analysis:**  
Media, mediana, varianza, percentiles, etc. / Mean, median, variance, percentiles, etc.  
  
✔️ **Análisis de Series Temporales / Time Series Analysis:**  
Rastrear tendencias de datos a lo largo del tiempo. / Track data trends over time.  
  
✔️ **Modelado Predictivo / Predictive Modeling:**  
Predicciones basadas en ML (bajo investigación de viabilidad). / ML-based forecasts (feasibility under research).  
  
✔️ **Modo Transparencia / Transparency Mode:**  
Muestra cálculos y fuentes de datos. / Shows calculations & data sources.  
  
✔️ **Datos en Tiempo Real / Real-time Data:**  
Integración con APIs de datos abiertos del gobierno. / Integrated with government open data APIs.  
  
✔️ **Seguridad Primero / Security First:**  
Mejores prácticas para integridad de datos y ciberseguridad. / Best practices for data integrity & cybersecurity.  

## 🎯 Objetivos / Goals
- Hacer que los datos gubernamentales complejos sean accesibles a un público amplio. / Make complex government data accessible to a wide audience.  
- Proporcionar herramientas interactivas y fáciles de usar para el análisis. / Provide interactive and easy-to-use analysis tools.  
- Asegurar transparencia y explicabilidad en todos los cálculos. / Ensure transparency and explainability in all computations.  

## 🛠️ Stack Tecnológico / Tech Stack
- Frontend: **React**  
- Backend: **Django**  
- **Base de Datos / Database:**  
- **APIs:** [datos.gob.es](https://datos.gob.es), otras fuentes de datos abiertos / other open data sources  
- **Visualizaciones / Visualizations:**  
- **Machine Learning:**  

## 📂 Estructura del Proyecto / Project Structure
```
/TFG
├── frontend/        # Frontend de la aplicación (React)
│   ├── node_modules/   
│   ├── public/        
│   │   ├── index.html   
│   │   └── vite.svg    
│   └── src/           
│       ├── assets/    
│       ├── App.css    
│       ├── App.jsx    
│       ├── index.css    
│       └── main.jsx   
│   ├── .gitignore     
│   ├── eslint.config.js 
│   ├── package-lock.json 
│   ├── package.json     
│   ├── README.md        
│   └── vite.config.js   
├── backend/         # Backend de la aplicación (Django)
│   ├── backend/     
│   │   ├── __init__.py 
│   │   ├── asgi.py     
│   │   ├── settings.py 
│   │   ├── urls.py     
│   │   └── wsgi.py     
│   ├── core/        # Aplicación Django principal
│   │   ├── migrations/ 
│   │   ├── __init__.py 
│   │   ├── admin.py    
│   │   ├── apps.py     
│   │   ├── models.py   
│   │   ├── tests.py    
│   │   ├── urls.py     
│   │   └── views.py    
│   ├── db.sqlite3     
│   └── manage.py
├── docs/            # Documentation & research
│   └── gantt.mpp    
├── scripts/         # Utility scripts
└── README.md        # This file
```