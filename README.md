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
- Base de Datos / Database:**PostgreSQL**  
- **APIs:** [datos.gob.es](https://datos.gob.es), otras fuentes de datos abiertos / other open data sources  
- **Visualizaciones / Visualizations:**  

## 📂 Estructura del Proyecto / Project Structure
```
/TFG
├── backend/                      # Backend Django
│   ├── settings.py
│   ├── urls.py
│
├── core/                         # App principal Django
│   ├── static/core/              # Archivos estáticos
│   │   └── # styles/             # (Carpeta vacía o no mostrada)
│   ├── templates/core/           # Plantillas
│   │   └── home.html
│   ├── admin.py
│   ├── apps.py
│   ├── models.py
│   ├── tests.py
│   ├── urls.py
│   └── views.py
│
├── data/                         # Módulo de datos
│   ├── services/                 # Servicios de datos
│   │   ├── search_datasets.py
│   │   └── sparql_service.py
│   └── utils/                    # Utilidades
│       ├── file_utils.py
│
├── frontend/                     # Frontend React
│   ├── node_modules/
│   ├── public/                   # Assets públicos
│   │   ├── index.html
│   ├── src/                      # Código fuente
│   │   ├── api/
│   │   ├── components/           # Componentes React
│   │   │   ├── FunctionalidadesPanel.jsx
│   │   │   ├── Layout.jsx
│   │   │   └── SearchComponent.jsx
│   │   ├── pages/               # Páginas
│   │   │   ├── CorrelationAnalysis.jsx
│   │   │   ├── ExportReports.jsx
│   │   │   ├── Home.jsx
│   │   │   ├── Prediction.jsx
│   │   │   ├── PublicSpending.jsx
│   │   │   └── SearchAndFilter.jsx
│   │   ├── App.css
│   │   ├── App.jsx
│   │   ├── backendService.js
│   │   ├── index.css
│   │   └── main.jsx
│   ├── .env
│   ├── start_project.ps1         # Scripts de inicio/detención
│   └── stop_project.ps1
│
├── docs/                         # Diagrama de Gantt
```