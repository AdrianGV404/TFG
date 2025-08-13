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

function SimpleChart({ suggestion, sampleRows, labels, series }) {
  if ((!suggestion && !(labels && series)) || (sampleRows && sampleRows.length === 0 && !series)) {
    return null;
  }

  const type = suggestion?.type;

  // 🔹 Formatear valores que parecen timestamps
  const formatLabel = (label) => {
    if (!isNaN(label) && Number(label) > 1000000000) {
      return new Date(Number(label)).toLocaleDateString("es-ES");
    }
    return label;
  };

  // 🔹 Detección automática de columnas
  const detectColumns = () => {
    const cols = Object.keys(sampleRows[0] || {});
    let x = suggestion?.x;
    let y = suggestion?.y;
    let categoryCol = suggestion?.category || suggestion?.geo_name;

    if (!x) {
      x = cols.find((c) => /periodo|año|anio|fecha/i.test(c)) || cols[0];
    }
    if (!y) {
      y = cols.find((c) => c !== x && sampleRows.some((r) => !isNaN(parseFloat(r[c]))));
    }
    if (!categoryCol) {
      categoryCol = cols.find((c) => c !== x && c !== y);
    }

    return { x, y, categoryCol };
  };

  // 🔹 Opciones comunes de gráficos
  const commonOptions = {
    responsive: true,
    maintainAspectRatio: false,
    scales: {
      x: {
        type: "category",
        ticks: { maxRotation: 45, minRotation: 30, color: "#ddd" },
        grid: { display: false },
      },
      y: {
        beginAtZero: true,
        ticks: { color: "#ddd" },
        grid: { color: "#555" },
      },
    },
    plugins: {
      legend: { labels: { color: "#ddd" } },
      tooltip: { enabled: true },
    },
  };

  // ========================
  // 📊 Caso especial INE API (labels + series)
  // ========================
  if (labels && Array.isArray(labels) && series && Array.isArray(series) && series.length > 0) {
    const chartData = {
      labels: labels.map(formatLabel),
      datasets: series.map((s, i) => ({
        label: s.name || `Serie ${i + 1}`,
        data: s.data,
        borderColor: `hsl(${(i * 360) / series.length}, 70%, 50%)`,
        backgroundColor: `hsl(${(i * 360) / series.length}, 70%, 60%)`,
        tension: 0.3,
        pointRadius: 3,
        fill: false,
      })),
    };
    return <Line data={chartData} options={commonOptions} height={450} />;
  }

  // ========================
  // 📋 Tabla
  // ========================
  if (type === "table") {
    return (
      <div style={{ maxHeight: 400, overflow: "auto" }}>
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
                    style={{
                      padding: "4px 8px",
                      whiteSpace: "nowrap",
                      overflow: "hidden",
                      textOverflow: "ellipsis",
                    }}
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

  // ========================
  // 📈 Time series genérico (no INE)
  // ========================
  if (type === "timeseries") {
    const { x, y, categoryCol } = detectColumns();
    const labels = [...new Set(sampleRows.map((r) => r[x]))]
      .map(formatLabel)
      .sort();
    let datasets = [];

    if (categoryCol) {
      const grouped = {};
      sampleRows.forEach((r) => {
        const cat = r[categoryCol] || "Sin categoría";
        if (!grouped[cat]) grouped[cat] = {};
        grouped[cat][r[x]] = parseFloat(r[y]) || 0;
      });

      datasets = Object.keys(grouped).map((cat, i) => ({
        label: cat,
        data: labels.map((l) => grouped[cat][l] ?? 0),
        borderColor: `hsl(${(i * 360) / Object.keys(grouped).length}, 70%, 50%)`,
        backgroundColor: `hsl(${(i * 360) / Object.keys(grouped).length}, 70%, 50%)`,
        tension: 0.3,
        pointRadius: 3,
        fill: false,
      }));
    } else {
      datasets.push({
        label: suggestion?.title || y,
        data: sampleRows.map((r) => parseFloat(r[y]) || 0),
        borderColor: "rgba(100, 150, 240, 0.8)",
        backgroundColor: "rgba(100, 150, 240, 0.5)",
        tension: 0.3,
        pointRadius: 3,
        fill: false,
      });
    }

    return <Line data={{ labels, datasets }} options={commonOptions} height={450} />;
  }

  // ========================
  // 📊 Bar / Pie
  // ========================
  if (type === "barchart" || type === "piechart") {
    const { x, y, categoryCol } = detectColumns();
    const cat = categoryCol || x;
    const val = y || suggestion?.value;

    const map = {};
    sampleRows.forEach((r) => {
      const key = r[cat] || "(sin)";
      const value = parseFloat(r[val]);
      map[key] = (map[key] || 0) + (isNaN(value) ? 0 : value);
    });

    const labels = Object.keys(map).slice(0, 20).map(formatLabel);
    const dataVals = labels.map((l) => map[l]);

    const commonData = {
      labels,
      datasets: [
        {
          label: suggestion?.title || val,
          data: dataVals,
          backgroundColor: labels.map((_, i) => `hsl(${(i * 360) / labels.length}, 70%, 60%)`),
          borderWidth: 1,
          borderColor: "#222",
        },
      ],
    };

    if (type === "barchart") {
      return <Bar data={commonData} options={commonOptions} height={450} />;
    } else {
      const pieOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: "right",
            labels: { color: "#ddd", boxWidth: 12, padding: 10 },
          },
          tooltip: { enabled: true },
        },
      };
      return <Pie data={commonData} options={pieOptions} height={450} />;
    }
  }

  // ========================
  // 🗺 Mapas no implementados
  // ========================
  if (type === "heatmap" || type === "choropleth") {
    return (
      <div style={{ padding: 12, color: "#ddd" }}>
        Mapa sugerido: {suggestion?.title}. Implementación de mapas requiere Leaflet/Mapbox y GeoJSON.
      </div>
    );
  }

  return null;
}

export default SimpleChart;