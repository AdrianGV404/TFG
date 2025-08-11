// src/components/ProcessDatasetPanel.jsx
import { useState } from "react";
import { analyze_dataset } from "../api/backendService";

// Chart.js config
import { Bar, Line, Pie } from 'react-chartjs-2';
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  BarElement,
  PointElement,
  LineElement,
  ArcElement,
  Tooltip,
  Legend
} from 'chart.js';

ChartJS.register(CategoryScale, LinearScale, BarElement, PointElement, LineElement, ArcElement, Tooltip, Legend);

// Función para elegir la mejor distribución según prioridad
function pickBestDistribution(distributionArray) {
  const supportedPriority = ["json", "csv", "xml", "rdf+xml", "html"];

  const normalizeFormat = fmt => {
    if (!fmt) return "";
    fmt = fmt.toLowerCase();
    if (fmt.includes("json")) return "json";
    if (fmt.includes("csv")) return "csv";
    if (fmt.includes("xml") && fmt.includes("rdf")) return "rdf+xml";
    if (fmt.includes("xml")) return "xml";
    if (fmt.includes("html")) return "html";
    return fmt;
  };

  const clean = distributionArray.map(d => ({
    format: normalizeFormat(
      typeof d.format === "string"
        ? d.format
        : d.format?.value || d.format?._value || ""
    ),
    url:
      typeof d.accessURL === "string"
        ? d.accessURL
        : d.accessURL?.value || d.accessURL?._value || ""
  })).filter(d => d.url && supportedPriority.includes(d.format));

  clean.sort(
    (a, b) =>
      supportedPriority.indexOf(a.format) - supportedPriority.indexOf(b.format)
  );

  return clean.length > 0 ? clean[0] : null;
}

