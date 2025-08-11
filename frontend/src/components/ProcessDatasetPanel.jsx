// src/components/ProcessDatasetPanel.jsx
import { useState } from "react";
import { analyze_dataset } from "../api/backendService";
import SimpleChart from "./DataVisualization";

export default function ProcessDatasetPanel({ selectedItems }) {
  const [isProcessing, setIsProcessing] = useState(false);
  const [analysis, setAnalysis] = useState(null);
  const [error, setError] = useState(null);
  const [chosenSuggestionIndex, setChosenSuggestionIndex] = useState(0);

  // Estado para número de filas y máximo
  const [rowsToProcess, setRowsToProcess] = useState(80); // por defecto
  const [useMaxRows, setUseMaxRows] = useState(true);

  //Popup
  const [isModalOpen, setIsModalOpen] = useState(false);

  // Elige la mejor distribución según prioridad
  const pickBestDistribution = (distributionArray) => {
    const supportedPriority = ["json", "csv", "xml", "rdf+xml", "html"];

    const normalizeFormat = (fmt) => {
      if (!fmt) return "";
      fmt = fmt.toLowerCase();
      if (fmt.includes("json")) return "json";
      if (fmt.includes("csv")) return "csv";
      if (fmt.includes("xml") && fmt.includes("rdf")) return "rdf+xml";
      if (fmt.includes("xml")) return "xml";
      if (fmt.includes("html")) return "html";
      return fmt;
    };

    const clean = distributionArray
      .map((d) => ({
        format: normalizeFormat(
          typeof d.format === "string"
            ? d.format
            : d.format?.value || d.format?._value || ""
        ),
        url:
          typeof d.accessURL === "string"
            ? d.accessURL
            : d.accessURL?.value || d.accessURL?._value || "",
      }))
      .filter((d) => d.url && supportedPriority.includes(d.format));

    clean.sort(
      (a, b) =>
        supportedPriority.indexOf(a.format) -
        supportedPriority.indexOf(b.format)
    );

    return clean.length > 0 ? clean[0] : null;
  };

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
      setError(
        "No hay formatos soportados (json, csv, xml, rdf+xml, html) para este dataset."
      );
      return;
    }

    setIsProcessing(true);
    try {
      const res = await analyze_dataset(
        chosen.url,
        chosen.format,
        useMaxRows ? -1 : rowsToProcess
      );
      setAnalysis(res);
      setChosenSuggestionIndex(0);
      setIsModalOpen(true); // Abrir modal cuando ya hay resultados
    } catch (e) {
      console.error(e);
      setError(e.message || "Error al analizar dataset");
    } finally {
      setIsProcessing(false);
    }
  };

  // Log de depuración para ver la sugerencia activa
  if (analysis && Array.isArray(analysis.suggestions)) {
    console.log(
      "Sugerencia activa:",
      analysis.suggestions[chosenSuggestionIndex]
    );
  }

  return (
    <div style={{ padding: 12, borderRadius: 6, background: "#222", color: "#fff" }}>
      <h4 style={{ marginTop: 0 }}>Procesar dataset seleccionado</h4>
      <p style={{ color: "#ccc" }}>
        Selecciona un dataset a la izquierda y pulsa <strong>Procesar</strong> para detectar el tipo de datos y ver sugerencias.
      </p>

      {selectedItems && selectedItems.length > 0 && (
        <div style={{ display: "flex", gap: "8px", alignItems: "center", flexWrap: "wrap" }}>
          {/* Input numérico */}
          <input
            type="number"
            min="1"
            value={rowsToProcess}
            onChange={(e) => setRowsToProcess(parseInt(e.target.value) || 1)}
            disabled={useMaxRows}
            style={{
              width: "80px",
              padding: "4px 8px",
              borderRadius: 4,
              border: "1px solid #555",
              backgroundColor: "#333",
              color: "#fff"
            }}
            aria-label="Número de filas a procesar"
          />
          {/* Checkbox máximo */}
          <label style={{ display: "flex", alignItems: "center", gap: "4px", fontSize: "0.9rem", color: "#ccc" }}>
            <input
              type="checkbox"
              checked={useMaxRows}
              onChange={(e) => setUseMaxRows(e.target.checked)}
              aria-label="Usar número máximo de filas"
            />
            Usar máximo
          </label>
          {/* Botón procesar */}
          <button
            onClick={handleProcess}
            disabled={isProcessing}
            style={{
              padding: "8px 14px",
              background: "#646cff",
              color: "white",
              border: "none",
              borderRadius: 6,
              cursor: "pointer"
            }}
            aria-label="Procesar dataset seleccionado"
          >
            {isProcessing ? "Procesando..." : "Procesar"}
          </button>
        </div>
      )}

      {error && <div style={{ marginTop: 10, color: "orange" }}>{error}</div>}

      {isModalOpen && analysis && (
        <div style={{
          position: "fixed",
          top: 0,
          left: 0,
          width: "100%",
          height: "100%",
          background: "rgba(0, 0, 0, 0.5)",
          display: "flex",
          alignItems: "center",
          justifyContent: "center",
          zIndex: 9999
        }}>
          <div style={{
            background: "#222",
            padding: "20px",
            borderRadius: "8px",
            maxWidth: "90%",
            maxHeight: "90%",
            overflowY: "auto",
            color: "#fff",
            position: "relative"
          }}>
            {/* Botón cerrar */}
            <button
              onClick={() => setIsModalOpen(false)}
              style={{
                position: "absolute",
                top: 10,
                right: 10,
                background: "transparent",
                border: "none",
                fontSize: "1.6rem",
                color: "#fff",
                cursor: "pointer"
              }}
              title="Cerrar"
            >
              &times;
            </button>

            {/* Aquí va tu resultado */}
            <h4>Resumen</h4>
            <div>Formato detectado: <b>{analysis.format_detected || "desconocido"}</b></div>
            <div>Tamaño de muestra: <b>{analysis.sample_rows_count}</b></div>

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
          </div>
        </div>
      )}

    </div>
  );
}
