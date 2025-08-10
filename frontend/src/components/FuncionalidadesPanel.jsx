import React from "react";

function FuncionalidadesPanel({ funcionalidadSeleccionada, setFuncionalidadSeleccionada }) {
  const funcionalidades = [
    "Predicción",
    "Correlación de variables",
    "Gasto Público",
    "Ver datos en gráficos",
  ];

  const handleChange = (e) => {
    setFuncionalidadSeleccionada(e.target.value);
  };

  const handleProcesarClick = () => {
    console.log("Procesar:", funcionalidadSeleccionada);
    // Aquí iría la funcionalidad futura
  };

  return (
    <div style={{ display: "flex", flexDirection: "column", height: "100%" }}>
      <div>
        <h3
          style={{
            marginTop: 0,
            marginBottom: "1.5rem",
            fontWeight: "600",
            fontSize: "1.3rem",
          }}
        >
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
                checked={funcionalidadSeleccionada === func}
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
        disabled={!funcionalidadSeleccionada}
        style={{
          padding: "12px 0",
          backgroundColor: funcionalidadSeleccionada ? "#646cff" : "#555555",
          color: "white",
          border: "none",
          borderRadius: "6px",
          cursor: funcionalidadSeleccionada ? "pointer" : "not-allowed",
          fontWeight: "700",
          fontSize: "1.1rem",
          marginTop: "auto",
          boxShadow: funcionalidadSeleccionada
            ? "0 4px 12px rgba(100, 108, 255, 0.5)"
            : "none",
          transition: "background-color 0.3s ease, box-shadow 0.3s ease",
          userSelect: "none",
        }}
        onMouseEnter={(e) => {
          if (funcionalidadSeleccionada) e.currentTarget.style.backgroundColor = "#5058e8";
        }}
        onMouseLeave={(e) => {
          if (funcionalidadSeleccionada) e.currentTarget.style.backgroundColor = "#646cff";
        }}
      >
        Procesar
      </button>
    </div>
  );
}

export default FuncionalidadesPanel;