function SimpleChart({ suggestion, sampleRows }) {
  if (!suggestion || !Array.isArray(sampleRows)) return null;

  const type = suggestion.type;
  if (type === "table") return null;

  if (type === "timeseries") {
    const x = suggestion.x;
    const y = suggestion.y;
    const labels = sampleRows.map(r => r[x]).slice(0, 50);
    const data = sampleRows.map(r => {
      const val = parseFloat(r[y]);
      return isNaN(val) ? 0 : val;
    }).slice(0, 50);
    return <Line data={{ labels, datasets: [{ label: suggestion.title, data, fill: false }] }} />;
  }

  if (type === "barchart" || type === "piechart") {
    const cat = suggestion.category || suggestion.geo_name || suggestion.x;
    const val = suggestion.value || suggestion.y;
    const map = {};
    sampleRows.forEach(r => {
      const k = r[cat] || "(sin)";
      const v = parseFloat(r[val]) || 0;
      map[k] = (map[k] || 0) + v;
    });
    const labels = Object.keys(map).slice(0, 20);
    const dataVals = labels.map(l => map[l]);
    if (type === "barchart") {
      return <Bar data={{ labels, datasets: [{ label: suggestion.title, data: dataVals }] }} />;
    } else {
      return <Pie data={{ labels, datasets: [{ label: suggestion.title, data: dataVals }] }} />;
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

export default function ProcessDatasetPanel({ selectedItems }) {
  const [isProcessing, setIsProcessing] = useState(false);
  const [analysis, setAnalysis] = useState(null);
  const [error, setError] = useState(null);
  const [chosenSuggestionIndex, setChosenSuggestionIndex] = useState(0);

  const handleProcess = async () => {
    setError(null);
    if (!selectedItems || selectedItems.length === 0) {
      setError("Selecciona al menos un dataset antes de procesar.");
      return;
    }

    const item = selectedItems[0];
    let chosen = null;

    if (item.distribution) {
      const distArray = Array.isArray(item.distribution)
        ? item.distribution
        : [item.distribution];
      chosen = pickBestDistribution(distArray);
    }

    if (!chosen) {
      setError("No hay formatos soportados (json, csv, xml, rdf+xml, html) para este dataset.");
      return;
    }

    setIsProcessing(true);
    try {
      const res = await analyze_dataset(chosen.url, chosen.format);
      setAnalysis(res);
      setChosenSuggestionIndex(0);
    } catch (e) {
      console.error(e);
      setError(e.message || "Error al analizar dataset");
    } finally {
      setIsProcessing(false);
    }
  };

  return (
    <div style={{ padding: 12, borderRadius: 6, background: "#222", color: "#fff" }}>
      <h4 style={{ marginTop: 0 }}>Procesar dataset seleccionado</h4>
      <p style={{ color: "#ccc" }}>
        Selecciona un dataset a la izquierda y pulsa <strong>Procesar</strong> para detectar el tipo de datos y ver sugerencias.
      </p>
      {selectedItems && selectedItems.length > 0 && (
        <button
          onClick={handleProcess}
          disabled={isProcessing}
          style={{
            padding: "8px 14px",
            background: "#646cff",
            color: "white",
            border: "none",
            borderRadius: 6
          }}
        >
          {isProcessing ? "Procesando..." : "Procesar"}
        </button>
      )}

      {error && <div style={{ marginTop: 10, color: "orange" }}>{error}</div>}

      {analysis && (
        <div style={{ marginTop: 14, textAlign: "left", color: "#eee" }}>
          <h5>Resumen</h5>
          <div>Formato detectado: <b>{analysis.format_detected || "desconocido"}</b></div>
          <div>Tamaño de muestra: <b>{analysis.sample_rows_count}</b></div>

          <h5 style={{ marginTop: 12 }}>Esquema detectado</h5>
          <ul>
            {(Array.isArray(analysis.schema) ? analysis.schema : []).map(c => (
              <li key={c.name}>
                <b>{c.name}</b> — {c.inferred_type} — ejemplos: {(Array.isArray(c.sample_values) ? c.sample_values : []).join(", ")}
              </li>
            ))}
          </ul>

          <h5 style={{ marginTop: 12 }}>Sugerencias</h5>
          <div style={{ display: "flex", gap: 8, flexWrap: "wrap" }}>
            {(Array.isArray(analysis.suggestions) ? analysis.suggestions : []).map((sug, idx) => (
              <button
                key={idx}
                onClick={() => setChosenSuggestionIndex(idx)}
                style={{
                  background: idx === chosenSuggestionIndex ? "#89da5c" : "#444",
                  color: idx === chosenSuggestionIndex ? "#072" : "#fff",
                  border: "none",
                  padding: "8px 10px",
                  borderRadius: 6,
                  cursor: "pointer"
                }}
              >
                {sug.title}
              </button>
            ))}
          </div>

          <div style={{ marginTop: 18 }}>
            <h5>Vista</h5>
            <div style={{ background: "#111", padding: 8, borderRadius: 6 }}>
              <SimpleChart
                suggestion={(Array.isArray(analysis.suggestions) ? analysis.suggestions : [])[chosenSuggestionIndex]}
                sampleRows={Array.isArray(analysis.sample_rows) ? analysis.sample_rows : []}
              />
            </div>
          </div>

          <div style={{ marginTop: 16 }}>
            <h5>Muestra de filas</h5>
            <div style={{ maxHeight: 260, overflow: "auto", background: "#0c0c0c", padding: 8, borderRadius: 6 }}>
              <table style={{ width: "100%", borderCollapse: "collapse", color: "#ddd", fontSize: 13 }}>
                <thead>
                  <tr>
                    {(Array.isArray(analysis.schema) ? analysis.schema : []).map(c => (
                      <th
                        key={c.name}
                        style={{ textAlign: "left", padding: "6px 8px", borderBottom: "1px solid #222" }}
                      >
                        {c.name}
                      </th>
                    ))}
                  </tr>
                </thead>
                <tbody>
                  {(Array.isArray(analysis.sample_rows) ? analysis.sample_rows.slice(0, 40) : []).map((r, i) => (
                    <tr key={i}>
                      {(Array.isArray(analysis.schema) ? analysis.schema : []).map(c => (
                        <td
                          key={c.name}
                          style={{ padding: "4px 8px", borderBottom: "1px solid #111" }}
                        >
                          {r[c.name]}
                        </td>
                      ))}
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
