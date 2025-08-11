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
        </h3>
        <form
          style={{
            display: "flex",
            flexDirection: "row", // ← en fila
            gap: "28px",          // ← separación horizontal entre opciones
            marginBottom: "1.2rem"
          }}
        >
          {funcionalidades.map((func) => (
            <label
              key={func}
              style={{
                display: "flex",
                alignItems: "center",
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
                  marginRight: "8px",  // separación entre el círculo y el texto
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
    </div>
  );
}

export default FuncionalidadesPanel;
