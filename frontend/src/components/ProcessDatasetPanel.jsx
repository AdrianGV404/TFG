import { useState } from "react";
import { analyze_dataset } from "../api/backendService";
import SimpleChart from "./DataVisualization";

export default function ProcessDatasetPanel({ selectedItems }) {
  const [isProcessing, setIsProcessing] = useState(false);
  const [analysis, setAnalysis] = useState(null);
  const [error, setError] = useState(null);
  const [chosenSuggestionIndex, setChosenSuggestionIndex] = useState(0);

  const [rowsToProcess, setRowsToProcess] = useState(80);
  const [useMaxRows, setUseMaxRows] = useState(true);

  const [isModalOpen, setIsModalOpen] = useState(false);

  // Nuevo estado para tipo de gráfico
  const [chartType, setChartType] = useState("line"); // valores posibles: "line", "bar", "stackedBar", "horizontalBar", "pie"

  const pickBestDistribution = (distributionArray) => {
    const supportedPriority = ["json", "csv", "xml", "rdf+xml", "html", "pc-axis"];
    const normalizeFormat = (fmt) => {
      if (!fmt) return "";
      fmt = fmt.toLowerCase();
      if (fmt.includes("json")) return "json";
      if (fmt.includes("csv")) return "csv";
      if (fmt.includes("xml") && fmt.includes("rdf")) return "rdf+xml";
      if (fmt.includes("xml")) return "xml";
      if (fmt.includes("html")) return "html";
      if (fmt.includes("pc-axis") || fmt === "px") return "pc-axis";
      return fmt;
    };
    const mapped = distributionArray.map((d) => {
      const url =
        typeof d.accessURL === "string"
          ? d.accessURL
          : d.accessURL?.value || d.accessURL?._value || "";
      const fmt =
        typeof d.format === "string"
          ? d.format
          : d.format?.value || d.format?._value || "";
      return { format: normalizeFormat(fmt), url };
    });
    const ineDist = mapped.find((d) => d.url && d.url.toLowerCase().includes("ine.es"));
    if (ineDist) return ineDist;
    const clean = mapped.filter((d) => d.url && supportedPriority.includes(d.format));
    clean.sort((a, b) => supportedPriority.indexOf(a.format) - supportedPriority.indexOf(b.format));
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
        "No hay formatos soportados (json, csv, xml, rdf+xml, html, pc-axis) para este dataset."
      );
      return;
    }
    setIsProcessing(true);
    try {
      const isIne = chosen.url.toLowerCase().includes("ine.es");
      const res = await analyze_dataset(
        chosen.url,
        isIne ? "" : chosen.format,
        useMaxRows ? -1 : rowsToProcess
      );
      if (res.success && res.suggestion && !res.suggestions) {
        res.suggestions = [res.suggestion];
      }
      setAnalysis(res);
      setChosenSuggestionIndex(0);
      // Reset tipo de gráfico al analizar nuevo dataset (opcional)
      setChartType("line");
      setIsModalOpen(true);
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
        Cantidad de filas a <strong>Procesar</strong> del conjunto de datos.
      </p>

      {selectedItems && selectedItems.length > 0 && (
        <div style={{ display: "flex", gap: "8px", alignItems: "center", flexWrap: "wrap" }}>
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
          <label style={{ display: "flex", alignItems: "center", gap: "4px", fontSize: "0.9rem", color: "#ccc" }}>
            <input
              type="checkbox"
              checked={useMaxRows}
              onChange={(e) => setUseMaxRows(e.target.checked)}
              aria-label="Usar número máximo de filas"
            />
            Usar máximo
          </label>
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
            width: "95%",
            height: "95%",
            overflow: "auto",
            color: "#fff",
            position: "relative"
          }}>
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

            {/* Botones para seleccionar tipo de gráfico */}
            <div style={{ marginTop: 12, display: "flex", gap: "8px", flexWrap: "wrap" }}>
              {["line", "bar", "stackedBar", "horizontalBar", "pie"].map((type) => (
                <button
                  key={type}
                  onClick={() => setChartType(type)}
                  style={{
                    padding: "6px 12px",
                    borderRadius: 6,
                    border: chartType === type ? "2px solid #89da5c" : "2px solid transparent",
                    backgroundColor: chartType === type ? "#b3f59c" : "#444",
                    color: chartType === type ? "#072" : "#fff",
                    cursor: "pointer"
                  }}
                >
                  {type === "line" ? "Línea" :
                   type === "bar" ? "Barra" :
                   type === "stackedBar" ? "Barra Apilada" :
                   type === "horizontalBar" ? "Barra Horizontal" :
                   type === "pie" ? "Pie Chart" : type}
                </button>
              ))}
            </div>

            <div style={{ marginTop: 18 }}>
              <h5>Vista</h5>
              <div style={{ background: "#111", padding: 8, borderRadius: 6, minHeight: 500 }}>
                <SimpleChart
                  suggestion={(Array.isArray(analysis.suggestions) ? analysis.suggestions : [])[chosenSuggestionIndex]}
                  sampleRows={Array.isArray(analysis.sample_rows) ? analysis.sample_rows : []}
                  labels={analysis.labels || []}
                  series={analysis.series || []}
                  chartType={chartType}
                />
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
