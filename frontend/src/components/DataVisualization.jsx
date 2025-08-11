// src/components/DataVisualization.jsx
import React from "react";
import { Bar, Line, Pie } from "react-chartjs-2";
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  BarElement,
  PointElement,
  LineElement,
  ArcElement,
  Tooltip,
  Legend,
} from "chart.js";

// Registro de componentes Chart.js necesarios para los gráficos
ChartJS.register(
  CategoryScale,
  LinearScale,
  BarElement,
  PointElement,
  LineElement,
  ArcElement,
  Tooltip,
  Legend
);

function SimpleChart({ suggestion, sampleRows }) {
  if (!suggestion || !Array.isArray(sampleRows)) return null;

  const type = suggestion.type;

  if (type === "table") {
    return (
      <div style={{ maxHeight: 260, overflow: "auto" }}>
        <table style={{ width: "100%", color: "#fff", borderCollapse: "collapse" }}>
          <thead>
            <tr>
              {Object.keys(sampleRows[0] || {}).map((col) => (
                <th
                  key={col}
                  style={{
                    borderBottom: "1px solid #555",
                    padding: "6px 8px",
                    textAlign: "left",
                  }}
                >
                  {col}
                </th>
              ))}
            </tr>
          </thead>
          <tbody>
            {sampleRows.map((row, i) => (
              <tr key={i} style={{ borderBottom: "1px solid #444" }}>
                {Object.keys(row).map((col) => (
                  <td
                    key={col}
                    style={{ padding: "4px 8px", whiteSpace: "nowrap", overflow: "hidden", textOverflow: "ellipsis" }}
                    title={String(row[col])}
                  >
                    {String(row[col])}
                  </td>
                ))}
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    );
  }

  // Opciones comunes para gráficos de barra y línea, con escala x tipo categórico
  const options = {
    responsive: true,
    maintainAspectRatio: false,
    scales: {
      x: {
        type: "category",
        ticks: { maxRotation: 45, minRotation: 30 },
        grid: {
          display: false,
        },
      },
      y: {
        beginAtZero: true,
        grid: {
          color: "#555",
        },
      },
    },
    plugins: {
      legend: {
        labels: {
          color: '#ddd',
        },
      },
      tooltip: {
        enabled: true,
      },
    },
  };

  if (type === "timeseries") {
    const x = suggestion.x;
    const y = suggestion.y;
    const labels = sampleRows.map((r) => r[x]).slice(0, 50);
    const dataValues = sampleRows
      .map((r) => {
        const val = parseFloat(r[y]);
        return isNaN(val) ? 0 : val;
      })
      .slice(0, 50);
    const data = {
      labels,
      datasets: [
        {
          label: suggestion.title,
          data: dataValues,
          fill: false,
          borderColor: "rgba(100, 150, 240, 0.8)",
          backgroundColor: "rgba(100, 150, 240, 0.5)",
          tension: 0.3,
          pointRadius: 3,
        },
      ],
    };
    return <Line data={data} options={options} height={300} />;
  }

  if (type === "barchart" || type === "piechart") {
    const cat = suggestion.category || suggestion.geo_name || suggestion.x;
    const val = suggestion.value || suggestion.y;
    const map = {};
    sampleRows.forEach((r) => {
      const key = r[cat] || "(sin)";
      const value = parseFloat(r[val]);
      map[key] = (map[key] || 0) + (isNaN(value) ? 0 : value);
    });
    const labels = Object.keys(map).slice(0, 20);
    const dataVals = labels.map((l) => map[l]);

    const commonData = {
      labels,
      datasets: [
        {
          label: suggestion.title,
          data: dataVals,
          backgroundColor: labels.map(
            (_, i) =>
              `hsl(${(i * 360) / labels.length}, 70%, 60%)`
          ),
          borderWidth: 1,
          borderColor: "#222",
        },
      ],
    };

    if (type === "barchart") {
      return <Bar data={commonData} options={options} height={300} />;
    } else {
      // Pie charts no requieren escalas
      const pieOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: "right",
            labels: {
              color: '#ddd',
              boxWidth: 12,
              padding: 10,
            },
          },
          tooltip: {
            enabled: true,
          },
        },
      };
      return <Pie data={commonData} options={pieOptions} height={300} />;
    }
  }

  if (type === "heatmap" || type === "choropleth") {
    return (
      <div style={{ padding: 12, color: "#ddd" }}>
        Mapa sugerido: {suggestion.title}. Implementación de mapas requiere Leaflet/Mapbox y GeoJSON.
      </div>
    );
  }

  return null;
}

export default SimpleChart;
