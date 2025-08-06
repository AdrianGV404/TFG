import { useState } from "react";

function FuncionalidadesPanel() {
  const funcionalidades = [
    "Predicción",
    "Correlación de variables",
    "Gasto Público",
    "Exportar Datos (descargar)",
  ];

  const [funcSeleccionada, setFuncSeleccionada] = useState("");

  const handleChange = (e) => {
    setFuncSeleccionada(e.target.value);
  };

  const handleProcesarClick = () => {
    console.log("Funcionalidad seleccionada para procesar:", funcSeleccionada);
  };

  return (
    <div
      style={{
        width: "250px",
        backgroundColor: "#1a1a1a",
        padding: "24px 20px",
        color: "white",
        borderLeft: "1px solid #444",
        display: "flex",
        flexDirection: "column",
        justifyContent: "space-between",
        height: "100vh",
        boxSizing: "border-box",
        fontFamily: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif",
      }}
    >
      <div>
        <h3 style={{ marginTop: 0, marginBottom: "1.5rem", fontWeight: "600", fontSize: "1.3rem" }}>
          Funcionalidades
        </h3>
        <form>
          {funcionalidades.map((func) => (
            <label
              key={func}
              style={{
                display: "flex",
                alignItems: "center",
                marginBottom: "18px",
                cursor: "pointer",
                fontSize: "1.1rem",
                userSelect: "none",
                transition: "color 0.3s ease",
              }}
            >
              <input
                type="radio"
                name="funcionalidad"
                value={func}
                checked={funcSeleccionada === func}
                onChange={handleChange}
                style={{
                  marginRight: "12px",
                  width: "20px",
                  height: "20px",
                  cursor: "pointer",
                  transition: "box-shadow 0.3s ease",
                }}
              />
              {func}
            </label>
          ))}
        </form>
      </div>

      <button
        onClick={handleProcesarClick}
        disabled={!funcSeleccionada}
        style={{
          padding: "12px 0",
          backgroundColor: funcSeleccionada ? "#646cff" : "#555555",
          color: "white",
          border: "none",
          borderRadius: "6px",
          cursor: funcSeleccionada ? "pointer" : "not-allowed",
          fontWeight: "700",
          fontSize: "1.1rem",
          boxShadow: funcSeleccionada
            ? "0 4px 12px rgba(100, 108, 255, 0.5)"
            : "none",
          transition: "background-color 0.3s ease, box-shadow 0.3s ease",
          userSelect: "none",
        }}
        onMouseEnter={(e) => {
          if (funcSeleccionada) e.currentTarget.style.backgroundColor = "#5058e8";
        }}
        onMouseLeave={(e) => {
          if (funcSeleccionada) e.currentTarget.style.backgroundColor = "#646cff";
        }}
      >
        Procesar
      </button>
    </div>
  );
}

export default FuncionalidadesPanel;
